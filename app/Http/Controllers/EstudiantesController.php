<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estudiante;
use App\Models\Visitum;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiDocTrait;
use Illuminate\Support\Facades\Log;


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
                $OBJ->UnidadAdministrativa = $request->UnidadAdministrativa;
                $OBJ->FechaInicio = $request->FechaInicio;
                $OBJ->FechaFin = $request->FechaFin;
                $OBJ->Telefono = $request->Telefono;
                $OBJ->Sexo = $request->Sexo;
                $OBJ->Escolaridad = $request->Escolaridad;
                $OBJ->InstitucionEducativa = $request->InstitucionEducativa;
                $OBJ->PersonaResponsable = $request->PersonaResponsable;
                $OBJ->NoGaffete = $request->NoGaffete;

                $OBJ->save();
                $response = $OBJ;
            } elseif ($type == 2) {

                $OBJ = Estudiante::find($request->CHID);
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->TipoEstudiante = $request->TipoEstudiante;
                $OBJ->Nombre = $request->Nombre;
                $OBJ->UnidadAdministrativa = $request->UnidadAdministrativa;
                $OBJ->FechaInicio = $request->FechaInicio;
                $OBJ->FechaFin = $request->FechaFin;
                $OBJ->Telefono = $request->Telefono;
                $OBJ->Escolaridad = $request->Escolaridad;
                $OBJ->InstitucionEducativa = $request->InstitucionEducativa;
                $OBJ->PersonaResponsable = $request->PersonaResponsable;
                $OBJ->NoGaffete = $request->NoGaffete;
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
                $CHID = $request->CHID; // Identificador 
                Log::info("CHID");
                Log::info($CHID);

                $response = [];

                // Buscar en la tabla Visitas
                $visita = Visitum::find($CHID);
                if ($visita) {
                    $response['tabla'] = 'Visitas';
                    $response['datos'] = $visita;
                    Log::info("El ID pertenece a la tabla Visitas.");
                } else {
                    // Buscar en la tabla Estudiantes si no está en Visitas
                    $estudiante = Estudiante::find($CHID);
                    if ($estudiante) {
                        $response['tabla'] = 'Estudiantes';
                        $response['datos'] = $estudiante;
                        Log::info("El ID pertenece a la tabla Estudiantes.");
                    } else {
                        $response['tabla'] = null;
                        $response['mensaje'] = "El ID no pertenece a ninguna tabla.";
                        Log::warning("El ID no pertenece a Visitas ni a Estudiantes.");
                    }
                }

                // return response()->json($response);

            }elseif ($type == 8) {
                //extender fecha fin
                $OBJ = Estudiante::find($request->CHID);
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->FechaFin = $request->FechaFin;
                
                $OBJ->save();
                $response = $OBJ;
            }else if ($type == 9) {
                //Cambiar estado de qr 
                $CHIDs = $request->input('CHIDs'); 
                $response = [];

                foreach ($CHIDs as $CHID) {
                $OBJ = Estudiante::find($CHID);

                    if ($OBJ) {
                    $OBJ->EstadoQR = 1;
                    $OBJ->ModificadoPor = $request->CHUSER;
                    $OBJ->save();
                    $response[] = $OBJ;
                    }
                }
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
            'EstadoQR'
        ])
            ->with('entidad') // Carga la relación anticipadamente
            ->where('deleted', 0)
            ->get();
    }
}
