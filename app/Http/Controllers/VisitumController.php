<?php

namespace App\Http\Controllers;

use App\Models\Visitum;
use App\Traits\ReportTrait;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class VisitumController extends Controller
{
    use ReportTrait;

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
	                  CONCAT(ce.Calle, ' ',ce.Colonia,' ',ce.CP , ' ',ce.Municipio) Direccion,
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

    public function formatoNotificacion($id)
    {

        try {

            $format = ['pdf'];
            $params = [
                "P_IMAGEN" => public_path() . '/img/TesoreriaLogo.png',
                "P_ID" => $id,
            ];
            $reporte = 'QR.jrxml';
            $reponse = $this->ejecutaReporte($format, $params, $reporte)->getData();
            // var_dump($reponse);
        } catch (\Exception $e) {
            $e->getMessage();
        }
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
                $OBJ->Extencion = $request->Extencion;
                $OBJ->Indefinido = $request->Indefinido;
                $OBJ->Observaciones = $request->Observaciones;

                if ($OBJ->save()) {
                    shell_exec('git stash');
                    shell_exec('git stash drop');
                    $data = $this->dataNotificacion($idgenerado);
                    $this->formatoNotificacion($idgenerado);
                    $rutaTemporal = public_path() . '/reportes/QR.pdf';

                    $correo = $request->EmailNotificacion;
                    Mail::send('notificacioEntrega', ['data' => $data[0]], function ($message) use ($rutaTemporal, $correo) {
                        $message->to($correo)
                            ->subject('Notificación de Visita');
                        $message->attach($rutaTemporal);
                    });

                    // unlink($rutaTemporal);

                    $objresul = Visitum::find($idgenerado);
                } else {
                }

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
                $OBJ->Extencion = $request->Extencion;
                $OBJ->Indefinido = $request->Indefinido;
                $OBJ->Observaciones = $request->Observaciones;

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
                                                  cee.id idAcceso,
                         vs.Extencion,
                         vs.Indefinido,
                         vs.Observaciones
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
                if (is_null($OBJ->FechaSalida)) {
                    if (is_null($OBJ->FechaEntrada)) {
                        $OBJ->FechaEntrada = now();
                        $OBJ->IdEstatus = "4112a976-5183-11ee-b06d-3cd92b4d9bf4";
                    } else {
                        $OBJ->FechaSalida = now();
                        $OBJ->IdEstatus = "0779435b-5718-11ee-b06d-3cd92b4d9bf4";
                    }
                }

                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->save();
                $response = $OBJ;
            } elseif ($type == 7) {

                if ($request->ROL) {
                    $query = "
                    SELECT
                             vs.id,
                             vs.IdEstatus as estatus,
                             JSON_OBJECT(
                                 'visitante', JSON_OBJECT(
                                     'nombre', vs.NombreVisitante,
                                     'apellidoP', vs.ApellidoPVisitante,
                                     'apellidoM', vs.ApellidoMVisitante,
                                     'origen',en.Nombre
                                 ),
                                 'receptor', JSON_OBJECT(
                                     'nombre', vs.NombreReceptor,
                                     'apellidoP', vs.ApellidoPReceptor,
                                     'apellidoM', vs.ApellidoMReceptor,
                                     'UnidadOperativa',en2.Nombre
                                 )
                                  
                                
                             ) as title,
                             vs.FechaVisita as start,
                             DATE_ADD(vs.FechaVisita, INTERVAL vs.Duracion HOUR) as end,
                             CASE
                                 WHEN vs.FechaVisita > NOW() THEN '#AF8C55'
                                 WHEN vs.FechaVisita < NOW() THEN '#EC7063'
                                 ELSE 'blue'
                             END as color
                         FROM SICA.Visita vs
                         LEFT JOIN TiCentral.Entidades en  ON vs.idEntidad = en.Id
                         LEFT JOIN TiCentral.Entidades en2  ON vs.IdEntidadReceptor = en2.Id
                         WHERE vs.deleted = 0
                         AND vs.FechaSalida IS NULL

                                AND vs.CreadoPor NOT IN (
                                                         SELECT distinct us.id FROM
                                                         TiCentral.Usuarios us
                                                         INNER JOIN TiCentral.UsuarioAplicacion ua on us.Id = ua.IdUsuario
                                                         INNER JOIN TiCentral.UsuarioRol ur ON ur.IdUsuario = us.Id
                                                         WHERE ua.IdApp='970c0ac7-51b5-11ee-b06d-3cd92b4d9bf4'
                                                         AND ur.IdRol='3c32e370-c151-11ee-8dee-d89d6776f970'
                                                         )
                         
                    ";
                } else {
                    $query = "
                    SELECT
                             vs.id,
                             vs.IdEstatus as estatus,
                             JSON_OBJECT(
                                 'visitante', JSON_OBJECT(
                                     'nombre', vs.NombreVisitante,
                                     'apellidoP', vs.ApellidoPVisitante,
                                     'apellidoM', vs.ApellidoMVisitante,
                                     'origen',en.Nombre
                                 ),
                                 'receptor', JSON_OBJECT(
                                     'nombre', vs.NombreReceptor,
                                     'apellidoP', vs.ApellidoPReceptor,
                                     'apellidoM', vs.ApellidoMReceptor,
                                     'UnidadOperativa',en2.Nombre
                                 )
                                  
                                
                             ) as title,
                             vs.FechaVisita as start,
                             DATE_ADD(vs.FechaVisita, INTERVAL vs.Duracion HOUR) as end,
                             CASE
                                 WHEN vs.FechaVisita > NOW() THEN '#AF8C55'
                                 WHEN vs.FechaVisita < NOW() THEN '#EC7063'
                                 ELSE 'blue'
                             END as color
                         FROM SICA.Visita vs
                         LEFT JOIN TiCentral.Entidades en  ON vs.idEntidad = en.Id
                         LEFT JOIN TiCentral.Entidades en2  ON vs.IdEntidadReceptor = en2.Id
                         WHERE vs.deleted = 0
                         AND vs.FechaSalida IS NULL
                    ";
                    $query = $query . " and vs.CreadoPor='" . $request->CHID . "'";
                    $query = $query . " and vs.IdEntidadReceptor='" . $request->IDENTIDAD . "'";
                }


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
                       and vis.Finalizado=0
                    ";
                $query = $query . " and vis.id='" . $request->CHID . "'";
                $query = $query . " AND DAY(vis.FechaVisita) = DAY(NOW())";
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
                       IFNULL(vs.ApellidoMVisitante, '') AS ApellidoMVisitante,
                       vs.idTipoentidad,
                       vs.idEntidad,
                       vs.NombreReceptor,
                       vs.ApellidoPReceptor,
                       IFNULL(vs.ApellidoMReceptor, '') AS ApellidoMReceptor,
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
                        ROUND(TIMESTAMPDIFF(MINUTE, vs.FechaEntrada, vs.FechaSalida) / 60, 2) AS tiempovisita,
                        FROM SICA.Visita vs
                        LEFT JOIN TiCentral.Entidades en  ON vs.idEntidad = en.Id
                        LEFT JOIN TiCentral.Entidades en2  ON vs.IdEntidadReceptor = en2.Id
                        LEFT JOIN SICA.Cat_Pisos catpi ON catpi.id = vs.PisoReceptor
                        Where vs.deleted =0
                        order by vs.FechaCreacion desc
                    ";
                $response = DB::select($query);
            } elseif ($type == 10) {
                $OBJ = Visitum::find($request->CHID);
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->Finalizado = 1;
                $OBJ->save();
                $response = $OBJ;
            } elseif ($type == 11) {
                shell_exec('git stash');
                shell_exec('git stash drop');
                $data = $this->dataNotificacion($request->CHID);
                $this->formatoNotificacion($request->CHID);
                $rutaTemporal = public_path() . '/reportes/QR.pdf';
                $correo = $request->EmailNotificacion;
                Mail::send('notificacioEntrega', ['data' => $data[0]], function ($message) use ($rutaTemporal, $correo) {
                    $message->to($correo)
                        ->subject('Notificación de Visita');
                    $message->attach($rutaTemporal);
                });
                unlink($rutaTemporal);
            } elseif ($type == 12) {
                shell_exec('git stash');
                shell_exec('git stash drop');
                $this->formatoNotificacion($request->CHID);
                $rutaTemporal = public_path() . '/reportes/QR.pdf';
                $response = file_get_contents($rutaTemporal);
                $response = base64_encode($response);
                unlink($rutaTemporal);
            } elseif ($type == 13) {
                date_default_timezone_set('America/Monterrey');
                $OBJ = Visitum::find($request->CHID);
                $OBJ->FechaEntrada = now();
                $OBJ->IdEstatus = "4112a976-5183-11ee-b06d-3cd92b4d9bf4";
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->save();
                $response = $OBJ;
            } elseif ($type == 14) {
                date_default_timezone_set('America/Monterrey');
                $OBJ = Visitum::find($request->CHID);
                $OBJ->FechaSalida = now();
                $OBJ->IdEstatus = "0779435b-5718-11ee-b06d-3cd92b4d9bf4";
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->Finalizado = 1;
                $OBJ->save();
                $response = $OBJ;
            } elseif ($type == 15) {
                date_default_timezone_set('America/Monterrey');
                $idgenerado = Str::uuid();
                $OBJ = new Visitum();
                $OBJ->id = $idgenerado;
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->CreadoPor = $request->CHUSER;
                $OBJ->FechaVisita = Carbon::now();
                $OBJ->Duracion = 0;
                $OBJ->IdTipoAcceso = $request->IdTipoAcceso;
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
                $OBJ->Extencion = $request->Extencion;
                $OBJ->Indefinido = 0;
                $OBJ->Observaciones = $request->Observaciones;
                $OBJ->FechaEntrada = Carbon::now();
                $OBJ->FechaSalida = Carbon::now();
                $OBJ->Finalizado = 1;
                $OBJ->Express = 1;

                if ($OBJ->save()) {
                    /*
                    if ($request->EmailNotificacion) {

                        shell_exec('git stash');
                        shell_exec('git stash drop');
                        $data = $this->dataNotificacion($idgenerado);
                        $this->formatoNotificacion($idgenerado);
                        $rutaTemporal = public_path() . '/reportes/QR.pdf';

                        $correo = $request->EmailNotificacion;
                        Mail::send('notificacioEntrega', ['data' => $data[0]], function ($message) use ($rutaTemporal, $correo) {
                            $message->to($correo)
                                ->subject('Notificación de Visita');
                            $message->attach($rutaTemporal);
                        });

                        // unlink($rutaTemporal);
                    }*/


                    $objresul = Visitum::find($idgenerado);
                } else {
                }

                $response = $objresul;
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
            ]
        );
    }
}
