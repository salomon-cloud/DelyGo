<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rating extends Model
{
    use HasFactory;
    protected $fillable = [
        'orden_id',
        'cliente_id',
        'repartidor_id',
        'puntuacion',
        'comentario',
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function repartidor()
    {
        return $this->belongsTo(User::class, 'repartidor_id');
    }
}
