<?php

namespace App\Http\Controllers;

use App\Models\Visitum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class InfoVisitasController extends Controller
{
    //
    public function handleReport(Request $request)
    {
        $SUCCESS = true;
        $NUMCODE = 0;
        $STRMESSAGE = 'Exito';
        $response64 = '';

        try {
            $obj = new \stdClass();
            $input = public_path() . '/reportes/informe.xlsx';
            $book = \PhpOffice\PhpSpreadsheet\IOFactory::load($input);
            $sheet1 = $book->getSheetByName('Sheet1');

            //$auditoria = Auditorium::find($request->CHID);

            $query = "
            SELECT 
            DATE(vi.FechaVisita) AS FechaVisita,
            en.Nombre,
            COUNT(vi.IdEntidadReceptor) AS VisitasPorDia
        FROM SICA.Visita vi
        INNER JOIN TiCentral.Entidades en ON vi.IdEntidadReceptor = en.Id
        WHERE vi.deleted = 0 
        
        GROUP BY DATE(vi.FechaVisita), en.Nombre;
                         ";
            $dataSheet1 = DB::select($query);
            $count = 3;
            for ($i = 0; $i < count($dataSheet1); ++$i) {
                $sheet1->setCellValue('A' . $count, $dataSheet1[$i]->FechaVisita);
                $sheet1->setCellValue('B' . $count, $dataSheet1[$i]->Nombre);
                $sheet1->setCellValue('C' . $count, $dataSheet1[$i]->VisitasPorDia);
                ++$count;
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($book);
            $writer->setOffice2003Compatibility(true);
            $writer->save($_SERVER['DOCUMENT_ROOT'] . '/reportes/informe.xlsx');
            $returninput = public_path() . '/reportes/informe.xlsx';

            $fragmentos = explode('.', $returninput);
            $obj->extencion = $fragmentos[1];
            $response = file_get_contents($returninput);
            $response64 = base64_encode($response);
            $obj->response64 = $response64;
        } catch (\Exception $e) {
            info($e->getMessage());
            $NUMCODE = 1;
            $STRMESSAGE = $e->getMessage();
            $SUCCESS = false;
        }

        return response()->json(
            [
                'NUMCODE' => $NUMCODE,
                'STRMESSAGE' => $STRMESSAGE,
                'RESPONSE' => $obj,
                'SUCCESS' => $SUCCESS,
            ]
        );
    }
}
