<?php

namespace App\Http\Controllers;

use App\Http\Requests\CargaDocumentoRequest;
use App\Models\EstudianteDocumento;
use App\Models\UsuarioRol;
use App\Tools\Variables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function __construct()
    {
        $roles = Variables::roles();
        $this->datos = (object) ['roles' => $roles];

        $this->middleware('auth');
        $this->middleware('rol:' . $roles['estudiante']->nombre . '|' . $roles['coordinador']->nombre, ['except' => [
            'ver'
        ]]);
        $this->middleware(
            'rol:' . $roles['estudiante']->nombre
                . '|' . $roles['coordinador']->nombre
                . '|' . $roles['secretariaGeneral']->nombre,
            ['only' => [
                'ver'
            ]]
        );
    }

    public function getEstudianteDocumento($ed_id)
    {
        $ed = null;
        $ur = UsuarioRol::find(session('ur')->id);

        switch ($ur->rol_id) {
            case $this->datos->roles['estudiante']->id:
                $ed = EstudianteDocumento::whereHas('estudiante.persona.usuario.usuarioRol', fn ($u) => $u === $ur->id)
                    ->find($ed_id);
                break;

            case $this->datos->roles['coordinador']->id:
                $programa_ids = $ur->usuario->dependenciasModalidades->pluck('id');
                $ed = EstudianteDocumento::whereHas('estudiante', fn ($e) => $e->whereIn('idPrograma', $programa_ids))
                    ->find($ed_id);
                break;

            case $this->datos->roles['secretariaGeneral']->id:
                $ed = EstudianteDocumento::find($ed_id);
                break;
        }

        return $ed;
    }

    public function cargar(CargaDocumentoRequest $request)
    {
        $ed = $this->getEstudianteDocumento($request->id);
        if (!$ed) return response('No permitido', 400);

        $documentos = Variables::documentos();
        $isEcaes = $ed->idDocumento === $documentos['ecaes']->id;

        if ($isEcaes) $this->validate($request, ['codigo_ecaes' => 'required'], ['required' => 'Obligatorio']);
        Storage::put($ed->path, file_get_contents($request->file('file')->getRealPath()));

        $ur = UsuarioRol::find(session('ur')->id);
        $estados = Variables::estados();

        $ed->estado_id = $estados['pendiente']->id;
        $ed->url_documento = $ed->path;
        $ed->motivo_rechazo = null;

        if ($ur->rol_id === $this->datos->roles['coordinador']->id) $ed->estado_id = $estados['aprobado']->id;
        if ($isEcaes) {
            $pg = $ed->estudiante->procesoGrado;
            $pg->codigo_ecaes = $request->codigo_ecaes;
            $pg->save();
        }

        $ed->save();

        return 'ok';
    }

    public function ver($ed_id)
    {
        $ed = $this->getEstudianteDocumento($ed_id);
        if (!$ed) return response('No permitido', 400);

        return response()->file(storage_path('/app/' . $ed->url_documento));
    }
}
