<?php

namespace App\Tools;

use Carbon\Carbon;
use SoapClient;

class WSSiare
{
    private $client;

    public function __construct()
    {
        $url = 'http://siare.unimagdalena.edu.co/WebServices/WS_Siare_AjusteHorarios.asmx?wsdl';
        $this->client = new SoapClient($url, ['trace' => 1]);
    }

    public function generarToken($codigo)
    {
        $token = $codigo . '*' . Carbon::now()->format('d/m/Y') . '*siare@2014_';
        $token = md5($token);
        $token = strtoupper($token);

        return $token;
    }

    public function ConsultarPazySalvo($codigo)
    {
        $token = $this->generarToken($codigo);
        $res = $this->client->ConsultarPazySalvo([
            'Codigo' => $codigo,
            'Token' => $token
        ]);

        return $res->ConsultarPazySalvoResult === 'true';
    }
}
