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
        $correo = new notificacion();

        Mail::to('aagarcia@cecapmex.com.mx')->send($correo);

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
