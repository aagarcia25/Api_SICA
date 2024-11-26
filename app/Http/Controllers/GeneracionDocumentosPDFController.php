<?php

namespace App\Http\Controllers;

use App\Services\FormatosPDFService;

use Illuminate\Http\Request;
use App\Models\Estudiante;

use ZipArchive;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\File;

class GeneracionDocumentosPDFController extends Controller
{
    public function makeQrEstudiante(Request $req)
    {
        $ids = $req->input('ids', []); // IDs de estudiantes (soporta uno o múltiples)
        $outputFormat = $req->input('output_format', 'pdf'); // 'pdf' por defecto, o 'qr'

        if (empty($ids)) {
            return response()->json(['error' => 'Debe proporcionar uno o más IDs de estudiantes.'], 400);
        }

        // Si hay un solo ID, genera el archivo directamente
        if (count($ids) === 1) {
            $estudiante = Estudiante::find($ids[0]);

            if (!$estudiante) {
                return response()->json(['error' => 'Estudiante no encontrado'], 404);
            }

            return $outputFormat === 'qr'
                ? $this->generateQrCode($estudiante)
                : $this->generatePdf($estudiante);
        }

        // Si son múltiples IDs, genera un ZIP con los archivos
        $files = [];
        foreach ($ids as $id) {
            $estudiante = Estudiante::find($id);

            if (!$estudiante) {
                return response()->json(['error' => "Estudiante con ID {$id} no encontrado"], 404);
            }

            $filePath = $outputFormat === 'qr'
                ? $this->generateQrFile($estudiante)
                : $this->generatePdfFile($estudiante);

            $files[] = $filePath;
        }

        // Crear ZIP con los archivos generados
        $zipFilePath = $this->createZip($files);

        // Retornar el ZIP
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    /**
     * Generar QR como respuesta (PNG)
     */
    private function generateQrCode($estudiante)
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($estudiante->id)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(300)
            ->margin(10)
            ->build();

        $sanitizedStudentName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $estudiante->Nombre);
        $fileName = 'QR_' . $sanitizedStudentName . '.png';

        return response($result->getString(), 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ]);
    }

    /**
     * Generar QR como archivo PNG
     */
    private function generateQrFile($estudiante)
    {
        $this->ensureTempDirectoryExists();

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($estudiante->id)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(300)
            ->margin(10)
            ->build();

        $filePath = storage_path("app/temp/QR_{$estudiante->Nombre}.png");
        $result->saveToFile($filePath);

        return $filePath;
    }

    private function ensureTempDirectoryExists()
    {
        $tempDir = storage_path('app/temp');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true); // Crear directorio con permisos recursivos
        }
    }


    /**
     * Generar PDF como respuesta
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
        $fileName = 'QR_' . $estudiante->Nombre;

        return $pdfService->generarPDF($data, $viewName, $fileName, 'A4', 'portrait');
    }

    /**
     * Generar PDF como archivo
     */
    public function generatePdfFile($estudiante)
    {
        $this->ensureTempDirectoryExists(); // Asegurar que el directorio temporal exista

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
        $filePath = storage_path("app/temp/PDF_{$estudiante->Nombre}.pdf");

        // Generar y guardar el PDF
        $pdfService->generarPDF($data, $viewName, basename($filePath), 'A4', 'portrait', $filePath);

        // Verificar si el archivo fue creado correctamente
        if (!file_exists($filePath)) {
            throw new \Exception("No se pudo crear el archivo PDF en la ruta: {$filePath}");
        }

        return $filePath;
    }


    /**
     * Crear un archivo ZIP con los archivos generados
     */
    private function createZip(array $files)
    {
        $zipFilePath = storage_path('app/temp/archivos.zip');
        $zip = new ZipArchive();

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Limpiar archivos temporales después de agregar al ZIP
        foreach ($files as $file) {
            File::delete($file);
        }

        return $zipFilePath;
    }
}
