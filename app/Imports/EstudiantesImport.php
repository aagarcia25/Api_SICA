<?php

namespace App\Imports;

use App\Models\Estudiante;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EstudiantesImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{

    public $userid;

    public function __construct($userid)
    {
        $this->userid = $userid;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if ($row['nombre'] != null) {
            return new Estudiante([
                'ModificadoPor' => $this->userid,
                'CreadoPor' => $this->userid,
                'TipoEstudiante' => $row['tipo_estudiante'],
                'Nombre' => $row['nombre'],
                'UnidadAdministrativa' => $row['unidad_administrativa'],
                'FechaInicio' => $row['fecha_inicio'],
                'FechaFin' => $row['fecha_fin'],
                'Telefono' => $row['telefono'],
                'Sexo' => $row['sexo'],
                'Escolaridad' => $row['escolaridad'],
                'InstitucionEducativa' => $row['institucion_educativa'],
                'PersonaResponsable' => $row['persona_responsable'],
                'NoGaffete' => $row['no_gaffete'],
            ]);
        }

        return null; // Retorna null si no hay datos
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
