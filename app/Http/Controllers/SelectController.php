<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SelectController extends Controller
{
    public function SelectIndex(Request $request)
    {

        $SUCCESS = true;
        $NUMCODE = 0;
        $STRMESSAGE = 'Exito';
        $response = "";
        try {

            $type = $request->NUMOPERACION;
            $query = "";

            if ($type == 1) {
                $query = "SELECT id  value , Descripcion label FROM TiCentral.TipoEntidades WHERE DELETED=0";
            } elseif ($type == 2) {
                $query = "SELECT id  value , Nombre label FROM TiCentral.Entidades WHERE DELETED=0";
                $query = $query . " and IdTipoEntidad='" . $request->P_ID . "'";

            } elseif ($type == 3) {
                $query = "      SELECT 1  value , '1 Hora' label FROM DUAL
                                UNION ALL
                                SELECT 2  value , '2 Hora' label FROM DUAL
                                UNION ALL
                                SELECT 3  value , '3 Hora' label FROM DUAL
                                UNION ALL
                                SELECT 4  value , '4 Hora' label FROM DUAL
                                UNION ALL
                                SELECT 5  value , '5 Hora' label FROM DUAL
                                UNION ALL
                                SELECT 6  value , '6 Hora' label FROM DUAL

                                ";
            } elseif ($type == 4) {
                $query = "SELECT id  value , Descripcion label FROM SICA.Cat_TipoAcceso WHERE DELETED=0";

            } elseif ($type == 5) {
                $query = "SELECT id  value , Descripcion label FROM SICA.Cat_Pisos ORDER BY FechaCreacion";

            } elseif ($type == 6) {
                $query = "SELECT id  value , Nombre label FROM TiCentral.Entidades WHERE DELETED=0";

            } elseif ($type == 7) {

                $query = "
                           SELECT ce.id value, ce.descripcion label FROM SICA.cat_edificios ce
                           INNER JOIN SICA.usuario_edificio ue ON ce.id = ue.IdEdificio
                          WHERE ue.deleted=0 ";
                $query = $query . " and ue.idUsuario='" . $request->P_ID . "'";

            } elseif ($type == 8) {
                $query = "
                      SELECT cee.id value , cee.descripcion label FROM SICA.cat_entradas_edi cee
                      INNER JOIN SICA.cat_edificios ce ON ce.id = cee.idEdificio
                      WHERE cee.deleted=0";
                $query = $query . " and ce.id='" . $request->P_ID . "'";
            }

            $response = DB::select($query);
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
