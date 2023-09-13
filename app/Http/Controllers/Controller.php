<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function buscamsg(string $code, $mensaje)
    {
        $valor = "";
        switch ($code) {
            case "23000":
                $valor = "Valor duplicado ya existe, por favor verifique";
                break;
            case "42S02":
                $valor = "Tabla no Existe, contacte a soporte código: 42S02";
                break;
            default:
                $valor = $mensaje;

        }

        return $valor;
    }

}
