<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entidad extends Model
{
    // Nombre completo de la base de datos y tabla
    protected $table = 'TiCentral.Entidades';

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'Id',
        'Nombre',
        'Direccion',
        'Telefono',
        'IdTipoEntidad',
        'IdTitular',
        'PerteneceA',
        'ControlInterno',
        'ClaveSiregob',
        'FechaCreacion',
        'CreadoPor',
        'UltimaActualizacion',
        'ModificadoPor',
        'Deleted',
    ];

    protected $casts = [
        'Id' => 'string',
        'FechaCreacion' => 'datetime',
        'UltimaActualizacion' => 'datetime',
        'Deleted' => 'boolean',
    ];

    /**
     * Relación con la Entidad Padre.
     */
    public function perteneceA()
    {
        return $this->belongsTo(Entidad::class, 'PerteneceA', 'Id');
    }
}
