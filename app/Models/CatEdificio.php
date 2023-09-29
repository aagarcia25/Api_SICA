<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CatEdificio
 *
 * @property string $id
 * @property string $deleted
 * @property Carbon $UltimaActualizacion
 * @property Carbon $FechaCreacion
 * @property string $ModificadoPor
 * @property string $CreadoPor
 * @property string $Descripcion
 * @property string $Calle
 * @property string $Colonia
 * @property string $Municipio
 * @property string $Estado
 * @property string $CP
 *
 * @property Collection|CatEntradasEdi[] $cat_entradas_edis
 * @property Collection|UsuarioEdificio[] $usuario_edificios
 *
 * @package App\Models
 */
class CatEdificio extends Model
{
    public $table = 'cat_edificios';
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
        'Calle',
        'Colonia',
        'Municipio',
        'Estado',
        'CP',
    ];

    public function cat_entradas_edis()
    {
        return $this->hasMany(CatEntradasEdi::class, 'idEdificio');
    }

    public function usuario_edificios()
    {
        return $this->hasMany(UsuarioEdificio::class, 'IdEdificio');
    }
}
