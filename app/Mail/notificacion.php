<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class notificacion extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function build()
    {

        $query = "
                     SELECT
                      vs.id,
	                  vs.FechaVisita,
                      vs.Duracion,
	                  CONCAT(ce.Calle, ' ',ce.Colonia,' ',ce.CP , '',ce.Municipio) Direccion,
	                  CONCAT(vs.NombreReceptor, ' ',vs.ApellidoPReceptor,' ',vs.ApellidoMReceptor ) receptor,
                      CONCAT(vs.NombreVisitante, ' ',vs.ApellidoPVisitante,' ',vs.ApellidoMVisitante ) visitante,
	                  en2.Nombre entidadreceptor,
	                  catpi.Descripcion pisoreceptorrr,
                      ce.Descripcion edificio
                      FROM SICA.Visita vs
                      LEFT JOIN TiCentral.Entidades en  ON vs.idEntidad = en.Id
                      LEFT JOIN TiCentral.Entidades en2  ON vs.IdEntidadReceptor = en2.Id
                      LEFT JOIN SICA.Cat_Pisos catpi ON catpi.id = vs.PisoReceptor
                      LEFT JOIN SICA.Cat_Edificios ce ON ce.id = vs.IdEdificio
                      LEFT JOIN SICA.Cat_Entradas_Edi cee ON catpi.id = vs.IdAcceso
                      where vs.deleted =0
                    ";
        $query = $query . " and vs.id='" . $this->params . "'";
        $OBJ = DB::select($query);
        return $this->view('notificacioEntrega')->with('data' => $OBJ[0]); // La vista que creaste anteriormente
    }
}
