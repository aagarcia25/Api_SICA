<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estudiante;
use App\Models\Visitum;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiDocTrait;
use Illuminate\Support\Facades\Log;

use App\Models\VisitaBitacora;

use carbon\carbon;

use Illuminate\Support\Facades\Mail;


class EstudiantesController extends Controller
{
    use ApiDocTrait;
    //
    public function Estudiante(Request $request)
    {

        $SUCCESS = true;
        $NUMCODE = 0;
        $STRMESSAGE = 'Exito';
        $response = "";

        try {
            $type = $request->NUMOPERACION;

            if ($type == 1) {
                $OBJ = new Estudiante();
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->CreadoPor = $request->CHUSER;
                $OBJ->TipoEstudiante = $request->TipoEstudiante;
                $OBJ->Nombre = $request->Nombre;
                $OBJ->IdEntidad = $request->UnidadAdministrativa;
                $OBJ->FechaInicio = $request->FechaInicio;
                $OBJ->FechaFin = $request->FechaFin;
                $OBJ->Telefono = $request->Telefono;
                $OBJ->Sexo = $request->Sexo;
                $OBJ->IdEscolaridad = $request->Escolaridad;
                $OBJ->IdInstitucionEducativa = $request->InstitucionEducativa;
                $OBJ->PersonaResponsable = $request->PersonaResponsable;
                $OBJ->NoGaffete = $request->NoGaffete;
                $OBJ->Correo = $request->Correo;
                $OBJ->Frecuencia = $request->frecuenciaAsistencia;
                $OBJ->HorarioDesde = $request->HorarioDesde;
                $OBJ->HorarioHasta = $request->HorarioHasta;


                $OBJ->save();
                $response = $OBJ;
            } elseif ($type == 2) {

                $OBJ = Estudiante::find($request->CHID);
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->TipoEstudiante = $request->TipoEstudiante;
                $OBJ->Nombre = $request->Nombre;
                $OBJ->IdEntidad = $request->UnidadAdministrativa;
                $OBJ->FechaInicio = $request->FechaInicio;
                $OBJ->FechaFin = $request->FechaFin;
                $OBJ->Telefono = $request->Telefono;
                $OBJ->Sexo = $request->Sexo;
                $OBJ->IdEscolaridad = $request->Escolaridad;
                $OBJ->IdInstitucionEducativa = $request->InstitucionEducativa;
                $OBJ->PersonaResponsable = $request->PersonaResponsable;
                $OBJ->NoGaffete = $request->NoGaffete;
                $OBJ->Correo = $request->Correo;
                $OBJ->Frecuencia = $request->frecuenciaAsistencia;
                $OBJ->HorarioDesde = $request->HorarioDesde;
                $OBJ->HorarioHasta = $request->HorarioHasta;

                $OBJ->save();
                $response = $OBJ;
            } elseif ($type == 3) {
                $OBJ = Estudiante::find($request->CHID);
                $OBJ->deleted = 1;
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->save();
                $response = $OBJ;
            } elseif ($type == 4) {
                $response = $this->obtenerEstudiantes();
            } elseif ($type == 5) {
                $file = request()->file('FILE');

                $nombre = $file->getClientOriginalName();
                $data = $this->UploadFile($request->TOKEN, env('APP_DOC_ROUTE') . "/FOTOS" . "/" . $request->ID, $nombre, $file, 'TRUE');
            } elseif ($type == 6) {
                $data = $this->ListFile($request->TOKEN, env('APP_DOC_ROUTE') . "/FOTOS" . "/" .  $request->P_ROUTE);

                $response = $data->RESPONSE;
            } elseif ($type == 7) {
                $CHID = $request->CHID;

                return $this->obtenerDetalleEntidadEstudiante($CHID);
            } elseif ($type == 8) {
                //extender fecha fin
                $OBJ = Estudiante::find($request->CHID);
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->FechaFin = $request->FechaFin;

                $OBJ->save();
                $response = $OBJ;
            } else if ($type == 9) {

                //Cambiar estado de qr 
                return $this->cambiarEstadoYEnviarNotificacion($request);
            } elseif ($type == 10) {
                // Registrar entrada
                return $this->registrarEntradaEstudiante($request->CHID, $request->CHUSER);
            } elseif ($type == 11) {
                // Registrar salida
                return $this->registrarSalidaEstudiante($request->CHID, $request->CHUSER);
            }
        } catch (QueryException $e) {
            $SUCCESS = false;
            $NUMCODE = 1;
            $STRMESSAGE = $this->buscamsg($e->getCode(), $e->getMessage());
        } catch (\Exception $e) {
            $SUCCESS = false;
            $NUMCODE = 1;
            $STRMESSAGE = $e->getMessage();
        }
        return response()->json(
            [
                'NUMCODE' => $NUMCODE,
                'STRMESSAGE' => $STRMESSAGE,
                'RESPONSE' => $response,
                'SUCCESS' => $SUCCESS,
            ]
        );
    }

    // Función para manejar el caso 4
    protected function obtenerEstudiantes()
    {
        return Estudiante::select([
            'id',
            'deleted',
            'UltimaActualizacion',
            'FechaCreacion',
            DB::raw('getUserName(ModificadoPor) as modi'),
            DB::raw('getUserName(CreadoPor) as creado'),
            'TipoEstudiante',
            'Nombre',
            'FechaInicio',
            'FechaFin',
            'Telefono',
            'Sexo',
            'PersonaResponsable',
            'NoGaffete',
            'IdEntidad', // Este campo es obligatorio para la relación
            'IdEscolaridad', // Este campo es obligatorio para la relación
            'IdInstitucionEducativa',
            'EstadoQR',
            'Correo',
            'Frecuencia',
            'HorarioDesde',
            'HorarioHasta'
        ])
            ->with('entidad') // Carga la relación anticipadamente
            ->where('deleted', 0)
            ->get();
    }
    public function registrarEntradaEstudiante($CHID, $CHUSER)
    {
        // Verificar si hay un registro sin salida
        $registroSinSalida = VisitaBitacora::where('IdVisita', $CHID)
            ->where('tipo', 'ESTUDIANTE')
            ->whereNull('FechaSalida')
            ->first();

        if ($registroSinSalida) {
            return $this->createResponse(
                null,
                "Ya existe una entrada sin salida registrada.",
                false,
                1
            );
        }

        // Registrar una nueva entrada
        $bitacora = new VisitaBitacora();
        $bitacora->IdVisita = $CHID;
        $bitacora->tipo = 'ESTUDIANTE';
        $bitacora->FechaEntrada = now();
        $bitacora->CreadoPor = $CHUSER;
        $bitacora->ModificadoPor = $CHUSER;
        $bitacora->FechaCreacion = now();
        $bitacora->UltimaActualizacion = now();
        $bitacora->IdEstatus = "4112a976-5183-11ee-b06d-3cd92b4d9bf4"; // Estatus por defecto
        $bitacora->save();

        return $this->createResponse($bitacora, "Entrada registrada con éxito.");
    }


    public function registrarSalidaEstudiante($CHID, $CHUSER)
    {
        $bitacora = VisitaBitacora::where('IdVisita', $CHID)
            ->where('tipo', 'ESTUDIANTE') // Asegurarse de que sea un estudiante
            ->whereNull('FechaSalida') // Buscar la última entrada sin salida
            ->orderBy('FechaEntrada', 'desc')
            ->first();

        if (!$bitacora) {
            return $this->createResponse(
                null,
                "No se encontró un registro de entrada sin salida.",
                false,
                1
            );
        }

        $bitacora->FechaSalida = now();
        $bitacora->ModificadoPor = $CHUSER;
        $bitacora->UltimaActualizacion = now();
        $bitacora->IdEstatus = "0779435b-5718-11ee-b06d-3cd92b4d9bf4"; // Estatus por defecto
        $bitacora->save();

        return $this->createResponse($bitacora, "Salida registrada con éxito.");
    }

    private function obtenerDetalleEntidadEstudiante($CHID)
    {
        // Buscar el estudiante
        $estudiante = Estudiante::find($CHID);


        if (!$estudiante) {
            // Si no es un estudiante, devolver directamente como visita
            Log::warning("El ID no pertenece a Estudiantes. Se asumirá como una Visita.");
            return $this->createResponse(
                [
                    'tabla' => 'Visitas',
                    'datos' => null, // Aquí podrías dejar datos vacíos si es necesario.
                ],
                "El ID pertenece a una Visita.",
                true,
                0
            );
        }

        // Buscar en la bitácora del estudiante
        $bitacora = VisitaBitacora::where('IdVisita', $CHID)
            ->where('tipo', 'ESTUDIANTE') // Filtrar solo registros de estudiantes
            ->orderBy('FechaEntrada', 'desc') // Obtener el más reciente
            ->first();

        // Determinar fechas de entrada y salida
        $fechaEntrada = null;
        $fechaSalida = null;
        if ($bitacora) {
            // Si hay entrada y salida en el último registro, retornar null para ambas
            if ($bitacora->FechaEntrada && $bitacora->FechaSalida) {
                $fechaEntrada = null;
                $fechaSalida = null;
            } else {
                $fechaEntrada = $bitacora->FechaEntrada;
                $fechaSalida = $bitacora->FechaSalida;
            }
        }

        // Calcular las horas totales
        $horasTotales = $this->calcularHorasEstudiante($CHID)['HorasTotales'];

        // Datos de la respuesta
        $datos = [
            'id' => $estudiante->id,
            'deleted' => $estudiante->deleted ?? "0",
            'UltimaActualizacion' => $estudiante->UltimaActualizacion,
            'FechaCreacion' => $estudiante->FechaCreacion,
            'ModificadoPor' => $estudiante->ModificadoPor,
            'CreadoPor' => $estudiante->CreadoPor,
            'TipoEstudiante' => $estudiante->TipoEstudiante,
            'Nombre' => $estudiante->Nombre,
            'FechaInicio' => $estudiante->FechaInicio,
            'FechaFin' => $estudiante->FechaFin,
            'Telefono' => $estudiante->Telefono,
            'Sexo' => $estudiante->Sexo,
            'PersonaResponsable' => $estudiante->PersonaResponsable,
            'NoGaffete' => $estudiante->NoGaffete,
            'EstadoQR' => $estudiante->EstadoQR ?? "1",
            'UnidadAdministrativa' => $estudiante->UnidadAdministrativa,
            'Escolaridad' => $estudiante->Escolaridad,
            'InstitucionEducativa' => $estudiante->InstitucionEducativa,
            'FechaEntrada' => $fechaEntrada, // Fecha entrada calculada
            'FechaSalida' => $fechaSalida,   // Fecha salida calculada
            'IdEstatus' => $bitacora?->IdEstatus ?? null, // Estatus más reciente de la bitácora
            'HorasTotales' => $horasTotales,
            'Correo' => $estudiante->Correo,
            'HorarioDesde' => $estudiante->HorarioDesde,
            'HorarioHasta' => $estudiante->HorarioHasta,
            'Frequencia' => $estudiante->Frecuencia,

        ];

        Log::info("El ID pertenece a la tabla Estudiantes.");
        return $this->createResponse(
            [
                'tabla' => 'Estudiantes',
                'datos' => $datos
            ],
            "Consulta exitosa."
        );
    }


    private function calcularHorasEstudiante($CHID)
    {
        // Buscar todos los registros de bitácora para el estudiante
        $bitacora = VisitaBitacora::where('IdVisita', $CHID)
            ->where('tipo', 'ESTUDIANTE') // Filtrar solo registros de estudiantes
            ->whereNotNull('FechaEntrada') // Asegurar que tiene fecha de entrada
            ->whereNotNull('FechaSalida') // Asegurar que tiene fecha de salida
            ->get();

        $horasTotales = 0;

        foreach ($bitacora as $registro) {
            // Calcular la diferencia en horas entre entrada y salida
            $entrada = Carbon::parse($registro->FechaEntrada);
            $salida = Carbon::parse($registro->FechaSalida);
            $horasTotales += $entrada->diffInHours($salida);
        }

        return [
            'HorasTotales' => $horasTotales,
            'Registros' => $bitacora
        ];
    }

    public function cambiarEstadoYEnviarNotificacion(Request $request)
    {
        $CHIDs = $request->input('CHIDs'); // IDs de estudiantes
        $response = [];
        $errores = [];
        $sinCorreo = []; // Lista para estudiantes sin correo

        foreach ($CHIDs as $CHID) {
            $estudiante = Estudiante::find($CHID);

            if (!$estudiante) {
                $errores[] = [
                    'id' => $CHID,
                    'error' => "Estudiante no encontrado."
                ];
                continue;
            }

            // Cambiar estado del QR
            $estudiante->EstadoQR = 1;
            $estudiante->ModificadoPor = $request->CHUSER;
            $estudiante->save();

            try {
                // Generar PDF con el QR
                $filePath = app(GeneracionDocumentosPDFController::class)->generatePdfFile($estudiante);

                // Verificar si el estudiante tiene correo antes de enviar la notificación
                if (!empty($estudiante->Correo)) {
                    $this->enviarNotificacion($estudiante, $filePath);
                    $response[] = $estudiante; // Agregar al listado de procesados
                } else {
                    $sinCorreo[] = $estudiante->Nombre; // Agregar a la lista de sin correo
                }
            } catch (\Exception $e) {
                // Capturar cualquier excepción crítica
                $errores[] = [
                    'id' => $CHID,
                    'error' => "Error crítico: {$e->getMessage()}"
                ];
            }
        }

        // Construir mensaje detallado
        $message = [
            'success' => 'Todos los QRs generados correctamente.',
            'warnings' => count($sinCorreo) > 0
                ? ['message' => 'Algunos estudiantes no tenían correo registrado.', 'students' => $sinCorreo]
                : null,
            'errors' => count($errores) > 0
                ? ['message' => 'Errores críticos durante el proceso.', 'details' => $errores]
                : null
        ];

        // Evaluar éxito general
        $success = empty($errores); // Verdadero si no hay errores críticos

        return $this->createResponse(
            $response,
            $message,
            $success,
            $success ? 0 : 1
        );
    }




    private function enviarNotificacion($estudiante, $filePath)
    {
        if (empty($estudiante->Correo)) {
            throw new \Exception("El estudiante {$estudiante->id} no tiene correo electrónico registrado.");
        }

        $correo = $estudiante->Correo;
        $correoCopiaOculta = 'mkcortes.86@gmail.com'; // Correo por defecto para copia oculta

        Mail::send('notificacionEstudiante', ['data' => $estudiante], function ($message) use ($correo, $correoCopiaOculta, $filePath) {
            $message->to($correo)
                ->bcc($correoCopiaOculta) // Agregar copia oculta
                ->subject('Notificación de QR para Acceso')
                ->attach($filePath);
        });
    }




    private function createResponse($data = null, $message = 'Exito', $success = true, $numCode = 0)
    {
        return response()->json([
            'NUMCODE' => $numCode,
            'STRMESSAGE' => $message,
            'RESPONSE' => $data,
            'SUCCESS' => $success,
        ]);
    }
}
