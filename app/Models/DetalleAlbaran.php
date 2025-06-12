<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleAlbaran extends Model
{
    use HasFactory;

    // AÑADIDO: Especificar explícitamente el nombre de la tabla
    protected $table = 'detalle_albaranes';

    protected $fillable = [
        'albaran_id',
        'producto_id',
        'nombre_producto',
        'unidades',
        'precio_unitario',
        'importe',
    ];

    public function albaran()
    {
        return $this->belongsTo(Albaran::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}