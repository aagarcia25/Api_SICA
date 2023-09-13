<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CatEstatus
 *
 * @property string $id
 * @property string $deleted
 * @property Carbon $UltimaActualizacion
 * @property Carbon $FechaCreacion
 * @property string $ModificadoPor
 * @property string $CreadoPor
 * @property string $Descripcion
 *
 * @package App\Models
 */
class CatEstatus extends Model
{
    public $table = 'Cat_Estatus';
    public $keyType = 'string';
    public $primaryKey = 'id';

    public $incrementing = false;
    public $timestamps = false;

    protected $_casts = [
        'deleted' => 'binary',
        'UltimaActualizacion' => 'datetime',
        'FechaCreacion' => 'datetime',
    ];

    protected $_fillable = [
        'deleted',
        'UltimaActualizacion',
        'FechaCreacion',
        'ModificadoPor',
        'CreadoPor',
        'Descripcion',
    ];
}
