<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Estudiante
 * 
 * @property string $id
 * @property string $deleted
 * @property Carbon $UltimaActualizacion
 * @property Carbon $FechaCreacion
 * @property string $ModificadoPor
 * @property string $CreadoPor
 * @property string $TipoEstudiante
 * @property string $Nombre
 * @property string $UnidadAdministrativa
 * @property Carbon $FechaInicio
 * @property Carbon $FechaFin
 * @property string $Telefono
 * @property string $Escolaridad
 * @property string $InstitucionEducativa
 * @property string $PersonaResponsable
 * @property string $NoGaffete
 *
 * @package App\Models
 */
class Estudiante extends Model
{
	protected $table = 'Estudiantes';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'UltimaActualizacion' => 'datetime',
		'FechaCreacion' => 'datetime',
		'FechaInicio' => 'datetime',
		'FechaFin' => 'datetime'
	];

	protected $appends = ['UnidadAdministrativa'];

	protected $hidden = ['IdEntidad', 'entidad'];

	protected $fillable = [
		'deleted',
		'UltimaActualizacion',
		'FechaCreacion',
		'ModificadoPor',
		'CreadoPor',
		'TipoEstudiante',
		'Nombre',
		'UnidadAdministrativa',
		'FechaInicio',
		'FechaFin',
		'Telefono',
		'Escolaridad',
		'InstitucionEducativa',
		'PersonaResponsable',
		'NoGaffete',
		'IdEntidad'
	];


	public function entidad()
	{
		return $this->belongsTo(Entidad::class, 'IdEntidad', 'Id');
	}

	public function getUnidadAdministrativaAttribute()
	{
		// Devuelve el nombre de la entidad si existe, de lo contrario, null
		return $this->entidad ? $this->entidad->Nombre : null;
	}
}
