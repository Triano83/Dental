<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_clinica',
        'direccion',
        'codigo_postal',
        'poblacion',
        'ciudad',
        'telefono',
        'nif',
    ];

    // Un cliente puede tener muchos albaranes
    public function albaranes()
    {
        return $this->hasMany(Albaran::class);
    }

    // Un cliente puede tener muchas facturas
    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }
}