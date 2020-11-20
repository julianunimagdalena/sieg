<?php

namespace App\Tools;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WSFoto
{
    public function __construct()
    {
        $this->token = Carbon::now('America/Bogota')->format('d-m-Y') . 'GRUPO-TIC';
    }

    public function consultarFoto($codigo)
    {
        $token = $this->token;
        $url = 'http://cinto.unimagdalena.edu.co:9000/ApiIdea/api/Foto/' . $codigo . '/' . strtoupper(md5($token));
        $res = json_decode($this->fetch($url, 'GET'));

        return (object) [
            'foto_actual' => $res->FotoActual,
            'foto_prevalidada' => $res->FotoPreValidada
        ];
    }

    public function validarFoto(string $foto)
    {
        $token = $this->token;
        $url = 'http://cinto.unimagdalena.edu.co:9000/ApiIdea/api/ValidarFoto';

        $headers = ['Content-Type' => 'application/json'];
        $body = '{ "key": "' . strtoupper(md5($token)) . '", "Foto": "' . $foto . '", }';

        $res = json_decode($this->fetch($url, 'POST', $headers, $body));

        return (object) ['valido' => $res->Valido, 'descripcion' => $res->Descripcion];
    }

    public function registrar($estudiante)
    {
        $rol_estudiante = DB::connection('carnetizacion')
            ->table('roles')->where('nombre', 'like', 'estudiante')->first();

        $usr_id = DB::connection('carnetizacion')->table('usuarios')->insertGetId([
            'username' => $estudiante->codigo,
            'nombre' => $estudiante->persona->nombres,
            'apellidos' => $estudiante->persona->apellidos,
            'email' => $estudiante->persona->correo,
            'roles_id' => $rol_estudiante->id
        ]);

        $est_id = DB::connection('carnetizacion')->table('estudiante')->insertGetId([
            'codigo' => $estudiante->codigo,
            'programa' => $estudiante->estudio->programa->nombre,
            'codPrograma' => $estudiante->estudio->programa->codigoPrograma,
            'facultad' => $estudiante->estudio->facultad->nombre,
            'modalidad' => $estudiante->estudio->modalidad->nombre,
            'cohorte' => $estudiante->cohorte,
            'numeroDocumento' => $estudiante->persona->identificacion,
            'telefono' => $estudiante->persona->celular,
            'correo' => $estudiante->persona->correo,
            'correo_inst' => $estudiante->persona->correo_institucional,
            'zonal' => $estudiante->zonal->nombre,
            'estado' => 0,
            'usuarios_id' => $usr_id
        ]);

        $pg = $estudiante->procesoGrado;
        $pg->est_carnetizacion_id = $est_id;
        $pg->save();
    }

    public function consultarEstado($est_id)
    {
        $estudiante = DB::connection('carnetizacion')->table('estudiante')->where('id', $est_id)->first();

        if (!$estudiante) return 'rechazado';
        if ($estudiante->estado == '1') return 'aprobado';

        return 'no aprobado';
    }

    public function guardarFoto(string $foto, $estudiante, bool $definitiva = false)
    {
        $token = $this->token;
        $url = 'http://cinto.unimagdalena.edu.co:9000/ApiIdea/api/Foto';
        $directorio = $definitiva ? '2' : '1';
        $headers = ['Content-Type' => 'application/json'];
        $body = '{ "key": "' . strtoupper(md5($token))
            . '", "codigo": ' . $estudiante->codigo . ', "Directorio": ' . $directorio . ', "Foto": "' . $foto . '", }';

        $res = $this->fetch($url, 'POST', $headers, $body);
        $res = json_decode($res);

        if ($res->Valido) $this->registrar($estudiante);

        return $res->Valido;
    }

    private function fetch(string $url, string $method, $headers = [], $body = '')
    {
        $client = new \GuzzleHttp\Client();
        $req = new \GuzzleHttp\Psr7\Request($method, $url, $headers, $body);
        $res = $client->send($req)->getBody();

        return json_decode($res);
    }
}
