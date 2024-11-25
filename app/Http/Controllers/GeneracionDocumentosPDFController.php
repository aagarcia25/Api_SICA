<?php

namespace App\Http\Controllers;

use App\Services\FormatosPDFService;

use Illuminate\Http\Request;
use App\Models\Estudiante;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

use Illuminate\Support\Facades\Log;

class GeneracionDocumentosPDFController extends Controller
{
    public function makeQrEstudiante(Request $req)
    {
        $estudiante = Estudiante::find($req->input('id'));

        if (!$estudiante) {
            return response()->json(['error' => 'Estudiante no encontrado'], 404);
        }

        $outputFormat = $req->input('output_format', 'pdf'); // 'pdf' por defecto, o 'qr'

        if ($outputFormat === 'qr') {
            return $this->generateQrCode($estudiante);
        }

        return $this->generatePdf($estudiante);
    }

    /**
     * Generar solo el QR Code como archivo PNG
     */
    private function generateQrCode($estudiante)
    {
        // Crear el QR usando el Builder
        $result = Builder::create()
            ->writer(new PngWriter()) // Configurar el escritor para PNG
            ->data($estudiante->id)   // Contenido del QR
            ->encoding(new Encoding('UTF-8')) // Codificación UTF-8
            ->errorCorrectionLevel(ErrorCorrectionLevel::High) // Nivel de corrección de errores
            ->size(300)              // Tamaño del QR
            ->margin(10)             // Márgenes alrededor del QR
            ->build();

        // Generar el nombre del archivo usando el nombre del estudiante
        $sanitizedStudentName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $estudiante->Nombre);
        $fileName = 'QR_' . $sanitizedStudentName . '.png';

        // Retornar el archivo como respuesta
        return response($result->getString(), 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ]);
    }

    /**
     * Generar PDF con la credencial del estudiante
     */
    private function generatePdf($estudiante)
    {
        $pdfService = app(FormatosPDFService::class);

        $data = [
            'qr' => $estudiante->id,
            'nombre' => $estudiante->Nombre,
            'unidadAdministrativa' => $estudiante->UnidadAdministrativa,
            'programa' => $estudiante->TipoEstudiante,
            'fechaInicio' => $estudiante->FechaInicio->format('d/m/Y'),
            'fechaFin' => $estudiante->FechaFin->format('d/m/Y'),
            'horario' => '10:00 - 14:00',
            'foto' => '/path/to/profile/picture.jpg',
        ];

        $viewName = 'qrEstudiante';
        $fileName = 'QR_Estudiante_' . $estudiante->id;

        return $pdfService->generarPDF($data, $viewName, $fileName, 'A4', 'portrait');
    }
}
