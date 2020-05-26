@extends('layouts.principal')
@section('title', 'Egresado')

@push('csscomponent')
<link rel="stylesheet" href="{{ asset('css/apptable.css')}}" />
<style>
    tr {
        cursor: auto !important;
    }

    i.fas {
        cursor: pointer;
    }
</style>
@endpush

@push('components')
@include('components.app.Titulo')
@include('components.modals.Modal')
@include('components.modals.UsuarioModal')
@endpush

@section('content')
<titulo>Administrar usuarios</titulo>
<button class="btn btn-primary btn-sm" v-on:click="usuarioModal(null)">Nuevo usuario</button>
<br><br>
<table class="table table-sm">
    <thead>
        <tr>
            <th>Usuario</th>
            <th>Identificación</th>
            <th>Rol</th>
            <th>Programas</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <tr class="TableRow" v-if="usuarios" v-for="usr in usuarios">
            <td>@{{usr.username}}</td>
            <td>@{{usr.identificacion}}</td>
            <td>@{{usr.rol}}</td>
            <td style="font-size: .8rem">@{{usr.programas || '-'}}</td>
            <td>
                <i class="fas fa-pen text-primary" title="Editar" v-on:click="usuarioModal(usr)"></i>
                <i class="fas fa-trash text-danger" title="Eliminar" v-on:click="eliminar(usr)"></i>
            </td>
        </tr>
    </tbody>
</table>
<usuario-modal id="usuarioModal" :usuario="usuario"></usuario-modal>
@endsection

@push('scripts')
<script type="module" src="{{asset('js/administrador/administrar_usuarios.js')}}"></script>
@endpush
