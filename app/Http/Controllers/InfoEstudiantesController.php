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
                e.Nombre AS NombreCompleto,
                ent.Nombre AS UnidadAdministrativa,
                e.PersonaResponsable,
                e.HorarioDesde AS HorarioEntrada,
                e.HorarioHasta AS HorarioSalida,
                DATE_FORMAT(vb.FechaEntrada, '%e/%M') AS FechaAsistenciaEntrada,
                COALESCE(DATE_FORMAT(vb.FechaEntrada, '%r'), NULL) AS HoraEntrada,
                DATE_FORMAT(vb.FechaSalida, '%e/%M') AS FechaAsistenciaSalida,
                COALESCE(DATE_FORMAT(vb.FechaSalida, '%r'), NULL) AS HoraSalida,
                ROUND(
                    TIMESTAMPDIFF(HOUR, vb.FechaEntrada, vb.FechaSalida) +
                    (TIMESTAMPDIFF(MINUTE, vb.FechaEntrada, vb.FechaSalida) % 60) / 100,
                2) AS TotalHoras
            FROM Estudiantes e
            LEFT JOIN VisitaBitacora vb ON e.id = vb.IdVisita
            LEFT JOIN TiCentral.Entidades ent ON e.IdEntidad = ent.Id
            WHERE e.deleted = 0 ";
    
            if ($request->idEstudiante != "") {
                $query .= " AND e.id = '" . $request->idEstudiante . "'";
            }
            if ($request->idUnidadAdministrativa != "") {
                $query .= " AND ent.Id = '" . $request->idUnidadAdministrativa . "'";
            }
            if ($request->fInicioFiltro != "" && $request->fFinFiltro != "") {
                $query .= " AND DATE(vb.FechaEntrada) BETWEEN '" . $request->fInicioFiltro . "' AND '" . $request->fFinFiltro . "'";
            } elseif ($request->fInicioFiltro != "" || $request->fFinFiltro != "") {
                $fechaFiltro = $request->fInicioFiltro != "" ? $request->fInicioFiltro : $request->fFinFiltro;
                $query .= " AND DATE(vb.FechaEntrada) = '" . $fechaFiltro . "'";
            }
    
            LOG::info($query);
            $dataSheet1 = DB::select($query);
            LOG::info($dataSheet1);
    
            $groupedData = [];
    
            // Agrupar por nombre y sumar horas
            foreach ($dataSheet1 as $data) {
                if (!isset($groupedData[$data->NombreCompleto])) {
                    $groupedData[$data->NombreCompleto] = [
                        'UnidadAdministrativa' => $data->UnidadAdministrativa,
                        'PersonaResponsable' => $data->PersonaResponsable,
                        'HorarioEntrada' => $data->HorarioEntrada,
                        'HorarioSalida' => $data->HorarioSalida,
                        'Detalle' => [],
                        'TotalHoras' => 0
                    ];
                }
    
                $groupedData[$data->NombreCompleto]['Detalle'][] = [
                    'FechaAsistenciaEntrada' => $data->FechaAsistenciaEntrada,
                    'HoraEntrada' => $data->HoraEntrada,
                    'FechaAsistenciaSalida' => $data->FechaAsistenciaSalida,
                    'HoraSalida' => $data->HoraSalida,
                    'Horas' => $data->TotalHoras
                ];
    
                $groupedData[$data->NombreCompleto]['TotalHoras'] += $data->TotalHoras;
            }
    
            $count = 3;
            foreach ($groupedData as $nombre => $info) {
                foreach ($info['Detalle'] as $detalle) {
                    $sheet1->setCellValue('A' . $count, $nombre);
                    $sheet1->setCellValue('B' . $count, $info['UnidadAdministrativa']);
                    $sheet1->setCellValue('C' . $count, $info['PersonaResponsable']);
                    $sheet1->setCellValue('D' . $count, $info['HorarioEntrada']);
                    $sheet1->setCellValue('E' . $count, $info['HorarioSalida']);
                    $sheet1->setCellValue('F' . $count, $detalle['FechaAsistenciaEntrada']);
                    $sheet1->setCellValue('G' . $count, $detalle['HoraEntrada']);
                    $sheet1->setCellValue('H' . $count, $detalle['FechaAsistenciaSalida']);
                    $sheet1->setCellValue('I' . $count, $detalle['HoraSalida']);
                    $sheet1->setCellValue('J' . $count, $detalle['Horas']);
                    $count++;
                }
    
                // Agregar total de horas por estudiante
                $sheet1->setCellValue('I' . $count, 'TOTAL');
                $sheet1->setCellValue('J' . $count, $info['TotalHoras']);
                $count++;
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
