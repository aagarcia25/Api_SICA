<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class GraficasController extends Controller
{
    //
    public function graficas(Request $request)
    {
        $SUCCESS = true;
        $NUMCODE = 0;
        $STRMESSAGE = 'Exito';
        $response = "";

        try {

            switch ($request->nivel) {

                case '0':
                    $query = "
                    SELECT 
                    'Visitas' AS Clasificacion,
                                COUNT(CASE WHEN v.Express = 1 THEN v.id END) AS 'Generado por Recepción', 
                                COUNT(CASE WHEN v.Express = 0 THEN v.id END) AS 'Generado con QR'
                                FROM SICA.Visita v
                                where
                                v.deleted = 0
                                
                        ";
                    $response = DB::select($query);

                    break;

                default:
                    $response = "No se Encuentra configurado para la migración";
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
}
