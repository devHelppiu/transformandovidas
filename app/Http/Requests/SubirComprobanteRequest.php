<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubirComprobanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comprobante' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'referencia_pago' => ['nullable', 'string', 'max:255'],
        ];
    }
}
