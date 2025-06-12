<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Albaran extends Model
{
    use HasFactory;

    // AÑADIDO: Especificar explícitamente el nombre de la tabla
    protected $table = 'albaranes';

    protected $fillable = [
        'cliente_id',
        'codigo_albaran',
        'fecha_envio',
        'nombre_paciente',
        'descuento',
        'total_productos',
        'total_albaran',
        'factura_id',
    ];

    protected $dates = ['fecha_envio']; // Para que Laravel lo maneje como objeto Carbon

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function detalleAlbaranes()
    {
        return $this->hasMany(DetalleAlbaran::class);
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }
}