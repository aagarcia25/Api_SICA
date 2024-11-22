<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\EstudiantesImport;
use App\Imports\EstudiantesImportv2;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class MigraDataController extends Controller
{
    public function ValidaServicio(Request $request)
    {

        $SUCCESS = true;
        $NUMCODE = 0;
        $STRMESSAGE = 'Exito';
        $response = "Servicio Activo";


        return response()->json(
            [
                'NUMCODE' => $NUMCODE,
                'STRMESSAGE' => $STRMESSAGE,
                'RESPONSE' => $response,
                'SUCCESS' => $SUCCESS,

            ]
        );
    }

    public function migraData(Request $request)
    {
        //log debug entra a esta funcion
        Log::debug('Entra a migraData');
        $SUCCESS = true;
        $NUMCODE = 0;
        $STRMESSAGE = 'Exito';
        $response = "";

        try {

            switch ($request->tipo) {

                case 'migraEstudiantes':
                    //log debug entra a este case
                    Log::debug('Entra a migraEstudiantes');

                    Excel::import(new EstudiantesImportv2($request->CHUSER), request()->file('inputfile'), ExcelExcel::XLS);
                    break;
                    // case 'migraAuditorias':
                    //     Excel::import(new AuditoriaImport($request->CHUSER), request()->file('inputfile'), ExcelExcel::XLS);
                    //     break;


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
