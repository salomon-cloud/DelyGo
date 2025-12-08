<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
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
            'puntuacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:500|min:3',
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
            'puntuacion.required' => 'La puntuación es requerida',
            'puntuacion.min' => 'La puntuación debe ser mínimo 1 estrella',
            'puntuacion.max' => 'La puntuación debe ser máximo 5 estrellas',
            'comentario.max' => 'El comentario no puede exceder 500 caracteres',
            'comentario.min' => 'El comentario debe tener mínimo 3 caracteres',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function attributes(): array
    {
        return [
            'orden_id' => 'orden',
            'puntuacion' => 'calificación',
            'comentario' => 'comentario',
        ];
    }
}
