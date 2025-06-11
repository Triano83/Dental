<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'numero_factura',
        'fecha_factura',
        'total_a_pagar',
    ];

    protected $dates = ['fecha_factura']; // Para que Laravel lo maneje como objeto Carbon

    // Una factura pertenece a un cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Una factura tiene muchos albaranes
    public function albaranes()
    {
        return $this->hasMany(Albaran::class);
    }
}