<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HelppiuPayService
{
    private string $baseUrl;
    private string $authToken;

    public function __construct()
    {
        $keyId = config('services.helppiu.key_id');
        $secret = config('services.helppiu.secret');
        $this->baseUrl = config('services.helppiu.base_url');
        $this->authToken = "{$keyId}:{$secret}";
    }

    /**
     * Create a checkout session and return the response (id, url, status, etc.).
     */
    public function createCheckoutSession(array $params): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->authToken}",
            'Content-Type' => 'application/json',
            'Idempotency-Key' => $params['idempotency_key'] ?? (string) \Illuminate\Support\Str::uuid(),
        ])->post("{$this->baseUrl}/checkout-sessions", [
            'reference' => $params['reference'],
            'amount' => $params['amount'],
            'description' => $params['description'] ?? '',
            'currency' => 'COP',
            'success_url' => $params['success_url'],
            'cancel_url' => $params['cancel_url'],
            'customer_email' => $params['customer_email'] ?? null,
            'customer_name' => $params['customer_name'] ?? null,
            'metadata' => $params['metadata'] ?? [],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'Helppiu Pay error: ' . ($response->json('message') ?? $response->body())
            );
        }

        return $response->json();
    }

    /**
     * Get a checkout session status.
     */
    public function getCheckoutSession(string $sessionId): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->authToken}",
        ])->get("{$this->baseUrl}/checkout-sessions/{$sessionId}");

        return $response->json();
    }

    /**
     * Verify a webhook signature (HMAC-SHA256) with optional timestamp validation.
     * FIX 53: Replay attack protection - rejects events older than 5 minutes.
     */
    public static function verifyWebhookSignature(string $payload, string $signature, ?string $timestamp = null): bool
    {
        $secret = config('services.helppiu.webhook_secret');

        if (empty($secret)) {
            return false;
        }

        // Reject events older than 5 minutes (replay attack protection)
        if ($timestamp !== null) {
            $ts = (int) $timestamp;
            if ($ts === 0 || abs(time() - $ts) > 300) {
                return false;
            }
        }

        // Sign payload + timestamp if present (standard format: timestamp.payload)
        $signedString = $timestamp ? "{$timestamp}.{$payload}" : $payload;
        $expected = hash_hmac('sha256', $signedString, $secret);

        return hash_equals($expected, $signature);
    }

    /**
     * Register a webhook endpoint via API.
     */
    public function registerWebhook(string $url, array $events = ['*']): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->authToken}",
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/webhook-endpoints", [
            'url' => $url,
            'events' => $events,
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'Helppiu Pay webhook registration error: ' . ($response->json('message') ?? $response->body())
            );
        }

        return $response->json();
    }
}
