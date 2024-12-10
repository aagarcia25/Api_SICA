<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



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
	ent.Id, 
	ent.Nombre AS NombreEntidad,
	    DATE_FORMAT(vb.FechaEntrada, '%e/%M') AS DiaMesEntrada, -- Día/mes de entrada
	    COALESCE(DATE_FORMAT(vb.FechaEntrada, '%r'), NULL) AS HoraEntrada, -- Hora de entrada
	    DATE_FORMAT(vb.FechaSalida, '%e/%M') AS DiaMesSalida,  -- Día/mes de salida
    COALESCE(DATE_FORMAT(vb.FechaSalida, '%r'), NULL) AS HoraSalida, -- Hora de salida
     ROUND(
            TIMESTAMPDIFF(HOUR, v.FechaEntrada, v.FechaSalida) +
            (TIMESTAMPDIFF(MINUTE, v.FechaEntrada, v.FechaSalida) % 60) / 100,
        2) AS DuracionHoras -- Duración en horas
FROM Estudiantes e
LEFT JOIN VisitaBitacora vb ON e.id = vb.IdVisita
LEFT JOIN TiCentral.Entidades ent ON e.IdEntidad=ent.Id
WHERE e.deleted = 0 
                         ";

            if ($request->idEstudiante != "") {
                $query = $query . " and   e.id='" . $request->idEstudiante . "'";
            }
            if ($request->idUnidadAdministrativa != "") {
                $query = $query . " and   ent.Id='" . $request->idUnidadAdministrativa . "'";
            }

            if ($request->fInicioFiltro != "" && $request->fFinFiltro != "") {
                // Si ambas fechas de inicio y fin están presentes, buscamos registros dentro del rango
                $query = $query . " AND DATE(vb.FechaEntrada) BETWEEN '" . $request->fInicioFiltro . "' AND '" . $request->fFinFiltro . "'";
            } elseif ($request->fInicioFiltro != "" || $request->fFinFiltro != "") {
                // Si solo una de las fechas (Inicio o Fin) está presente, buscamos registros solo para ese día
                $fechaFiltro = $request->fInicioFiltro != "" ? $request->fInicioFiltro : $request->fFinFiltro;
                $query = $query . " AND DATE(vb.FechaEntrada) = '" . $fechaFiltro . "'";
            }
            //  if ($request->idUnidadAdministrativa != "") {
            //         $query = $query . " and    e.Nombre='" . $request->idUnidadAdministrativa . "'";
            //     }
            //  if ($request->fInicioFiltro != "") {
            //         $query = $query . " and    e.FechaInicio='" . $request->fInicioFiltro . "'";
            //  }if ($request->fFinFiltro != "") {
            //         $query = $query . " and    e.FechaFin='" . $request->fFinFiltro . "'";
            //     }

            LOG::info($query);
            $dataSheet1 = DB::select($query);
            LOG::info($dataSheet1);

            $count = 3;
            for ($i = 0; $i < count($dataSheet1); ++$i) {
                $sheet1->setCellValue('A' . $count, $dataSheet1[$i]->NombreEstudiante);
                $sheet1->setCellValue('B' . $count, $dataSheet1[$i]->NombreEntidad);
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
            // Define la ruta
            $folderPath = $_SERVER['DOCUMENT_ROOT'] . '/reportes/temp';

            // Verifica si la carpeta no existe
            if (!is_dir($folderPath)) {
                // Si no existe, crea la carpeta (recursivamente si es necesario)
                mkdir($folderPath, 0777, true); // 0777 da permisos completos, true permite crear carpetas padres si no existen
            }

            // Ahora guarda el archivo en la ruta asegurada
            $writer->save($folderPath . '/ReporteGeneralEstudiantes.xlsx');
            $returninput = public_path() . '/reportes/temp/ReporteGeneralEstudiantes.xlsx';


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
