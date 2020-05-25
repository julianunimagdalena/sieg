@extends('layouts.principal')
@section('title', 'Egresado')

@push('csscomponent')
<link rel="stylesheet" href="{{ asset('css/apptable.css')}}" />
<style>
    tr {
        cursor: auto !important;
    }
</style>
@endpush

@push('components')
@include('components.app.Titulo')
@include('components.app.SelectorInfoGrado')
@endpush

@section('content')
<div class="row">
    <div class="col-lg-6">
        <titulo>Estado del proceso de grado</titulo>
        <selector-info-grado v-model="info" :infos="infos"></selector-info-grado>
        <div v-if="info">
            <span class="font-weight-bold">NOMBRE DEL ESTUDIANTE:</span> @{{info.nombre}}
            <br>
            <span class="font-weight-bold">CODIGO DEL ESTUDIANTE:</span> @{{info.codigo}}
            <br><br>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>PROCESO</th>
                        <th>RESPONSABLE</th>
                        <th>ESTADO</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="TableRow">
                        <td>Registro de la encuesta (Momento de Grado)</td>
                        <td>Estudiante</td>
                        <td>
                            <a href="egresado/encuesta">
                                <span :class="['badge', estadoEncuesta]">@{{estadoEncuesta}}</span>
                            </a>
                        </td>
                    </tr>
                    <tr class="TableRow">
                        <td>Ficha de egresado</td>
                        <td>Estudiante</td>
                        <td>
                            <a href="egresado/ficha-egresado">
                                <span :class="['badge', estadoFicha]">@{{estadoFicha}}</span>
                            </a>
                        </td>
                    </tr>
                    <tr class="TableRow">
                        <td>Carga de los documentos de grado</td>
                        <td>Estudiante</td>
                        <td>
                            <a href="egresado/carga-documentos">
                                <span :class="['badge', info.estado_documentos]">@{{info.estado_documentos}}</span>
                            </a>
                        </td>
                    </tr>
                    <tr class="TableRow">
                        <td>Confirmación de asistencia a ceremonia de grado</td>
                        <td>Estudiante</td>
                        <td>
                            <span :class="['badge', confirmacionCeremonia]">@{{confirmacionCeremonia}}</span>
                        </td>
                    </tr>
                    <tr class="TableRow">
                        <td>Aprobación del proceso de grado</td>
                        <td>Dirección de programa</td>
                        <td><span :class="['badge', estadoPrograma]">@{{estadoPrograma}}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-6">
        <titulo>Listado de paz y salvos</titulo>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>DEPENDENCIA</th>
                    <th>COMENTARIO</th>
                    <th>ESTADO</th>
                </tr>
            </thead>
            <tbody v-if="info">
                <tr class="TableRow" v-for="ps in info.paz_salvos">
                    <td>@{{ps.nombre}}</td>
                    <td>@{{ps.comentario || '-'}}</td>
                    <td><span :class="['badge', getEstado(ps.paz_salvo)]">@{{getEstado(ps.paz_salvo)}}</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script type="module" src="{{asset('js/egresado/index.js')}}"></script>
@endpush
