<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Estudiante;
use App\Models\Catalogo;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class EstudiantesImportv2 implements ToCollection, WithHeadingRow, WithChunkReading
{
    private $estudiantes;
    private $idUser; // Usuario proporcionado en el constructor
    private $errorCounter = 0;

    public function __construct($userUuid)
    {
        $this->idUser = $userUuid;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function collection(Collection $rows)
    {
        Log::info('Iniciando procesamiento de estudiantes: ' . $rows->count());
        $this->loadAllModels();

        $batchSize = 100;
        $batches = $rows->chunk($batchSize);

        foreach ($batches as $batch) {
            foreach ($batch as $row) {
                $this->processRow($row); // Procesa cada fila sin detener todo el lote
            }
        }

        Log::info('Procesamiento completado con éxito.');
    }



    public function loadAllModels()
    {
        $this->estudiantes = new Estudiante;
    }

    private function saveEstudiante($row)
    {
        try {
            // Verifica si ya existe un estudiante por correo o gafete
            //$existe = Estudiante::where('Correo', $row['correo'])
            //  ->orWhere('NoGaffete', $row['no_gaffete'])
            //   ->exists();

            // if ($existe) {
            // Log::warning("Estudiante ya existente: " . $row['correo']);
            // return null;
            // }

            //log debug para verificar fecha_inicio+
            Log::debug('Fecha Inicio: ' . $row['fecha_inicio']);
            //log debug para verificar fecha_fin
            Log::debug('Fecha Fin: ' . $row['fecha_fin']);

            $fechaInicio = $this->parseDate($row['fecha_inicio']);
            $fechaFin = $this->parseDate($row['fecha_fin']);

            // Crea el estudiante
            $estudiante = Estudiante::create([
                'Nombre' => $row['nombre'],
                'Sexo' => $row['sexo'],
                'Correo' => $row['correo'],
                'NoGaffete' => $row['no_gaffete'],
                'Frecuencia' => $row['frecuencia'],
                'HorarioDesde' => $row['horario_desde'],
                'HorarioHasta' => $row['horario_hasta'],
                'IdEscolaridad' => $this->getOrCreateCatalogEntry('Cat_Escolaridad', 'Nombre', $row['escolaridad']),
                'IdInstitucionEducativa' => $this->getOrCreateCatalogEntry('Cat_Institucion_Educativa', 'Nombre', $row['institucion_educativa']),
                'CreadoPor' => $this->idUser,
                'ModificadoPor' => $this->idUser,
                'IdEntidad' => '127c5167-8959-11ee-9d15-d89d6776f970',
                'PersonaResponsable' => $row['persona_responsable'],
                'TipoEstudiante' => $row['tipo_estudiante'],
                'Telefono' => $row['telefono'],
                'FechaInicio' => $fechaInicio,
                'FechaFin' => $fechaFin,
            ]);

            return $estudiante;
        } catch (\Exception $e) {
            Log::error("Error al guardar estudiante: " . $e->getMessage());
            throw $e;
        }
    }

    private function parseDate($date)
    {
        // Log para verificar el valor recibido
        Log::info('Fecha recibida: ' . $date);

        try {
            if (!empty($date)) {
                // Verifica si la fecha es un número (formato de Excel)
                if (is_numeric($date)) {
                    // Convierte el número de Excel a una fecha usando el inicio "1900-01-01"
                    return Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($date - 2)->format('Y-m-d');
                }

                // Intenta formatear la fecha con formatos conocidos
                $formats = ['d/m/Y', 'Y-m-d', 'm/d/Y'];
                foreach ($formats as $format) {
                    try {
                        return Carbon::createFromFormat($format, $date)->format('Y-m-d');
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            // Log de advertencia si no se puede interpretar la fecha
            Log::warning('Fecha inválida o vacía detectada: ' . $date);
            return null;
        } catch (\Exception $e) {
            Log::error('Error al procesar la fecha: ' . $date . ' - ' . $e->getMessage());
            return null;
        }
    }





    private function getOrCreateCatalogEntry($table, $field, $value)
    {
        try {
            if (is_null($value)) {
                $value = "N/A";
            }

            $catalogo = new Catalogo;
            $catalogo->setTable($table);

            $catalogo = $catalogo->newQuery()->where($field, $value)->first();

            if (!$catalogo) {
                $catalogo = new Catalogo([], $table);
                $catalogo->setTable($table);
                $catalogo->$field = $value;
                $catalogo->Nombre = $value;
                $catalogo->CreadoPor = $this->idUser;
                $catalogo->save();
            }

            return $catalogo->id;
        } catch (\Exception $e) {
            Log::error("Error en getOrCreateCatalogEntry: " . $e->getMessage());
            return null;
        }
    }

    public function processRow($row)
    {
        try {
            // Verificar si el campo clave 'nombre' está vacío
            if (empty($row['nombre'])) {
                //Log::warning('Fila con nombre vacío detectada. Procesamiento detenido para esta fila.');
                return; // Detiene el procesamiento de esta fila sin lanzar excepción
            }

            $estudiante = $this->saveEstudiante($row);

            if (!$estudiante) {
                Log::warning("Error al guardar estudiante: " . $row['correo']);
                return; // Detiene el procesamiento de esta fila sin lanzar excepción
            }

            Log::info('Procesamiento completado para el estudiante: ' . $estudiante->id);
        } catch (\Exception $e) {
            Log::error('Error inesperado en fila: ' . $e->getMessage());
        }
    }
}
