<?php

namespace App\Http\Controllers;

use App\Models\Visitum;
use DateTime;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VisitumController extends Controller
{
    /* SE IDENTIFICA EL TIPO DE OPERACION A REALIZAR
    1._ INSERTAR UN REGISTRO
    2._ ACTUALIZAR UN REGISTRO
    3._ ELIMINAR UN REGISTRO
    4._ CONSULTAR GENERAL DE REGISTROS, (SE INCLUYEN FILTROS)
     */

    public function visita_index(Request $request)
    {

        $SUCCESS = true;
        $NUMCODE = 0;
        $STRMESSAGE = 'Exito';
        $response = "";

        try {
            $type = $request->NUMOPERACION;

            if ($type == 1) {
                $idgenerado = Str::uuid();

                $OBJ = new Visitum();
                $OBJ->id = $idgenerado;
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->CreadoPor = $request->CHUSER;
                $OBJ->FechaVisita = new DateTime($request->FechaVisita);
                $OBJ->Duracion = intval($request->Duracion);
                $OBJ->IdTipoAcceso = $request->IdTipoAcceso;
                $OBJ->Proveedor = $request->Proveedor;
                $OBJ->NombreVisitante = $request->NombreVisitante;
                $OBJ->ApellidoPVisitante = $request->ApellidoPVisitante;
                $OBJ->ApellidoMVisitante = $request->ApellidoMVisitante;
                $OBJ->idTipoentidad = $request->idTipoentidad;
                $OBJ->idEntidad = $request->idEntidad;
                $OBJ->NombreReceptor = $request->NombreReceptor;
                $OBJ->ApellidoPReceptor = $request->ApellidoPReceptor;
                $OBJ->ApellidoMReceptor = $request->ApellidoMReceptor;
                $OBJ->idEntidadReceptor = $request->idEntidadReceptor;
                $OBJ->PisoReceptor = $request->PisoReceptor;
                $OBJ->EmailNotificacion = $request->EmailNotificacion;
                $OBJ->save();
                $data = Visitum::find($idgenerado);

                $response = $data;

            } elseif ($type == 2) {

                $OBJ = Visitum::find($request->CHID);
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->FechaVisita = new DateTime($request->FechaVisita);
                $OBJ->Duracion = intval($request->Duracion);
                $OBJ->IdTipoAcceso = $request->IdTipoAcceso;
                $OBJ->Proveedor = $request->Proveedor;
                $OBJ->NombreVisitante = $request->NombreVisitante;
                $OBJ->ApellidoPVisitante = $request->ApellidoPVisitante;
                $OBJ->ApellidoMVisitante = $request->ApellidoMVisitante;
                $OBJ->idTipoentidad = $request->idTipoentidad;
                $OBJ->idEntidad = $request->idEntidad;
                $OBJ->NombreReceptor = $request->NombreReceptor;
                $OBJ->ApellidoPReceptor = $request->ApellidoPReceptor;
                $OBJ->ApellidoMReceptor = $request->ApellidoMReceptor;
                $OBJ->idEntidadReceptor = $request->idEntidadReceptor;
                $OBJ->PisoReceptor = $request->PisoReceptor;
                $OBJ->EmailNotificacion = $request->EmailNotificacion;
                $OBJ->save();
                $response = $OBJ;

            } elseif ($type == 3) {
                $OBJ = Visitum::find($request->CHID);
                $OBJ->deleted = 1;
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->save();
                $response = $OBJ;

            } elseif ($type == 4) {
                $query = "
                    SELECT
                    *
                    FROM SICA.Visita
                    where deleted =0
                    ";
                $query = $query . " and CreadoPor='" . $request->CHIDUSER . "'";
                $response = DB::select($query);

            } elseif ($type == 5) {
                $query = "
                     SELECT
   vs.id,
	vs.deleted,
	vs.UltimaActualizacion,
	vs.FechaCreacion,
	getUserName(vs.ModificadoPor) ModificadoPor,
	getUserName(vs.CreadoPor) CreadoPor,
	vs.FechaVisita,
	vs.FechaEntrada,
	vs.FechaSalida,
	vs.Duracion,
	vs.IdTipoAcceso,
	vs.Proveedor,
	vs.NombreVisitante,
	vs.ApellidoPVisitante,
	vs.ApellidoMVisitante,
	vs.idTipoentidad,
	vs.idEntidad,
	vs.NombreReceptor,
	vs.ApellidoPReceptor,
	vs.ApellidoMReceptor,
	vs.PisoReceptor,
	vs.IdEstatus,
    vs.IdEntidadReceptor,
	DATE_ADD(vs.FechaVisita, INTERVAL vs.Duracion HOUR) tiempo,
    	en.Nombre entidadname,
        	en2.Nombre entidadreceptor,
        case
						  when vs.FechaVisita > NOW() then '#AF8C55'
                    when vs.FechaVisita < NOW() then '#EC7063'
                    ELSE 'blue'
						  END color,
                            catpi.Descripcion pisoreceptorrr
   FROM SICA.Visita vs
   LEFT JOIN TiCentral.Entidades en  ON vs.idEntidad = en.Id
   LEFT JOIN TiCentral.Entidades en2  ON vs.IdEntidadReceptor = en2.Id
    JOIN SICA.Cat_Pisos catpi ON catpi.id = vs.PisoReceptor
   where vs.deleted =0
                    ";
                $query = $query . " and vs.Id='" . $request->CHID . "'";

                $response = DB::select($query);

            } elseif ($type == 6) {

                $OBJ = Visitum::find($request->CHID);
                if (is_null($OBJ->FechaEntrada)) {
                    $OBJ->FechaEntrada = now();
                    $OBJ->IdEstatus = "4112a976-5183-11ee-b06d-3cd92b4d9bf4";
                } else {
                    $OBJ->FechaSalida = now();
                    $OBJ->IdEstatus = "0779435b-5718-11ee-b06d-3cd92b4d9bf4";
                    $OBJ->Finalizado = 1;
                }

                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->save();
                $response = $OBJ;

            } elseif ($type == 7) {

                $query = "
                    SELECT
                    vs.id,
                    vs.IdEstatus estatus,
                    CONCAT(vs.NombreReceptor,' ',vs.ApellidoPReceptor,' ',vs.ApellidoMReceptor) title,
                    vs.FechaVisita start,
                    DATE_ADD(vs.FechaVisita, INTERVAL vs.Duracion HOUR) end,
                    case
						  when vs.FechaVisita > NOW() then '#AF8C55'
                    when vs.FechaVisita < NOW() then '#EC7063'
                    ELSE 'blue'
						  END color
                    FROM SICA.Visita vs
                    where vs.deleted =0
                    and vs.FechaSalida is null
                    ";
                $query = $query . " and vs.CreadoPor='" . $request->CHID . "'";
                $response = DB::select($query);
            } elseif ($type == 8) {
                $query = "
                  SELECT
                      vis.id,
                      ce.Descripcion,
                      vis.Finalizado
                      FROM SICA.Visita vis
                      INNER JOIN SICA.Cat_Estatus ce ON vis.IdEstatus = ce.id
                       WHERE 1=1
                    ";
                $query = $query . " and vis.id='" . $request->CHID . "'";
                $response = DB::select($query);
            } elseif ($type == 9) {
                $query = "
                   SELECT
                       vs.id,
                       vs.deleted,
                       vs.UltimaActualizacion,
                       vs.FechaCreacion,
                       getUserName(vs.ModificadoPor) ModificadoPor,
                       getUserName(vs.CreadoPor) CreadoPor,
                       vs.FechaVisita,
                       vs.FechaEntrada,
                       vs.FechaSalida,
                       vs.Duracion,
                       vs.IdTipoAcceso,
                       vs.Proveedor,
                       vs.NombreVisitante,
                       vs.ApellidoPVisitante,
                       vs.ApellidoMVisitante,
                       vs.idTipoentidad,
                       vs.idEntidad,
                       vs.NombreReceptor,
                       vs.ApellidoPReceptor,
                       vs.ApellidoMReceptor,
                       vs.PisoReceptor,
                       vs.IdEstatus,
                       vs.IdEntidadReceptor,
                       DATE_ADD(vs.FechaVisita, INTERVAL vs.Duracion HOUR) tiempo,
                       en.Nombre entidadname,
                       en2.Nombre entidadreceptor,
                       case
                           when vs.FechaVisita > NOW() then '#AF8C55'
                           when vs.FechaVisita < NOW() then '#EC7063'
                           ELSE 'blue'
                       		END color,
                        catpi.Descripcion pisoreceptorrr,
                        vs.Finalizado,
                         (TIMESTAMPDIFF(Hour, vs.FechaEntrada, vs.FechaSalida) ) AS tiempovisita
                        FROM SICA.Visita vs
                        LEFT JOIN TiCentral.Entidades en  ON vs.idEntidad = en.Id
                        LEFT JOIN TiCentral.Entidades en2  ON vs.IdEntidadReceptor = en2.Id
                        LEFT JOIN SICA.Cat_Pisos catpi ON catpi.id = vs.PisoReceptor
                        Where vs.deleted =0
                    ";
                $response = DB::select($query);

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
            ]);

    }

}
