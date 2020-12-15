<?php

namespace App\Tools;

use Carbon\Carbon;
use App\Models\TipoGrado;
use Illuminate\Support\Facades\Mail;

class MailHelper
{
    /**
     * options:
     * name
     * subject
     */
    static public function enviarCorreo($content, $dest, $options = null)
    {
        $data = ['texto' => $content, 'nombre' => $options->name];

        try {
            Mail::send('emails.index', $data, function ($message) use ($dest, $options) {
                $message->subject($options->subject . ' - Universidad del Magdalena');

                foreach ($dest as $des) {
                    $message->bcc($des);
                }
            });

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
