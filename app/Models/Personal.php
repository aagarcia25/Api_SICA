<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Personal
 * 
 * @property string $id
 * @property string $deleted
 * @property Carbon $UltimaActualizacion
 * @property Carbon $FechaCreacion
 * @property string $ModificadoPor
 * @property string $CreadoPor
 * @property string $Nombre
 * @property string $ApellidoPaterno
 * @property string $ApellidoMaterno
 * @property string $CorreoElectronico
 * @property string $CURP
 * @property string $Telefono
 * @property string $Ext
 * @property string|null $idEntidad
 * @property string $Puesto
 *
 * @package App\Models
 */
class Personal extends Model
{
	protected $table = 'Personal';
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
		'Nombre',
		'ApellidoPaterno',
		'ApellidoMaterno',
		'CorreoElectronico',
		'CURP',
		'Telefono',
		'Ext',
		'idEntidad',
		'Puesto'
	];
}
