<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CatEntradasEdi
 *
 * @property string $id
 * @property string $deleted
 * @property Carbon $UltimaActualizacion
 * @property Carbon $FechaCreacion
 * @property string $ModificadoPor
 * @property string $CreadoPor
 * @property string $Descripcion
 * @property string $idEdificio
 *
 * @property CatEdificio $cat_edificio
 *
 * @package App\Models
 */
class CatEntradasEdi extends Model
{
    public $table = 'Cat_Entradas_Edi';
    public $keyType = 'string';
    public $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    public $casts = [
        'UltimaActualizacion' => 'datetime',
        'FechaCreacion' => 'datetime',
    ];

    public $fillable = [
        'deleted',
        'UltimaActualizacion',
        'FechaCreacion',
        'ModificadoPor',
        'CreadoPor',
        'Descripcion',
        'idEdificio',
    ];

    public function cat_edificio()
    {
        return $this->belongsTo(CatEdificio::class, 'idEdificio');
    }
}
