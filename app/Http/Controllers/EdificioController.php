<?php

namespace App\Http\Controllers;

use App\Models\CatEdificio;
use App\Models\CatEntradasEdi;
use App\Models\UsuarioEdificio;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EdificioController extends Controller
{
    public function Edificio_index(Request $request)
    {

        $SUCCESS = true;
        $NUMCODE = 0;
        $STRMESSAGE = 'Exito';
        $response = "";

        try {
            $type = $request->NUMOPERACION;

            if ($type == 1) {
                $OBJ = new CatEdificio();
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->CreadoPor = $request->CHUSER;
                $OBJ->Descripcion = $request->Descripcion;
                $OBJ->Calle = $request->Calle;
                $OBJ->Colonia = $request->Colonia;
                $OBJ->Municipio = $request->Municipio;
                $OBJ->Estado = $request->Estado;
                $OBJ->CP = $request->CP;
                $OBJ->save();
                $response = $OBJ;

            } elseif ($type == 2) {

                $OBJ = CatEdificio::find($request->CHID);
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->CreadoPor = $request->CHUSER;
                $OBJ->Descripcion = $request->Descripcion;
                $OBJ->Calle = $request->Calle;
                $OBJ->Colonia = $request->Colonia;
                $OBJ->Municipio = $request->Municipio;
                $OBJ->Estado = $request->Estado;
                $OBJ->CP = $request->CP;

                $OBJ->save();
                $response = $OBJ;

            } elseif ($type == 3) {
                $OBJ = CatEdificio::find($request->CHID);
                $OBJ->deleted = 1;
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->save();
                $response = $OBJ;

            } elseif ($type == 4) {
                $query = "
                     SELECT
                        eu.id,
                        eu.deleted,
                        eu.UltimaActualizacion,
                        eu.FechaCreacion,
                        getUserName(eu.ModificadoPor) ModificadoPor,
                        getUserName(eu.CreadoPor) CreadoPor,
                        eu.Descripcion,
                        eu.Calle,
                        eu.Colonia,
                        eu.Municipio,
                        eu.Estado,
                        eu.CP
                        FROM SICA.Cat_Edificios eu
                        where deleted =0
                    ";
                $response = DB::select($query);

            } elseif ($type == 5) {
                $OBJ = new CatEntradasEdi();
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->CreadoPor = $request->CHUSER;
                $OBJ->Descripcion = $request->Descripcion;
                $OBJ->idEdificio = $request->idEdificio;
                $OBJ->save();
                $response = $OBJ;

            } elseif ($type == 6) {
                $OBJ = CatEntradasEdi::find($request->CHID);
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->Descripcion = $request->Descripcion;
                $OBJ->idEdificio = $request->idEdificio;
                $OBJ->save();
                $response = $OBJ;

            } elseif ($type == 7) {
                $OBJ = CatEntradasEdi::find($request->CHID);
                $OBJ->deleted = 1;
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->save();
                $response = $OBJ;

            } elseif ($type == 9) {
                $query = "
                    SELECT
                         eu.id,
                         eu.deleted,
                         eu.UltimaActualizacion,
                         eu.FechaCreacion,
                         getUserName(eu.ModificadoPor) ModificadoPor,
                         getUserName(eu.CreadoPor) CreadoPor,
                         eu.Descripcion
                        FROM SICA.Cat_Entradas_Edi eu
                        where deleted =0
                    ";
                $query = $query . " and eu.idEdificio='" . $request->idEdificio . "'";

                $response = DB::select($query);

            } elseif ($type == 10) {
                $OBJ = new UsuarioEdificio();
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->CreadoPor = $request->CHUSER;
                $OBJ->idUsuario = $request->idUsuario;
                $OBJ->idEdificio = $request->idEdificio;
                $OBJ->save();
                $response = $OBJ;

            } elseif ($type == 11) {
                $OBJ = UsuarioEdificio::find($request->CHID);
                $OBJ->deleted = 1;
                $OBJ->ModificadoPor = $request->CHUSER;
                $OBJ->save();
                $response = $OBJ;

            } elseif ($type == 12) {

                $query = "
                  SELECT
                    eu.id,
                    eu.deleted,
                    eu.UltimaActualizacion,
                    eu.FechaCreacion,
                    getUserName(eu.ModificadoPor) ModificadoPor,
                    getUserName(eu.CreadoPor) CreadoPor,
                    getUserName(eu.idUsuario) idUsuario,
                    eu.IdEdificio
                    FROM Usuario_Edificio eu
                    where deleted =0
                    ";
                $query = $query . " and eu.IdEdificio='" . $request->idEdificio . "'";
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
