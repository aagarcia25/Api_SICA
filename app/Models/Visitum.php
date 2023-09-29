<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Visitum
 *
 * @property string $id
 * @property string $deleted
 * @property Carbon $UltimaActualizacion
 * @property Carbon $FechaCreacion
 * @property string $ModificadoPor
 * @property string $CreadoPor
 * @property Carbon $FechaVisita
 * @property Carbon|null $FechaEntrada
 * @property Carbon|null $FechaSalida
 * @property int $Duracion
 * @property string $IdTipoAcceso
 * @property string|null $Proveedor
 * @property string|null $NombreVisitante
 * @property string|null $ApellidoPVisitante
 * @property string|null $ApellidoMVisitante
 * @property string|null $idTipoentidad
 * @property string|null $idEntidad
 * @property string $NombreReceptor
 * @property string $ApellidoPReceptor
 * @property string $ApellidoMReceptor
 * @property string $IdEntidadReceptor
 * @property string|null $PisoReceptor
 * @property string $IdEstatus
 * @property int|null $Finalizado
 * @property string|null $EmailNotificacion
 * @property string|null $IdEdificio
 * @property string|null $IdAcceso
 *
 * @property CatTipoacceso $cat_tipoacceso
 * @property CatEstatus $cat_estatus
 * @property CatEdificio|null $cat_edificio
 * @property CatEntradasEdi|null $cat_entradas_edi
 * @property CatPiso|null $cat_piso
 *
 * @package App\Models
 */
class Visitum extends Model
{
    public $table = 'visita';
    public $incrementing = false;
    public $timestamps = false;
    public $keyType = 'string';
    public $primaryKey = 'id';

    public $casts = [
        'UltimaActualizacion' => 'datetime',
        'FechaCreacion' => 'datetime',
        'FechaVisita' => 'datetime',
        'FechaEntrada' => 'datetime',
        'FechaSalida' => 'datetime',
        'Duracion' => 'int',
        'Finalizado' => 'int',
    ];

    public $fillable = [
        'deleted',
        'UltimaActualizacion',
        'FechaCreacion',
        'ModificadoPor',
        'CreadoPor',
        'FechaVisita',
        'FechaEntrada',
        'FechaSalida',
        'Duracion',
        'IdTipoAcceso',
        'Proveedor',
        'NombreVisitante',
        'ApellidoPVisitante',
        'ApellidoMVisitante',
        'idTipoentidad',
        'idEntidad',
        'NombreReceptor',
        'ApellidoPReceptor',
        'ApellidoMReceptor',
        'IdEntidadReceptor',
        'PisoReceptor',
        'IdEstatus',
        'Finalizado',
        'EmailNotificacion',
        'IdEdificio',
        'IdAcceso',
    ];

    public function cat_tipoacceso()
    {
        return $this->belongsTo(CatTipoacceso::class, 'IdTipoAcceso');
    }

    public function cat_estatus()
    {
        return $this->belongsTo(CatEstatus::class, 'IdEstatus');
    }

    public function cat_edificio()
    {
        return $this->belongsTo(CatEdificio::class, 'IdEdificio');
    }

    public function cat_entradas_edi()
    {
        return $this->belongsTo(CatEntradasEdi::class, 'IdAcceso');
    }

    public function cat_piso()
    {
        return $this->belongsTo(CatPiso::class, 'PisoReceptor');
    }
}
