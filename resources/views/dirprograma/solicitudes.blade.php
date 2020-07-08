@extends('layouts.principal')
@section('title', 'Solicitudes de grado pendientes')

@push('csscomponent')
<link rel="stylesheet" href="{{ asset('css/apptable.css')}}" />
@endpush

@push('components')
    @include('components.app.input')
    @include('components.app.modal')
@endpush

@section('content')
<div id="app">
    <h3 class="titulo text-primary">Solicitudes de grado pendientes</h3>
    <p>A continuación los estudiantes que hicieron una solicitud de grado a su programa:</p>
    <table class="table table-sm table-hover">
        <thead class="bg-primary">
            <tr class="text-uppercase text-white">
                <th>Código</th>
                <th>Nombre</th>
                <th>Fecha de grado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr class="TableRow" v-for="item in solicitudes" :key="item.id">
                <td>@{{item.codigo_estudiante}}</td>
                <td>@{{item.nombre_estudiante}}</td>
                <td>@{{item.fecha_grado.nombre}}</td>
                <td>@{{item.fecha}}</td>
                <td>
                    <a href="#" @click.prevent="activarEstudiante(item)">
                        <i class="far fa-check-circle text-success" title="Activar estudiante"></i>
                    </a>
                    <i
                        class="far fa-times-circle text-danger ml-3 action-btn"
                        @click="form.solicitud = item"
                        data-toggle="modal"
                        data-target="#modalRechazarSolicitud"
                        title="Rechazar solicitud"></i>
                </td>
            </tr>
        </tbody>
    </table>

    <modal id="modalRechazarSolicitud" title="Motivo de No Aprobación" :onsubmit="rechazarSolicitud" >
        <app-input
            label="Motivo"
            required
            class="form-group"
            placeholder="Motivo"
            type="textarea"
            v-model="form.solicitud.motivo"
            :errors="errors.solicitud.motivo"
            @input="errors.solicitud.motivo = undefined"
        />
    </modal>
</div>
@endsection

@push('scripts')
<script type="module" src="{{asset('js/dirprograma/solicitudes.js')}}"></script>
@endpush
