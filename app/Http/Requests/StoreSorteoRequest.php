<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSorteoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'fecha_sorteo' => ['required', 'date', 'after:today'],
            'fecha_cierre_ventas' => ['required', 'date', 'after_or_equal:today', function ($attribute, $value, $fail) {
                if ($this->fecha_sorteo && \Carbon\Carbon::parse($value)->startOfDay()->gt(\Carbon\Carbon::parse($this->fecha_sorteo)->startOfDay())) {
                    $fail('La fecha de cierre de ventas no puede ser posterior a la fecha del sorteo.');
                }
            }],
            'total_tickets' => ['required', 'integer', 'min:1', 'max:10000'],
            'precio_ticket' => ['required', 'numeric', 'min:0.01'],
            'valor_premio' => ['nullable', 'numeric', 'min:0'],
            'compra_minima' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
