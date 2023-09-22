<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class notificacion extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->view('notificacioEntrega'); // La vista que creaste anteriormente
    }
}
