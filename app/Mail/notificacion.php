<?php
namespace App\Mail;

use App\Models\Visitum;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class notificacion extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function build()
    {

        $OBJ = Visitum::find($this->params);
        return $this->view('notificacioEntrega')->with('data', $OBJ); // La vista que creaste anteriormente
    }
}
