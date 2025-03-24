<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EntidadEspecial;
use App\Models\Visitum;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiDocTrait;
use Illuminate\Support\Facades\Log;

use App\Models\VisitaBitacora;

use carbon\carbon;

use Illuminate\Support\Facades\Mail;

class EntidadEspecialController extends Controller
{
    //
    public function verificarEntidadEspecial(Request $request)
    {
        // Obtener CHID desde la URL (para GET)
        $CHID = $request->query('CHID'); 
        Log::info("CHID recibido: " . json_encode($CHID));


        if (!$CHID) {
            Log::warning("No se recibió CHID en la petición.");
            return response()->json([
                'esEntidadEspecial' => false,
                'mensaje' => "No se recibió un CHID válido."
            ], 400);
        }

        // Buscar la entidad especial por ID
        //$entidad = EntidadEspecial::where('idEntidad', $CHID)->first();
        $entidad = DB::table('EntidadEspecial')
        ->join('Cat_Pisos', 'EntidadEspecial.idPiso', '=', 'Cat_Pisos.id')
        ->where('EntidadEspecial.idEntidad', $CHID)
        ->select('EntidadEspecial.*', 'Cat_Pisos.Descripcion as NombrePiso')
        ->first();

        if (!$entidad) {
            Log::info("El ID $CHID no pertenece a Entidades Especiales.");
            return response()->json([
                'esEntidadEspecial' => false,
                'mensaje' => "El ID no pertenece a una Entidad Especial."
            ]);
        }

        Log::info("El ID $CHID sí pertenece a una Entidad Especial.");

        return response()->json([
            'esEntidadEspecial' => true,
            'mensaje' => "El ID pertenece a una Entidad Especial.",
            'datos' => $entidad
        ]);
    }
}
