<?php

namespace App\Traits;

use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\DB;
trait MailTrait
{

    public function getEmailByid($id)
    {

        try {
            $response = DB::select(DB::raw("
        SELECT  ut.CorreoElectronico FROM   TiCentral.Usuarios ut
        WHERE    ut.id='" . $id . "'
        "
            ));
            return $response[0];
        } catch (\Exception $e) {
            return "";
        }

    }

    public function sendMail($user_id, $subject, $menu, $referencia, $body)
    {

        $correo = $this->getEmailByid($user_id)->CorreoElectronico;
        if ($correo !== "") {
            $params = array(
                "referencia" => $referencia,
                "subject" => $subject,
                "to" => $correo,
                "data" => $body);
            $params = json_encode($params);
            $data = json_decode($params);
            Mailer::sendMailService(($data));
        }

    }

    public static function sendMailService($params)
    {
        $emails = ['aagarcia@cecapmex.com', 'adolfoangelgarcia66@gmail.com'];

        $subject = 'Mail de Prueba';
        return $this->subject($subject)
            ->to($emails)
            ->view('mails.demobody')
            ->with([
                'params' => $this->params,
            ]);

    }

}
