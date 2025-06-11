<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleAlbaran extends Model
{
    use HasFactory;

     // AÑADE ESTA LÍNEA para especificar el nombre de la tabla
    protected $table = 'detalle_albaranes'; // Asegúrate de que este sea el nombre real en tu DB


    protected $fillable = [
        'albaran_id',
        'producto_id',
        'nombre_producto',
        'unidades',
        'precio_unitario',
        'importe',
    ];

    // Un detalle de albarán pertenece a un albarán
    public function albaran()
    {
        return $this->belongsTo(Albaran::class);
    }

    // Un detalle de albarán puede pertenecer a un producto (para obtener datos del producto)
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}