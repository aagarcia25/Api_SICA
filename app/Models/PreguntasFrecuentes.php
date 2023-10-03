<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PreguntasFrecuentes
 *
 * @property string $id
 * @property string $deleted
 * @property Carbon $UltimaActualizacion
 * @property Carbon $FechaCreacion
 * @property string $ModificadoPor
 * @property string $CreadoPor
 * @property string|null $idMenu
 * @property string|null $Pregunta
 * @property string|null $Texto
 * @property string|null $RutaGuia
 * @property string|null $RutaVideo
 * @property string|null $NombreOriginalVideo
 * @property string $Departamento
 *
 * @package App\Models
 */
class PreguntasFrecuentes extends Model
{
    public $table = 'PreguntasFrecuentes';
    public $incrementing = false;
    public $timestamps = false;

    protected $_casts = [
        'UltimaActualizacion' => 'datetime',
        'FechaCreacion' => 'datetime',
    ];

    protected $_fillable = [
        'deleted',
        'UltimaActualizacion',
        'FechaCreacion',
        'ModificadoPor',
        'CreadoPor',
        'idMenu',
        'Pregunta',
        'Texto',
        'RutaGuia',
        'RutaVideo',
        'NombreOriginalVideo',
    ];
}
