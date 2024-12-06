<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $estudiante;
    public $filePath;

    public function __construct($estudiante, $filePath)
    {
        $this->estudiante = $estudiante;
        $this->filePath = $filePath;
    }

    public function handle()
    {
        Log::info('Job ejecutado a las: ' . now());
        Mail::send('notificacionEstudiante', ['data' => $this->estudiante], function ($message) {
            $message->to($this->estudiante->Correo)
                ->subject('Notificación de QR para Acceso')
                ->attach($this->filePath);
        });
    }
}
