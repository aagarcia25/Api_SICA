<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MigraDataController extends Controller
{
    public function ValidaServicio(Request $request)
    {

        $SUCCESS = true;
        $NUMCODE = 0;
        $STRMESSAGE = 'Exito';
        $response = "Servicio Activo";

        $para = "aagarcia@cecapmex.com";
        $asunto = "Asunto del correo";
        $mensaje = "Hola, este es un correo de prueba.";

// Enviar el correo
        mail($para, $asunto, $mensaje);

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
