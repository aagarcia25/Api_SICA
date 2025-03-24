<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


/**
 * Class EntidadEspecial
 * 
 * @property string $id
 * @property string $deleted
 * @property Carbon $UltimaActualizacion
 * @property Carbon $FechaCreacion
 * @property string $ModificadoPor
 * @property string $CreadoPor
 * @property string|null $NombreEdntidad
 * @property string|null $idEntidad
 *
 * @package App\Models
 */
class EntidadEspecial extends Model
{
	protected $table = 'EntidadEspecial';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'UltimaActualizacion' => 'datetime',
		'FechaCreacion' => 'datetime'
	];

	protected $fillable = [
		'id',
		'deleted',
		'UltimaActualizacion',
		'FechaCreacion',
		'ModificadoPor',
		'CreadoPor',
		'NombreEdntidad',
		'idEntidad'
	];
}
