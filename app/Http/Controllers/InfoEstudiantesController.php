<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class InfoEstudiantesController extends Controller
{
    //
    public function ReporteGeneralEstudiantes(Request $request)
    {
        $SUCCESS = true;
        $NUMCODE = 0;
        $STRMESSAGE = 'Exito';
        $response64 = '';

        try {
            $obj = new \stdClass();
            $input = public_path() . '/reportes/ReporteGeneralEstudiantes.xlsx';
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
                $sheet1->setCellValue('A' . $count, $dataSheet1[$i]->Nombre);
                $sheet1->setCellValue('B' . $count, $dataSheet1[$i]->IdEntidad);
                $sheet1->setCellValue('C' . $count, $dataSheet1[$i]->PersonaResponsable);
                $sheet1->setCellValue('C' . $count, $dataSheet1[$i]->HorarioDesde);
                $sheet1->setCellValue('C' . $count, $dataSheet1[$i]->HorarioHasta);
                $sheet1->setCellValue('C' . $count, $dataSheet1[$i]->FechaEntrada);
                $sheet1->setCellValue('C' . $count, $dataSheet1[$i]->FechaSalida);

                ++$count;
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($book);
            $writer->setOffice2003Compatibility(true);
            $writer->save($_SERVER['DOCUMENT_ROOT'] . '/reportes/ReporteGeneralEstudiantes.xlsx');
            $returninput = public_path() . '/reportes/ReporteGeneralEstudiantes.xlsx';

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
