<?php
namespace App\Traits;

use PHPJasper\PHPJasper;

trait ReportTrait
{

    public function ejecutaReporte($formato, $parametros, $reporte)
    {

        $response = "Reporte Generado Correctamente";
        $SUCCESS = true;

        try {

            $input = public_path() . '/reportes/' . $reporte;
            $output = public_path() . '/reportes';

            $options = [
                'format' => $formato,
                'params' => $parametros,
                'db_connection' => [
                    'driver' => env('DB_CONNECTION'),
                    'username' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'host' => env('DB_HOST'),
                    'database' => env('DB_DATABASE'),
                    'port' => env('DB_PORT'),
                ],
            ];

            $jasper = new PHPJasper;
            $jasper->process(
                $input,
                $output,
                $options
            )->execute();

        } catch (\Exception $e) {
            $jasper = new PHPJasper;
            $X = $jasper->process(
                $input,
                $output,
                $options
            )->output();

            $SUCCESS = false;
            $response = $X;
        }

        return response()->json(
            [
                'RESPONSE' => $response,
                'SUCCESS' => $SUCCESS,
            ]
        );

    }

}
