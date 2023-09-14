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
 * @property Carbon $Hora
 * @property int $Duracion
 * @property string $IdTipoAcceso
 * @property string|null $Proveedor
 * @property string|null $NombreVisitante
 * @property string|null $ApellidoPVisitante
 * @property string|null $ApellidoMVisitante
 * @property string|null $idTipoentidad
 * @property string|null $idEntidad
 * @property string|null $NombreReceptor
 * @property string|null $ApellidoPReceptor
 * @property string|null $ApellidoMReceptor
 * @property string|null $idEntidadReceptor
 * @property string|null $PisoReceptor
 * @property string $idEstatus
 *
 * @property CatTipoAcceso $cat_tipo_acceso
 *
 * @package App\Models
 */
class Visitum extends Model
{
    public $table = 'Visita';
    public $incrementing = false;
    public $timestamps = false;
    public $keyType = 'string';
    public $primaryKey = 'id';

    protected $_casts = [
        'UltimaActualizacion' => 'datetime',
        'FechaCreacion' => 'datetime',
        'FechaVisita' => 'datetime',
        'Duracion' => 'int',
    ];

    protected $_fillable = [
        'deleted',
        'UltimaActualizacion',
        'FechaCreacion',
        'ModificadoPor',
        'CreadoPor',
        'FechaVisita',
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
        'idEntidadReceptor',
        'PisoReceptor',
        'idEstatus',
    ];

    public function cat_tipo_acceso()
    {
        return $this->belongsTo(CatTipoAcceso::class, 'IdTipoAcceso');
    }
}
