<?php

namespace App\Http\Controllers;

use App\Models\Visitum;
use DateTime;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VisitumController extends Controller
{
    /* SE IDENTIFICA EL TIPO DE OPERACION A REALIZAR
    1._ INSERTAR UN REGISTRO
    2._ ACTUALIZAR UN REGISTRO
    3._ ELIMINAR UN REGISTRO
    4._ CONSULTAR GENERAL DE REGISTROS, (SE INCLUYEN FILTROS)
     */

    public function dataNotificacion($id)
    {
        $query = "
                     SELECT
                      vs.id,
	                  vs.FechaVisita,
                      vs.Duracion,
	                  CONCAT(ce.Calle, ' ',ce.Colonia,' ',ce.CP , '',ce.Municipio) Direccion,
	                  CONCAT(vs.NombreReceptor, ' ',vs.ApellidoPReceptor,' ',vs.ApellidoMReceptor ) receptor,
                      CONCAT(vs.NombreVisitante, ' ',vs.ApellidoPVisitante,' ',vs.ApellidoMVisitante ) visitante,
	                  en2.Nombre entidadreceptor,
	                  catpi.Descripcion pisoreceptorrr,
                      ce.Descripcion edificio
                      FROM SICA.Visita vs
                      LEFT JOIN TiCentral.Entidades en  ON vs.idEntidad = en.Id
                      LEFT JOIN TiCentral.Entidades en2  ON vs.IdEntidadReceptor = en2.Id
                      LEFT JOIN SICA.Cat_Pisos catpi ON catpi.id = vs.PisoReceptor
                      LEFT JOIN SICA.Cat_Edificios ce ON ce.id = vs.IdEdificio
                      LEFT JOIN SICA.Cat_Entradas_Edi cee ON catpi.id = vs.IdAcceso
                      where vs.deleted =0
                    ";
        $query = $query . " and vs.id='" . $id . "'";
        $OBJ = DB::select($query);
        return $OBJ;
    }
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
                $OBJ->IdEdificio = $request->IdEdificio;
                $OBJ->IdAcceso = $request->IdAcceso;
                $OBJ->save();

                $data = $this->dataNotificacion($idgenerado);

                $rutaTemporal = storage_path('app/temp/qr.png');

                // Renderiza la vista en formato HTML
                $html = view('notificacioEntrega', ['data' => $data[0]])->render();

                // Configura Dompdf
                $options = new \Dompdf\Options();
                $options->set('isHtml5ParserEnabled', true);
                $options->set('isPhpEnabled', true);

                $dompdf = new \Dompdf\Dompdf($options);
                $dompdf->loadHtml($html);

                // Establece el tamaño del papel y la orientación
                $dompdf->setPaper('A4', 'portrait');

                // Renderiza el PDF
                $dompdf->render();

                // Guarda el PDF en la ruta temporal
                file_put_contents($rutaTemporal, $dompdf->output());

                $correo = $request->EmailNotificacion;
                Mail::send('notificacioEntrega', ['data' => $data[0]], function ($message) use ($rutaTemporal, $correo) {
                    $message->to($correo)
                        ->subject('Notificación de Visita');
                    $message->attach($rutaTemporal);
                });

                unlink($rutaTemporal);
                $objresul = Visitum::find($idgenerado);

                $response = $objresul;

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
                $OBJ->IdEdificio = $request->IdEdificio;
                $OBJ->IdAcceso = $request->IdAcceso;

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
                                                  catpi.Descripcion pisoreceptorrr,
                                                  vs.EmailNotificacion,
                                                  ce.id idEdificio,
                                                  cee.id idAcceso
                         FROM SICA.Visita vs
                         LEFT JOIN TiCentral.Entidades en  ON vs.idEntidad = en.Id
                         LEFT JOIN TiCentral.Entidades en2  ON vs.IdEntidadReceptor = en2.Id
                          JOIN SICA.Cat_Pisos catpi ON catpi.id = vs.PisoReceptor
                           LEFT JOIN SICA.Cat_Edificios ce ON ce.id = vs.IdEdificio
                         LEFT JOIN SICA.Cat_Entradas_Edi cee ON cee.id = vs.IdAcceso
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

            } elseif ($type == 10) {
                $OBJ = Visitum::find($request->CHID);
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->Finalizado = 1;
                $OBJ->save();
                $response = $OBJ;

            } elseif ($type == 11) {

                $data = $this->dataNotificacion($request->CHID);
                $qr = QrCode::format('png')->size(200)->generate($request->CHID);
                $rutaTemporal = storage_path('app/temp/qr.png');
                file_put_contents($rutaTemporal, $qr);
                $correo = $request->EmailNotificacion;
                Mail::send('notificacioEntrega', ['data' => $data[0]], function ($message) use ($rutaTemporal, $correo) {
                    $message->to($correo)
                        ->subject('Notificación de Visita');
                    $message->attach($rutaTemporal);
                });

                unlink($rutaTemporal);

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

    public function bitacora(Request $request)
    {

        $SUCCESS = true;
        $NUMCODE = 0;
        $STRMESSAGE = 'Exito';
        $response = "";

        try {

            $query = "
                       SELECT
                         visb.FechaCreacion,
                         getUserName(visb.ModificadoPor) usuario,
                         est.Descripcion estatus
                       FROM
                           SICA.Visita vis
                           INNER JOIN SICA.VisitaBitacora visb ON vis.id = visb.IdVisita
                           INNER JOIN SICA.Cat_Estatus est ON visb.IdEstatus = est.id
                       WHERE
                           vis.id = :visId
                       ORDER BY visb.FechaCreacion
                   ";

            $response = DB::select($query, ['visId' => $request->CHID]);

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
