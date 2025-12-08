<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRestauranteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->rol === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255|min:3',
            'descripcion' => 'nullable|string|max:1000',
            'direccion' => 'required|string|max:255|min:5',
            'user_id' => 'required|integer|exists:users,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del restaurante es requerido',
            'nombre.min' => 'El nombre debe tener mínimo 3 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',
            'direccion.required' => 'La dirección es requerida',
            'direccion.min' => 'La dirección debe tener mínimo 5 caracteres',
            'direccion.max' => 'La dirección no puede exceder 255 caracteres',
            'user_id.required' => 'El usuario es requerido',
            'user_id.exists' => 'El usuario seleccionado no existe',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function attributes(): array
    {
        return [
            'nombre' => 'nombre del restaurante',
            'descripcion' => 'descripción',
            'direccion' => 'dirección',
            'user_id' => 'usuario propietario',
        ];
    }
}
