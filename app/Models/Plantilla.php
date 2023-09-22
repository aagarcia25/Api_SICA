<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Plantilla
 *
 * @property string $id
 * @property string $referencia
 * @property string $body
 * @property string $CreadoPor
 * @property string $ModificadoPor
 * @property Carbon $UltimaActualizacion
 * @property Carbon $FechaCreacion
 * @property string $deleted
 *
 * @package App\Models
 */
class Plantilla extends Model
{
    public $table = 'Plantillas';
    public $incrementing = false;
    public $timestamps = false;

    public $casts = [
        'UltimaActualizacion' => 'datetime',
        'FechaCreacion' => 'datetime',
    ];

    public $fillable = [
        'referencia',
        'body',
        'CreadoPor',
        'ModificadoPor',
        'UltimaActualizacion',
        'FechaCreacion',
        'deleted',
    ];
}
