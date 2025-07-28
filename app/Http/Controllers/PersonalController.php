<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Personal;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use carbon\carbon;
use Illuminate\Support\Facades\Mail;

class PersonalController extends Controller
{
    //
    public function PersonalIndex(Request $request)
    {

        $SUCCESS = true;
        $NUMCODE = 0;
        $STRMESSAGE = 'Exito';
        $response = "";

        try {
            $type = $request->NUMOPERACION;

            if ($type == 1) {
                $OBJ = new Personal();
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->CreadoPor = $request->CHUSER;
                $OBJ->Nombre = $request->Nombre;
                $OBJ->ApellidoPaterno = $request->ApellidoPaterno;
                $OBJ->ApellidoMaterno = $request->ApellidoMaterno;
                $OBJ->CorreoElectronico = $request->CorreoElectronico;
                $OBJ->CURP = $request->CURP;
                $OBJ->Telefono = $request->Telefono;
                $OBJ->Ext = $request->Ext;
                $OBJ->idEntidad = $request->idEntidad;
                $OBJ->Puesto = $request->Puesto;
                $OBJ->save();
                $response = $OBJ;
            }
             elseif ($type == 2) {

                $OBJ = Personal::find($request->CHID);
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->Nombre = $request->Nombre;
                $OBJ->ApellidoPaterno = $request->ApellidoPaterno;
                $OBJ->ApellidoMaterno = $request->ApellidoMaterno;
                $OBJ->CorreoElectronico = $request->CorreoElectronico;
                $OBJ->CURP = $request->CURP;
                $OBJ->Telefono = $request->Telefono;
                $OBJ->Ext = $request->Ext;
                $OBJ->idEntidad = $request->idEntidad;
                $OBJ->Puesto = $request->Puesto;
                $OBJ->save();

                $OBJ->save();
                $response = $OBJ;

            } elseif ($type == 3) {
                $OBJ = Personal::find($request->CHID);
                $OBJ->deleted = 1;
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->save();
                $response = $OBJ;

            }elseif ($type == 4) {

                $query = "
            SELECT 
            per.id,
            per.deleted,
            per.UltimaActualizacion,
            per.FechaCreacion,
            getUserName(per.ModificadoPor) modi,
            getUserName(per.CreadoPor) creado,
            per.Nombre,
            per.ApellidoPaterno,
            per.ApellidoMaterno,
            per.CorreoElectronico,
            per.CURP,
            per.Telefono,
            per.Ext,
            per.idEntidad,
            per.Puesto,
            ent.Nombre entNombre
            FROM SICA.Personal per
            LEFT JOIN TiCentral.Entidades ent ON per.idEntidad = ent.Id
            WHERE per.deleted=0 

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
