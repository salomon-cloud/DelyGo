<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrdenRequest extends FormRequest
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
            'restaurante_id' => 'required|integer|exists:restaurantes,id',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|integer|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1|max:100',
            'direccion_entrega' => 'required|string|max:255|min:5',
            'total' => 'required|numeric|min:0.01|max:99999.99',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'restaurante_id.required' => 'Debe seleccionar un restaurante',
            'restaurante_id.exists' => 'El restaurante seleccionado no existe',
            'productos.required' => 'Debe seleccionar al menos un producto',
            'productos.min' => 'Debe seleccionar al menos un producto',
            'productos.*.id.required' => 'ID de producto requerido',
            'productos.*.id.exists' => 'Uno de los productos no existe',
            'productos.*.cantidad.required' => 'La cantidad es requerida',
            'productos.*.cantidad.min' => 'La cantidad debe ser mínimo 1',
            'productos.*.cantidad.max' => 'La cantidad no puede exceder 100',
            'direccion_entrega.required' => 'La dirección de entrega es requerida',
            'direccion_entrega.min' => 'La dirección debe tener mínimo 5 caracteres',
            'total.required' => 'El total es requerido',
            'total.min' => 'El total debe ser mayor a 0',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function attributes(): array
    {
        return [
            'restaurante_id' => 'restaurante',
            'productos' => 'productos',
            'direccion_entrega' => 'dirección',
            'total' => 'total',
        ];
    }
}
