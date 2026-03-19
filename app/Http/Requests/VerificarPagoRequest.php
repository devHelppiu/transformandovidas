<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerificarPagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'accion' => ['required', 'in:verificar,rechazar'],
            'nota_rechazo' => ['required_if:accion,rechazar', 'nullable', 'string', 'max:500'],
        ];
    }
}
