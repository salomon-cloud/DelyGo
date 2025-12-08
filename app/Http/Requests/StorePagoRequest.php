<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePagoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->rol === 'cliente';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'orden_id' => 'required|integer|exists:ordenes,id',
            'metodo_pago' => 'required|string|in:tarjeta,transferencia,efectivo',
            'monto' => 'required|numeric|min:0.01|max:99999.99',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'orden_id.required' => 'La orden es requerida',
            'orden_id.exists' => 'La orden no existe',
            'metodo_pago.required' => 'Debe seleccionar un método de pago',
            'metodo_pago.in' => 'El método de pago seleccionado no es válido',
            'monto.required' => 'El monto es requerido',
            'monto.min' => 'El monto debe ser mayor a 0',
            'monto.max' => 'El monto no puede ser mayor a 99,999.99',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function attributes(): array
    {
        return [
            'orden_id' => 'orden',
            'metodo_pago' => 'método de pago',
            'monto' => 'monto',
        ];
    }
}
