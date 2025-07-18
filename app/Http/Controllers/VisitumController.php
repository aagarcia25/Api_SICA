<?php

namespace App\Http\Controllers;

use App\Models\Visitum;
use App\Traits\ReportTrait;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Dompdf\Options;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;

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
	                    DATE_FORMAT(vs.FechaVisita, '%Y-%b-%d %h:%i  %p') as FechaVisita,
                        CONCAT(vs.Duracion, ' Horas') AS Duracion ,
                        CONCAT_WS(' ', ce.Calle, ce.Colonia, ce.CP, ce.Municipio) AS Direccion,
                        CONCAT_WS(' ', vs.NombreReceptor, vs.ApellidoPReceptor, vs.ApellidoMReceptor) AS receptor,
                        CONCAT_WS(' ', vs.NombreVisitante, vs.ApellidoPVisitante, vs.ApellidoMVisitante) AS visitante,
                        en2.Nombre entidadreceptor,
                        catpi.Descripcion pisoreceptorrr,
                        ce.Descripcion edificio,
                        vs.Observaciones AS observaciones
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

                $validacion = true;
                if ($request->Indefinido == 1) {
                    $cantidad = $this->ValidaCantidadIndefinidos($request->idEntidadReceptor);
                    if ($cantidad >= 5) {
                        throw new Exception("El Área No Puede Tener Más de 5 QR Sin Vigencia");
                    }
                }

                if ($validacion) {

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
                        $data = $this->dataNotificacion($idgenerado);

                        if (!empty($correo)) {
                            $this->enviarNotificacionVisita($data, $correo);
                        }

                        $objresul = Visitum::find($idgenerado);
                    }

                    $response = $objresul;
                }
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
                         AND vs.Finalizado = 0
                         AND vs.Cancelado = 0
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
                         AND vs.Finalizado = 0
                         AND vs.Cancelado = 0
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
                ini_set('memory_limit', '512M');
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
                       vs.Extencion,
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
                        vs.Express,
                        vs.Cancelado,
                        vs.Observaciones,
                        vs.EmailNotificacion,
                     
                        case
                        	when vs.Indefinido = 0 then 'Con Vigencia'
                        	when vs.Indefinido = 1 then 'Sin Vigencia'
                        END AS Indefinido,
                        ten.Descripcion tenDescripcion,
                        ten.Id tenId,
                        ed.id eddid,
                        ed.Descripcion edDescripcion,
                        ceed.id taid,
                        ceed.Descripcion ceedDescripcion,
                        cta.id ctaid,
                        cta.Descripcion ctaDescripcion
                        FROM SICA.Visita vs
                        LEFT JOIN TiCentral.Entidades en  ON vs.idEntidad = en.Id
                        LEFT JOIN TiCentral.Entidades en2  ON vs.IdEntidadReceptor = en2.Id
                        LEFT JOIN TiCentral.TipoEntidades ten ON vs.idTipoentidad = ten.Id
                        LEFT JOIN SICA.Cat_Pisos catpi ON catpi.id = vs.PisoReceptor
                        LEFT JOIN SICA.Cat_Edificios ed ON vs.IdEdificio = ed.id
                        LEFT JOIN SICA.Cat_Entradas_Edi ceed ON vs.IdAcceso = ceed.id
                        LEFT JOIN SICA.Cat_TipoAcceso cta ON vs.IdTipoAcceso = cta.id
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
                $data = $this->dataNotificacion($request->CHID);
                $correo = $request->EmailNotificacion;
                if (!empty($correo)) {
                    $this->enviarNotificacionVisita($data, $correo);
                }
                //unlink($rutaTemporal);
            } elseif ($type == 12) {
                $data = $this->dataNotificacion($request->CHID);
                $pdfBinary = $this->pdfBinary($data);
                $response = base64_encode($pdfBinary);
                //unlink($rutaTemporal);
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
            } elseif ($type == 16) {

                $query = "SELECT DISTINCT 
                          vs.NombreVisitante,
                          vs.ApellidoPVisitante,
                          vs.ApellidoMVisitante
                 FROM SICA.Visita vs     
                          WHERE vs.IdTipoAcceso = 'f751513c-528e-11ee-b06d-3cd92b4d9bf4'
                          AND vs.Finalizado = 1                             
                          AND vs.NombreVisitante LIKE ?";
                $response = DB::select($query, ['%' . $request->NombreVisitante . '%']);
            } elseif ($type == 17) {
                $query = "SELECT DISTINCT vs.* FROM SICA.Visita vs     
                          WHERE vs.IdTipoAcceso = 'f751513c-528e-11ee-b06d-3cd92b4d9bf4'
                          AND vs.Finalizado = 1
                          AND vs.NombreVisitante = ? 
                          ";

                $bindings = [$request->NombreVisitante];

                // Verificar y agregar ApellidoPVisitante
                if (!is_null($request->ApellidoPVisitante)) {
                    $query .= " AND vs.ApellidoPVisitante = ?";
                    $bindings[] = $request->ApellidoPVisitante;
                }

                // Verificar y agregar ApellidoMVisitante
                if (!is_null($request->ApellidoMVisitante)) {
                    $query .= " AND vs.ApellidoMVisitante = ?";
                    $bindings[] = $request->ApellidoMVisitante;
                }

                $query .= " ORDER BY vs.FechaCreacion DESC LIMIT 1";
                info($query);
                $response = DB::select($query, $bindings);
            } elseif ($type == 18) {
                $query = "
                SELECT 
                COUNT(*) as contador
                FROM SICA.Visita v
                WHERE v.Cancelado = 1
                ";
                $response = DB::select($query);
                $response = $response[0];
            } elseif ($type == 19) {
                $query = "
                SELECT 
                COUNT(*) as contador
                FROM SICA.Visita v
                WHERE v.deleted = 0
                ";
                $response = DB::select($query);
                $response = $response[0];
            } elseif ($type == 20) {
                switch ($request->nivel) {
                    case '0':
                        $query = "
                SELECT 
                COUNT(*) as contador
                FROM SICA.Visita v
                WHERE v.deleted = 0
                ";
                        $response = DB::select($query);
                        $response = $response[0];
                        break;
                    default:
                        $response = "No se Encuentra configurado para la migración";
                }
            } elseif ($type == 21) {

                $query = "
                      SELECT
                      DATE(vs.FechaVisita) as fecha,
                      COUNT(vs.id) as cantidad_visitas
                  FROM SICA.Visita vs
                  WHERE vs.deleted = 0
                  AND vs.Finalizado = 0
                  AND vs.Cancelado = 0
                  GROUP BY DATE(vs.FechaVisita)
                  ORDER BY fecha;
                ";
                $response = DB::select($query);
            } elseif ($type == 22) {
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
                        vs.Express,
                        vs.Cancelado,
                        vs.Observaciones
                        FROM SICA.Visita vs
                        LEFT JOIN TiCentral.Entidades en  ON vs.idEntidad = en.Id
                        LEFT JOIN TiCentral.Entidades en2  ON vs.IdEntidadReceptor = en2.Id
                        LEFT JOIN SICA.Cat_Pisos catpi ON catpi.id = vs.PisoReceptor
                        Where vs.deleted =0
                      
                    ";
                $query = $query . " and vs.CreadoPor='" . $request->CHID . "'";
                $query = $query . "  order by vs.FechaCreacion desc";
                $response = DB::select($query);
            } elseif ($type == 23) {
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
                        vs.Express,
                        vs.Cancelado,
                        vs.Observaciones
                        FROM SICA.Visita vs
                        LEFT JOIN TiCentral.Entidades en  ON vs.idEntidad = en.Id
                        LEFT JOIN TiCentral.Entidades en2  ON vs.IdEntidadReceptor = en2.Id
                        LEFT JOIN SICA.Cat_Pisos catpi ON catpi.id = vs.PisoReceptor
                        Where vs.deleted =0
                        and vs.Indefinido=1
                     
                    ";
                if (!$request->ROL) {
                    $query = $query . " and vs.IdEntidadReceptor='" . $request->IDENTIDAD . "'";
                }

                $query = $query . "  order by vs.FechaCreacion desc";
                $response = DB::select($query);
            } elseif ($type == 24) {
                $query = "SELECT 
    UUID() AS id,
    COUNT(1) AS Cantidad,
    en.Nombre
FROM SICA.Visita vs
INNER JOIN TiCentral.Entidades en ON vs.IdEntidadReceptor = en.Id
WHERE vs.Indefinido = 0 AND vs.Express = 0
GROUP BY vs.IdEntidadReceptor, en.Nombre
ORDER BY COUNT(1) DESC;
            ";
            
                $response = DB::select($query,[]);
            }elseif ($type == 25) { //este hace que cuando sea el rol de administrador general se muestren todas las visitas

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
                         AND vs.Finalizado = 0
                         AND vs.Cancelado = 0
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
                         AND vs.Finalizado = 0
                         AND vs.Cancelado = 0
                    ";
                    
                }


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

    public function ValidaCantidadIndefinidos($idEntidad)
    {
        $query = "
                      SELECT COUNT(1) AS Cantidad
                      FROM SICA.Visita vs 
                      INNER JOIN TiCentral.Entidades en  ON vs.IdEntidadReceptor = en.Id
                      WHERE  vs.Indefinido=1 AND vs.deleted=0 AND vs.IdEntidadReceptor=:identidad
                   ";
        $response = DB::select($query, ['identidad' => $idEntidad]);
        return $response[0]->Cantidad ?? 0;
    }

    public function enviarNotificacionVisita(array $data, string $correo)
    {
        $pdfBinary = $this->pdfBinary($data);
        $row  = (array) $data[0];
        Mail::send(           
            'notificacioEntrega',           
            ['data' => $row],
            function ($message) use ($pdfBinary, $correo, $row) {
                $message->to($correo)
                        ->subject('Notificación de Visita')
                        ->attachData(
                            $pdfBinary,
                            'visita-' . $row['id'] . '.pdf',
                            ['mime' => 'application/pdf']
                        );
            }
        );
    }

    public function pdfBinary(array $data)
    {
        $row  = (array) $data[0];
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Arial');
        $options->set('backend', 'GD'); // ⚠️ clave para evitar el error de Imagick

        $pdf = new \Dompdf\Dompdf($options);

        $html = view('visita', ['data' => $row])->render();

        $pdf->loadHtml($html);
        $pdf->setPaper('letter');
        $pdf->render();

        $pdfBinary = $pdf->output(); 
        return $pdfBinary;
    }
}
