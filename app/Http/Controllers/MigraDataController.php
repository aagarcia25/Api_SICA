<?php

namespace App\Http\Controllers;

use App\Mail\notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MigraDataController extends Controller
{
    public function ValidaServicio(Request $request)
    {

        $SUCCESS = true;
        $NUMCODE = 0;
        $STRMESSAGE = 'Exito';
        $response = "Servicio Activo";
        $correo = new notificacion("f69087a0-2e7c-41af-becb-37e9f8e106bb");

        Mail::to('aagarcia@cecapmex.com')->send($correo);

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
