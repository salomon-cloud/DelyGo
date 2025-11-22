<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'restaurante_id',
        'nombre',
        'descripcion',
        'precio',
        'disponible',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'precio' => 'decimal:2',
        'disponible' => 'boolean',
    ];

    /**
     * Relación con el restaurante al que pertenece el producto.
     */
    public function restaurante(): BelongsTo
    {
        return $this->belongsTo(Restaurante::class);
    }
}
