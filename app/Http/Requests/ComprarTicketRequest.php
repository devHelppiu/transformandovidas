<?php

namespace App\Http\Requests;

use App\Models\Combo;
use App\Models\Sorteo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ComprarTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'codigo_referido' => ['nullable', 'string', 'max:10'],
            'combo_id' => ['nullable', 'integer', 'exists:combos,id'],
            'cantidad' => ['required', 'integer', 'min:1', 'max:100'],
            'numeros' => ['nullable', 'array', 'max:100'],
            'numeros.*' => ['string', 'size:4', 'regex:/^\d{4}$/'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $sorteo = $this->route('sorteo');

                if (! $sorteo || $sorteo->estado !== 'activo') {
                    $validator->errors()->add('sorteo', 'Este sorteo no está activo.');
                    return;
                }

                if ($sorteo->ventasCerradas()) {
                    $validator->errors()->add('sorteo', 'Las ventas de este sorteo han cerrado.');
                    return;
                }

                $disponibles = $sorteo->numerosDisponibles();

                if (empty($disponibles)) {
                    $validator->errors()->add('sorteo', 'No hay números disponibles en este sorteo.');
                    return;
                }

                if ($this->filled('combo_id')) {
                    $combo = Combo::find($this->combo_id);
                    if (! $combo || $combo->sorteo_id !== $sorteo->id || ! $combo->activo) {
                        $validator->errors()->add('combo_id', 'El combo seleccionado no es válido para este sorteo.');
                        return;
                    }
                    if ($combo->cantidad < $sorteo->compra_minima) {
                        $validator->errors()->add('combo_id', "La compra mínima es de {$sorteo->compra_minima} tickets.");
                        return;
                    }
                    if (count($disponibles) < $combo->cantidad) {
                        $validator->errors()->add('combo_id', 'No hay suficientes números disponibles para este combo.');
                    }
                } else {
                    $cantidad = (int) $this->cantidad;
                    if ($cantidad < $sorteo->compra_minima) {
                        $validator->errors()->add('cantidad', "La compra mínima es de {$sorteo->compra_minima} tickets.");
                        return;
                    }
                    if (count($disponibles) < $cantidad) {
                        $validator->errors()->add('cantidad', 'No hay suficientes números disponibles.');
                    }
                }

                // Validar números seleccionados manualmente
                if ($this->filled('numeros') && is_array($this->numeros)) {
                    $numerosElegidos = $this->numeros;
                    $cantidadReal = $this->filled('combo_id') 
                        ? Combo::find($this->combo_id)?->cantidad ?? count($numerosElegidos)
                        : (int) $this->cantidad;

                    // Verificar que la cantidad de números coincide
                    if (count($numerosElegidos) !== $cantidadReal) {
                        $validator->errors()->add('numeros', "Debes seleccionar exactamente {$cantidadReal} números.");
                        return;
                    }

                    // Verificar que los números están dentro del rango del sorteo
                    $maxNumero = str_pad($sorteo->total_tickets - 1, 4, '0', STR_PAD_LEFT);
                    foreach ($numerosElegidos as $numero) {
                        if ($numero > $maxNumero) {
                            $validator->errors()->add('numeros', "El número {$numero} está fuera del rango del sorteo.");
                            return;
                        }
                    }

                    // Verificar que los números están disponibles
                    $ocupados = array_diff($numerosElegidos, $disponibles);
                    if (count($ocupados) > 0) {
                        $validator->errors()->add('numeros', 'Los números ' . implode(', ', $ocupados) . ' ya no están disponibles.');
                    }
                }
            },
        ];
    }
}
