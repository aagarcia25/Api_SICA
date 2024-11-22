<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

use Log;

class FormatosPDFService
{

    public function generarPDF($data, $viewName, $fileName, $paper, $orientation)
    {

        view()->share('data', $data);

        $pdf = Pdf::setPaper($paper, $orientation)->loadView($viewName);

        return $pdf->download($fileName . '.pdf');
    }
}
