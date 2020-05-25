<?php

namespace App\Http\Controllers;

use App\Models\EstudianteDocumento;
use App\Models\UsuarioRol;
use App\Tools\Variables;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function __construct()
    {
        $roles = Variables::roles();
        $this->datos = (object) ['roles' => $roles];

        $this->middleware('auth');
        $this->middleware('rol:' . $roles['estudiante']->nombre);
    }

    public function ver($ed_id)
    {
        $ed = null;
        $ur = UsuarioRol::find(session('ur')->id);

        if ($ur->rol_id === $this->datos->roles['estudiante']->id) {
            $ed = EstudianteDocumento::whereHas('estudiante.persona.usuario.usuarioRol', fn ($u) => $u === $ur->id)
                ->find($ed_id);
        }

        if (!$ed) return response('No permitido', 400);

        return response()->file(storage_path('/app/' . $ed->url_documento));
    }
}
