<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSorteoRequest extends FormRequest
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
            'fecha_sorteo' => ['required', 'date'],
            'fecha_cierre_ventas' => ['required', 'date', 'before_or_equal:fecha_sorteo'],
            'total_tickets' => ['required', 'integer', 'min:1', 'max:10000'],
            'precio_ticket' => ['required', 'numeric', 'min:0.01'],
            'valor_premio' => ['nullable', 'numeric', 'min:0'],
            'compra_minima' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
