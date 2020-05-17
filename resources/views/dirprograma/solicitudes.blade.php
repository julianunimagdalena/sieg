@extends('layouts.principal')
@section('title', 'Solicitudes de grado pendientes')

@section('content')
<div id="app">
    <h3 class="titulo text-primary">Solicitudes de grado pendientes</h3>
    <p>A continuación los estudiantes que hicieron una solicitud de grado a su programa:</p>
    <table class="table table-sm table-hover">
        <thead class="bg-primary">
            <tr class="text-uppercase text-white">
                <th>Fecha</th>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Fecha de grado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="item in solicitudes" :key="item.id">
                <td>@{{item.fecha}}</td>
                <td>@{{item.codigo_estudiante}}</td>
                <td>@{{item.nombre_estudiante}}</td>
                <td>@{{item.fecha_grado.nombre}}</td>
                <td>
                    <a href="#" @click.prevent="activarEstudiante(item)">
                        <i class="fas fa-plus text-success" title="Activar estudiante"></i>
                    </a>
                    <i class="fas fa-times text-danger" title="Rechazar solicitud"></i>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script type="module" src="{{asset('js/dirprograma/solicitudes.js')}}"></script>
@endpush
