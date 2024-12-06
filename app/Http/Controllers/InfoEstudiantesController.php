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
	e.id,
	e.Nombre AS NombreEstudiante,
	e.PersonaResponsable AS PersonaResponsable,
	e.HorarioDesde AS HorarioDesde,
	e.HorarioHasta AS HorarioHasta,
	e.FechaInicio AS FechaInicio,
	e.FechaFin AS FechaFin,
	    DATE_FORMAT(vb.FechaEntrada, '%e/%M') AS DiaMesEntrada, -- Día/mes de entrada
	    COALESCE(DATE_FORMAT(vb.FechaEntrada, '%r'), NULL) AS HoraEntrada, -- Hora de entrada
	    DATE_FORMAT(vb.FechaSalida, '%e/%M') AS DiaMesSalida,  -- Día/mes de salida
    COALESCE(DATE_FORMAT(vb.FechaSalida, '%r'), NULL) AS HoraSalida, -- Hora de salida
    ROUND(COALESCE(TIMESTAMPDIFF(SECOND, vb.FechaEntrada, vb.FechaSalida), 0) / 3600, 2) AS DuracionHoras -- Duración en horas
FROM Estudiantes e
LEFT JOIN VisitaBitacora vb ON e.id = vb.IdVisita
WHERE e.deleted = 0 
                         ";
            $dataSheet1 = DB::select($query);
            $count = 3;
            for ($i = 0; $i < count($dataSheet1); ++$i) {
                $sheet1->setCellValue('A' . $count, $dataSheet1[$i]->NombreEstudiante);
                //$sheet1->setCellValue('B' . $count, $dataSheet1[$i]->IdEntidad);
                $sheet1->setCellValue('C' . $count, $dataSheet1[$i]->PersonaResponsable);
                $sheet1->setCellValue('D' . $count, $dataSheet1[$i]->HorarioDesde);
                $sheet1->setCellValue('E' . $count, $dataSheet1[$i]->HorarioHasta);
                $sheet1->setCellValue('F' . $count, $dataSheet1[$i]->DiaMesEntrada);
                $sheet1->setCellValue('G' . $count, $dataSheet1[$i]->HoraEntrada);
                $sheet1->setCellValue('H' . $count, $dataSheet1[$i]->DiaMesSalida);
                $sheet1->setCellValue('I' . $count, $dataSheet1[$i]->HoraSalida);
                $sheet1->setCellValue('J' . $count, $dataSheet1[$i]->DuracionHoras);



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
