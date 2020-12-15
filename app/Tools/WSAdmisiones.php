<?php

namespace App\Tools;

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;

class WSAdmisiones
{
    function __construct()
    {
        $date = Carbon::now()->format('d/m/Y');

        $this->baseURL = 'http://ayre.unimagdalena.edu.co/WebServicesEgresados/egr';
        $this->initialToken = 'infoCenEgres@jax*wS_2015-*#!' . $date . '!#';
    }

    private function generateToken(string $value = '')
    {
        return strtoupper(md5($this->initialToken . $value));
    }

    private function fetch(string $url, $method = 'GET', $transform_json = true)
    {
        $client = new \GuzzleHttp\Client();

        // try {
        //     $res = $client->request($method, $url)->getBody();
        //     $res = json_decode($res);
        // } catch (RequestException $e) {
        //     if ($e->hasResponse()) $res = $e->getResponse()->getBody();
        // }

        $res = $client->request($method, $url)->getBody();
        if ($transform_json) $res = json_decode($res);

        return $res;
    }

    public function getFacultades()
    {
        $url = $this->baseURL . '/listfacs/' . $this->generateToken();
        return $this->fetch($url);
    }

    public function getFotoByCodigo(string $codigo)
    {
        $url = $this->baseURL . '/bytesfoto/' . $codigo . '/' . $this->generateToken($codigo);
        // return $this->fetch($url);
    }

    public function getHistorialActualizacionDocumentosIdentidad(string $codigo)
    {
        $url = $this->baseURL . '/hisactdoc/' . $codigo . '/' . $this->generateToken($codigo);
        return $this->fetch($url);
    }

    public function getInfoEstudianteByCodigo(string $codigo)
    {
        $url = $this->baseURL . '/infoestcod/' . $codigo . '/' . $this->generateToken($codigo);
        return $this->fetch($url);
    }

    public function getInformacionGraduadoByCodigo(string $codigo)
    {
        $url = $this->baseURL . '/infogrdcod/' . $codigo . '/' . $this->generateToken($codigo);
        return $this->fetch($url);
    }

    public function getInformacionGraduadoByDocumentoIdentidad(string $identificacion, $tipoDoc = 'C.C.')
    {
        $url = $this->baseURL . '/infgrdoc/' . $identificacion . '/' . $this->generateToken($identificacion);
        // $url = $this->baseURL . '/infgrdoc/' . $tipoDoc . '/' . $identificacion . '/' . $this->generateToken($identificacion);
        return $this->fetch($url);
    }

    public function getListaGraduadoByFechas($fechaInicio, $fechaFinal)
    {
        $fi = Carbon::parse($fechaInicio)->format('dmY');
        $ff = Carbon::parse($fechaFinal)->format('dmY');
        $url = $this->baseURL . '/listgrdfec/' . $fi . '/' . $ff . '/' . $this->generateToken($fi . $ff);

        return $this->fetch($url);
    }

    public function getLogin()
    {
    }

    public function getProgramasAcademicos()
    {
        $url = $this->baseURL . '/listprgs/' . $this->generateToken();
        return $this->fetch($url);
    }

    public function fetchDocumentoIdentidad($codigo)
    {
        $url = $this->baseURL . '/bytesdocumento/' . $codigo . '/' . $this->generateToken($codigo);
        return $this->fetch($url, 'GET', false);
    }
}
