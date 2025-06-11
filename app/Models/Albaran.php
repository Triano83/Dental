<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Albaran extends Model
{
    use HasFactory;

     // AÑADE ESTA LÍNEA para especificar el nombre de la tabla
    protected $table = 'albaranes'; // Asegúrate de que este sea el nombre real en tu DB


    protected $fillable = [
        'cliente_id',
        'codigo_albaran',
        'fecha_envio',
        'nombre_paciente',
        'descuento',
        'total_productos', // Campo para la suma antes del descuento
        'total_albaran',   // Campo para el total después del descuento
        'factura_id',
    ];

    protected $dates = ['fecha_envio']; // Para que Laravel lo maneje como objeto Carbon

    // Un albarán pertenece a un cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Un albarán tiene muchos detalles de albarán
    public function detalleAlbaranes()
    {
        return $this->hasMany(DetalleAlbaran::class);
    }

    // Un albarán puede pertenecer a una factura (puede ser null)
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }
}