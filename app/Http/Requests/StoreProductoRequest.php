<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255|min:3',
            'descripcion' => 'nullable|string|max:1000',
            'precio' => 'required|numeric|min:0.01|max:99999.99',
            'restaurante_id' => 'required|integer|exists:restaurantes,id',
            'disponible' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es requerido',
            'nombre.min' => 'El nombre debe tener mínimo 3 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'precio.required' => 'El precio es requerido',
            'precio.min' => 'El precio debe ser mayor a 0',
            'precio.max' => 'El precio no puede ser mayor a 99,999.99',
            'restaurante_id.required' => 'El restaurante es requerido',
            'restaurante_id.exists' => 'El restaurante no existe',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function attributes(): array
    {
        return [
            'nombre' => 'nombre del producto',
            'descripcion' => 'descripción',
            'precio' => 'precio',
            'restaurante_id' => 'restaurante',
            'disponible' => 'disponibilidad',
        ];
    }
}
