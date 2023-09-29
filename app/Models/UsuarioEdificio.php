<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UsuarioEdificio
 *
 * @property string $id
 * @property string $deleted
 * @property Carbon $UltimaActualizacion
 * @property Carbon $FechaCreacion
 * @property string $ModificadoPor
 * @property string $CreadoPor
 * @property string|null $idUsuario
 * @property string|null $IdEdificio
 *
 * @property CatEdificio|null $cat_edificio
 *
 * @package App\Models
 */
class UsuarioEdificio extends Model
{
    public $table = 'Usuario_Edificio';
    public $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    public $casts = [
        'deleted' => 'binary',
        'UltimaActualizacion' => 'datetime',
        'FechaCreacion' => 'datetime',
    ];

    public $fillable = [
        'deleted',
        'UltimaActualizacion',
        'FechaCreacion',
        'ModificadoPor',
        'CreadoPor',
        'idUsuario',
        'IdEdificio',
    ];

    public function cat_edificio()
    {
        return $this->belongsTo(CatEdificio::class, 'IdEdificio');
    }
}
