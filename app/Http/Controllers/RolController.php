<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;

class RolController extends Controller
{
    private function search(Request $req)
    {
        $query = new Rol();

        if ($req->id) $query = $query->where('id', $req->id);
        if ($req->nombre) $query = $query->where('nombre', 'like', $req->nombre);

        return $query;
    }

    public function get(Request $req)
    {
        $query = $this->search($req);
        return $query->first();
    }
}
