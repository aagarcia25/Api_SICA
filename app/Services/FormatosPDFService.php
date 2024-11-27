<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

use Log;

class FormatosPDFService
{

    public function generarPDF($data, $viewName, $fileName, $paper, $orientation, $savePath = null)
    {
        // Compartir los datos con la vista
        view()->share('data', $data);

        // Crear el PDF
        $pdf = Pdf::setPaper($paper, $orientation)->loadView($viewName);

        // Si se proporciona una ruta, guardar el archivo en disco
        if ($savePath) {
            $pdf->save($savePath); // Método para guardar el archivo
            return $savePath; // Retornar la ruta del archivo guardado
        }

        // De lo contrario, retornar el PDF para descarga
        return $pdf->download($fileName . '.pdf');
    }
}
