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
 * @property Collection|CatEntradasEdi[] $Cat_Entradas_Edis
 * @property Collection|UsuarioEdificio[] $Usuario_Edificios
 *
 * @package App\Models
 */
class CatEdificio extends Model
{
    public $table = 'Cat_Edificios';
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

    public function Cat_Entradas_Edis()
    {
        return $this->hasMany(CatEntradasEdi::class, 'idEdificio');
    }

    public function Usuario_Edificios()
    {
        return $this->hasMany(UsuarioEdificio::class, 'IdEdificio');
    }
}
