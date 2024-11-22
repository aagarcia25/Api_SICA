<?php

namespace App\Http\Controllers;

use App\Services\FormatosPDFService;

use Illuminate\Http\Request;
use App\Models\Estudiante;


use Illuminate\Support\Facades\Log;

class GeneracionDocumentosPDFController extends Controller
{
    public function makeQrEstudiante(Request $req)
    {
        $estudiante = Estudiante::find($req->input('id'));

        if (!$estudiante) {
            return response()->json(['error' => 'Estudiante no encontrado'], 404);
        }

        $pdfService = app(FormatosPDFService::class);

        $data = [
            'qr' => $estudiante->id,
            'nombre' => $estudiante->Nombre,
            'unidadAdministrativa' => $estudiante->UnidadAdministrativa,
            'programa' => $estudiante->TipoEstudiante,
            'fechaInicio' => $estudiante->FechaInicio->format('d/m/Y'),
            'fechaFin' => $estudiante->FechaFin->format('d/m/Y'),
            'horario' => '10:00 - 14:00', // Ajusta el horario según tus necesidades
            'foto' => '/path/to/profile/picture.jpg', // Ruta o URL de la foto del estudiante
        ];

        $viewName = 'qrEstudiante';
        $fileName = 'QR_Estudiante_' . $estudiante->id;

        return $pdfService->generarPDF($data, $viewName, $fileName, 'A4', 'portrait');
    }
}
