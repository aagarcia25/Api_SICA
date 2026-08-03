<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\IOFactory;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 
class InfoVisitasController extends Controller

{

    public function handleReport(Request $request)

    {

        $SUCCESS = true;

        $NUMCODE = 0;

        $STRMESSAGE = 'Éxito';

        $obj = new \stdClass();
 
        try {

            // Plantilla original

            $input = public_path('reportes/informe.xlsx');
 
            if (!file_exists($input)) {

                throw new \Exception(

                    'No se encontró la plantilla: ' . $input

                );

            }
 
            $book = IOFactory::load($input);

            $sheet1 = $book->getSheetByName('Sheet1');
 
            if (!$sheet1) {

                throw new \Exception(

                    'No se encontró la hoja Sheet1 en informe.xlsx'

                );

            }
 
            $query = "

                SELECT

                    DATE(vi.FechaVisita) AS FechaVisita,

                    en.Nombre,

                    COUNT(vi.IdEntidadReceptor) AS VisitasPorDia

                FROM SICA.Visita vi

                INNER JOIN TiCentral.Entidades en

                    ON vi.IdEntidadReceptor = en.Id

                WHERE vi.deleted = 0

                GROUP BY DATE(vi.FechaVisita), en.Nombre

            ";
 
            $dataSheet1 = DB::select($query);

            $count = 3;
 
            foreach ($dataSheet1 as $registro) {

                $sheet1->setCellValue(

                    'A' . $count,

                    $registro->FechaVisita

                );
 
                $sheet1->setCellValue(

                    'B' . $count,

                    $registro->Nombre

                );
 
                $sheet1->setCellValue(

                    'C' . $count,

                    $registro->VisitasPorDia

                );
 
                $count++;

            }
 
            // Archivo generado dentro de la carpeta temp

            $output = public_path('reportes/temp/informe.xlsx');
 
            $writer = new Xlsx($book);

            $writer->setOffice2003Compatibility(true);

            $writer->save($output);
 
            // Convertir el archivo generado a Base64

            $response = file_get_contents($output);
 
            if ($response === false) {

                throw new \Exception(

                    'No fue posible leer el reporte generado.'

                );

            }
 
            $obj->extencion = pathinfo(

                $output,

                PATHINFO_EXTENSION

            );
 
            $obj->response64 = base64_encode($response);
 
        } catch (\Throwable $e) {

            info($e->getMessage());
 
            $NUMCODE = 1;

            $STRMESSAGE = $e->getMessage();

            $SUCCESS = false;

        }
 
        return response()->json([

            'NUMCODE' => $NUMCODE,

            'STRMESSAGE' => $STRMESSAGE,

            'RESPONSE' => $obj,

            'SUCCESS' => $SUCCESS,

        ]);

    }

}
 
