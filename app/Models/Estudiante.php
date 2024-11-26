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

	protected $appends = ['UnidadAdministrativa', 'Escolaridad', 'InstitucionEducativa'];

	protected $hidden = ['entidad'];

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
		'IdEntidad',
		'IdEscolaridad',
		'IdInstitucionEducativa',
		'Correo',
		'HorarioDesde',
		'HorarioHasta',
		'Frequencia'
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

	public function getEscolaridadAttribute()
	{
		if (!$this->IdEscolaridad) {
			Log::warning('IdEscolaridad está vacío.', ['id' => $this->id]);
			return 'N/A';
		}

		$nombre = DB::table('Cat_Escolaridad')
			->where('id', $this->IdEscolaridad)
			->value('Nombre');

		if (!$nombre) {
			Log::warning('Nombre no encontrado en Cat_Escolaridad.', ['IdEscolaridad' => $this->IdEscolaridad]);
		}

		return $nombre ?? 'N/A';
	}

	public function getInstitucionEducativaAttribute()
	{
		if (is_null($this->IdInstitucionEducativa)) {
			Log::warning('IdInstitucionEducativa está vacío.', ['id' => $this->id]);
			return 'N/A';
		}

		$nombre = DB::table('Cat_Institucion_Educativa')
			->where('id', $this->IdInstitucionEducativa)
			->value('Nombre');

		if (is_null($nombre)) {
			Log::warning('Resultado de la consulta de institución educativa es null.', ['IdInstitucionEducativa' => $this->IdInstitucionEducativa]);
		}

		return $nombre ?? 'N/A';
	}
}
