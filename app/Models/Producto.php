<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'precio',
    ];

    // Un producto puede estar en muchos detalles de albarán (aunque no directamente)
    public function detalleAlbaranes()
    {
        return $this->hasMany(DetalleAlbaran::class);
    }
}