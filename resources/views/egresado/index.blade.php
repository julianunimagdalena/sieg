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
@include('components.app.card-action')
@include('components.app.input')
@include('components.app.estado-icono')
@include('components.app.required')
@include('components.app.badge')
@include('components.app.select')
@include('components.ficha.user-avatar')
@include('components.modals.Modal')
@include('components.modals.AsistenciaCeremoniaModal')
@endpush

@section('content')
<div >
    <titulo>Estado del proceso de grado</titulo>
    <div class="mt-3">
        <card-action title="PERFIL">
            <div class="row">
                <div class="col-md-2 col-sm-12" >
                    <user-avatar :imgstyle="{'max-height': '100px'}"></user-avatar >
                </div>
                <div class="col-md-10 col-sm-12" v-if="info">
                    <div class="form-row">
                        <div class="col-md-6 col-sm-12 form-group">
                            <app-input
                                label="Nombre Completo"
                                disabled
                                v-model="info.nombre"
                            />
                        </div>
                        <div class="col-md-6 col-sm-12 form-group">
                            <app-input
                                label="Codigo"
                                disabled
                                v-model="info.codigo"
                            />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-2 col-sm-4 form-group">
                            <app-input
                                label="Tipo Documento"
                                disabled
                                v-model="info.tipo_documento"
                            />
                        </div>
                        <div class="col-md-4 col-sm-8 form-group">
                            <app-input
                                label="Documento"
                                disabled
                                v-model="info.documento"
                            />
                        </div>
                        <div class="col-md-6 col-sm-12 form-group">
                            <label>Programa</label>
                            <select
                                v-model="info"
                                class="form-control"
                            >
                                <option v-for="inf in infos" :value="inf">@{{inf.programa}}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </card-action>
    </div>
    <hr/>
    <card-action :border="false" class="app-text-black-1">
        <div class="font-weight-bold">
            <span>
                Posibles Estados:
            </span>
            <span class="text-warning ml-2">
                <i class="far fa-clock"></i> Pendiente
            </span>
            <span class="ml-2 mr-2">
                -
            </span>
            <span class="text-success">
                <i class="far fa-check-circle"></i> Aprobado
            </span>
            <span class="ml-2 mr-2">
                -
            </span>
            <span class="text-danger">
                <i class="far fa-times-circle"></i> Rechazado
            </span>
        </div>
    </card-action>
    <div class="row mt-3 mb-5">
        <div class="col col-md-6 col-sm-12">
            <card-action fluid title="Información de Proceso de Grado" :hflex="false" >
                <template v-slot:head>
                    <div class="text-secondary">
                        <small>
                            <app-required>Usted puede presionar en el estado para más acciones</app-required>
                        </small>
                    </div>
                </template>
                <table class="table table-sm ">
                    <thead class="thead-light">
                        <tr>
                            <th>PROCESO</th>
                            <th>RESPONSABLE</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody class="app-text-black-1">
                        <tr class="TableRow-2">
                            <td class="font-weight-bold">Registro de la encuesta (Momento de Grado)</td>
                            <td>Estudiante</td>
                            <td class="font-weight-bold">
                                <a href="egresado/encuesta">
                                    <estado-icono :estado="estadoEncuesta"></estado-icono>

                                </a>
                            </td>
                        </tr>
                        <tr class="TableRow-2">
                            <td class="font-weight-bold">Ficha de egresado</td>
                            <td>Estudiante</td>
                            <td class="font-weight-bold">
                                <a href="egresado/ficha-egresado">
                                    <estado-icono :estado="estadoFicha"></estado-icono>
                                </a>
                            </td>
                        </tr>
                        <tr class="TableRow-2">
                            <td class="font-weight-bold">Carga de los documentos de grado</td>
                            <td>Estudiante</td>
                            <td>
                                <a href="egresado/carga-documentos">
                                    <estado-icono :estado="info.estado_documentos"></estado-icono>
                                </a>
                            </td>
                        </tr>
                        <tr class="TableRow-2">
                            <td class="font-weight-bold">Confirmación de asistencia a ceremonia de grado</td>
                            <td>Estudiante</td>
                            <td>
                                <a href="#asistenciaCeremoniaModal" data-toggle="modal">
                                    <estado-icono :estado="confirmacionCeremonia"></estado-icono>
                                </a>
                            </td>
                        </tr>
                        <tr class="TableRow-2">
                            <td class="font-weight-bold">Aprobación del proceso de grado</td>
                            <td>Dirección de programa</td>
                            <td><estado-icono :estado="estadoPrograma"></estado-icono></td>
                        </tr>
                    </tbody>
                </table>
            </card-action>
        </div>
        <div class="col col-md-6 col-sm-12">
            <card-action title="Listado de Paz y Salvos" fluid>
                <table class="table table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>DEPENDENCIA</th>
                            <th>COMENTARIO</th>
                            <th>ESTADO</th>
                        </tr>
                    </thead>
                    <tbody v-if="info" class="app-text-black-1">
                        <tr class="TableRow" v-for="ps in info.paz_salvos">
                            <td class="font-weight-bold">@{{ps.nombre}}</td>
                            <td>@{{ps.comentario || '-'}}</td>
                            <td>
                                <estado-icono :estado="getEstado(ps.paz_salvo)"></estado-icono>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </card-action>
        </div>
    </div>
</div>
<asistencia-ceremonia-modal id="asistenciaCeremoniaModal" v-if="info" :codigo="info.codigo" v-on:complete="fetchData">
</asistencia-ceremonia-modal>
@endsection

@push('scripts')
<script type="module" src="{{asset('js/egresado/index.js')}}"></script>
@endpush
