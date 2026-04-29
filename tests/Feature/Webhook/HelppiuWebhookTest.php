<?php

namespace Tests\Feature\Webhook;

use App\Models\Pago;
use App\Models\Sorteo;
use App\Models\Ticket;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Services\HelppiuPayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * FIX 59: Tests críticos para webhook de Helppiu
 */
class HelppiuWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function generateSignature(string $payload, ?string $timestamp = null): string
    {
        $secret = config('services.helppiu.webhook_secret');
        $signedString = $timestamp ? "{$timestamp}.{$payload}" : $payload;
        return hash_hmac('sha256', $signedString, $secret);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        config(['services.helppiu.webhook_secret' => 'test-secret']);

        $payload = json_encode(['event' => 'transaction.approved', 'data' => []]);

        $response = $this->postJson('/webhooks/helppiu', json_decode($payload, true), [
            'X-Helppiu-Signature' => 'invalid-signature',
        ]);

        $response->assertStatus(401);
    }

    public function test_webhook_rejects_missing_secret_configuration(): void
    {
        config(['services.helppiu.webhook_secret' => '']);

        $response = $this->postJson('/webhooks/helppiu', [
            'event' => 'transaction.approved',
            'data' => [],
        ]);

        $response->assertStatus(503);
    }

    public function test_webhook_accepts_valid_signature(): void
    {
        config(['services.helppiu.webhook_secret' => 'test-secret']);

        $payload = json_encode(['event' => 'transaction.pending', 'data' => ['reference' => 'TV-test123']]);
        $signature = $this->generateSignature($payload);

        $response = $this->call(
            'POST',
            '/webhooks/helppiu',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_X_HELPPIU_SIGNATURE' => $signature],
            $payload
        );

        $response->assertStatus(200);
    }

    public function test_webhook_rejects_replay_attack_with_old_timestamp(): void
    {
        config(['services.helppiu.webhook_secret' => 'test-secret']);

        // Timestamp de hace 10 minutos (más de 5 min permitido)
        $oldTimestamp = (string) (time() - 600);
        $payload = json_encode(['event' => 'transaction.approved', 'data' => []]);
        $signature = $this->generateSignature($payload, $oldTimestamp);

        $response = $this->call(
            'POST',
            '/webhooks/helppiu',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_HELPPIU_SIGNATURE' => $signature,
                'HTTP_X_HELPPIU_TIMESTAMP' => $oldTimestamp,
            ],
            $payload
        );

        $response->assertStatus(401);
    }

    public function test_webhook_idempotency_prevents_duplicate_processing(): void
    {
        config(['services.helppiu.webhook_secret' => 'test-secret']);

        $transactionId = 'tx-unique-123';
        $payload = json_encode([
            'event' => 'transaction.pending',
            'data' => [
                'transaction_id' => $transactionId,
                'reference' => 'TV-test456',
            ],
        ]);
        $signature = $this->generateSignature($payload);

        // First request - should process
        $response1 = $this->call(
            'POST',
            '/webhooks/helppiu',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_X_HELPPIU_SIGNATURE' => $signature],
            $payload
        );
        $response1->assertStatus(200);

        // Verify webhook event was created and marked as processed
        $this->assertDatabaseHas('webhook_events', [
            'transaction_id' => $transactionId,
        ]);
        $webhookEvent = WebhookEvent::where('transaction_id', $transactionId)->first();
        $this->assertNotNull($webhookEvent->processed_at);

        // Second request with same transaction_id - should be ignored
        $response2 = $this->call(
            'POST',
            '/webhooks/helppiu',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_X_HELPPIU_SIGNATURE' => $signature],
            $payload
        );
        $response2->assertStatus(200);
        $response2->assertJson(['status' => 'already_processed']);
    }

    public function test_webhook_aprueba_pago_y_actualiza_ticket(): void
    {
        config(['services.helppiu.webhook_secret' => 'test-secret']);

        $sorteo = \App\Models\Sorteo::factory()->create(['estado' => 'activo']);
        $ticket = \App\Models\Ticket::factory()->create([
            'sorteo_id' => $sorteo->id,
            'estado' => 'reservado',
            'grupo_compra' => 'abc-test',
        ]);
        $pago = \App\Models\Pago::factory()->create([
            'ticket_id' => $ticket->id,
            'estado' => 'pendiente',
        ]);

        $payload = json_encode([
            'event' => 'transaction.approved',
            'data' => [
                'reference' => 'TV-abc-test',
                'transaction_id' => 'TX-100',
                'payment_method' => 'pse',
            ],
        ]);
        $signature = $this->generateSignature($payload);

        $response = $this->call('POST', '/webhooks/helppiu', [], [], [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_X_HELPPIU_SIGNATURE' => $signature],
            $payload
        );

        $response->assertOk();
        $this->assertDatabaseHas('pagos', ['id' => $pago->id, 'estado' => 'verificado']);
        $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'estado' => 'pagado']);
    }

    public function test_webhook_idempotente_no_procesa_duplicados(): void
    {
        config(['services.helppiu.webhook_secret' => 'test-secret']);

        \App\Models\WebhookEvent::create([
            'source' => 'helppiu',
            'event_type' => 'transaction.approved',
            'transaction_id' => 'TX-DUP-1',
            'payload' => [],
            'processed_at' => now(),
        ]);

        $payload = json_encode([
            'event' => 'transaction.approved',
            'data' => ['reference' => 'TV-x', 'transaction_id' => 'TX-DUP-1'],
        ]);
        $signature = $this->generateSignature($payload);

        $response = $this->call('POST', '/webhooks/helppiu', [], [], [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_X_HELPPIU_SIGNATURE' => $signature],
            $payload
        );

        $response->assertOk();
        $response->assertJson(['status' => 'already_processed']);
    }
}
