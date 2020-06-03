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
@include('components.app.card')
@include('components.forms.FiltroFechaGrado')
@endpush

@section('content')
<titulo>Administrar fechas de grado</titulo>
<button class="btn-primary btn">Nueva fecha de grado</button>
<br><br>
<card title="Filtrar fechas de grado" color="primary">
    <filtro-fecha-grado v-on:buscar="buscar"></filtro-fecha-grado>
</card>
<br>
<card color="primary" v-if="searched">
    <table class="table table-sm data-table" v-if="fechas.length>0">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr class="TableRow" v-for="fec in fechas">
                <td>@{{fec.fecha}}</td>
                <td>@{{fec.nombre}}</td>
                <td>@{{fec.tipo}}</td>
                <td>
                    <i class="fas fa-pen text-primary" title="Editar" v-on:click="fechaGradoModal(fec)"></i>
                    <i class="fas fa-trash text-danger" title="Eliminar" v-on:click="eliminarFecha(fec)"></i>
                </td>
            </tr>
        </tbody>
    </table>
    <span class="text-danger" v-else>No se encontro ningun resultado</span>
</card>
@endsection

@push('scripts')
<script type="module" src="{{asset('js/administrador/fechas_grado.js')}}"></script>
@endpush
