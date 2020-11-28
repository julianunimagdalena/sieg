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
@include('components.app.icono-estado')
@include('components.app.required')
@include('components.app.list-group')
@include('components.app.icons-information')
@include('components.app.badge')
@include('components.app.select')
@include('components.ficha.user-avatar')
@include('components.app.modal')
@include('components.modals.verdocumento')
@include('components.modals.AsistenciaCeremoniaModal')
@include('components.inputs.InputFile')
@include('components.modals.CargaDocumentoModal')
@endpush

@section('content')
<div>
    <titulo>Estado del proceso de grado</titulo>
    <div class="mt-3">
        <card-action title="PERFIL">
            <div class="row">
                <div class="col-md-2 col-sm-12">
                    <user-avatar :imgstyle="{'max-height': '100px'}"></user-avatar>
                </div>
                <div class="col-md-10 col-sm-12" v-if="info">
                    <div class="form-row">
                        <div class="col-md-6 col-sm-12 form-group">
                            <app-input label="Nombre Completo" disabled v-model="info.nombre" />
                        </div>
                        <div class="col-md-6 col-sm-12 form-group">
                            <app-input label="Código" disabled v-model="info.codigo" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-2 col-sm-4 form-group">
                            <app-input label="Tipo Documento" disabled v-model="info.tipo_documento" />
                        </div>
                        <div class="col-md-4 col-sm-8 form-group">
                            <app-input label="Documento" disabled v-model="info.documento" />
                        </div>
                        <div class="col-md-6 col-sm-12 form-group">
                            <app-input label="Programa" disabled v-model="info.programa" />
                        </div>
                    </div>
                </div>
            </div>
        </card-action>
    </div>
    <hr />
    <icons-information>
        <span class="ml-4">
            <span>Acciones: </span>
            <span class="text-secondary">
                <span class="ml-2">
                    <i class="fas fa-location-arrow"></i> Ir a la Pagina
                </span>
                <span class="ml-2"> - </span>
                <span class="ml-2 text-primary">
                    <i class="fas fa-pencil-alt"></i> Editar Información
                </span>
            </span>
        </span>
    </icons-information>
    <div class="row mt-3 mb-5">
        <div class="col-md-8 col-sm-12">
            <card-action fluid title="Información de Proceso de Grado" :hflex="false">
                <table class="table table-sm ">
                    <thead class="thead-light">
                        <tr>
                            <th>PROCESO</th>
                            <th>RESPONSABLE</th>
                            <th>ESTADO</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody class="app-text-black-1">
                        <tr class="TableRow-2" v-if="info.diligencia_encuesta">
                            <td class="font-weight-bold">Registro de la encuesta (Momento de Grado)</td>
                            <td>Estudiante</td>
                            <td>
                                <icono-estado :estado="estadoEncuesta"></icono-estado>
                            </td>
                            <td class="font-weight-bold">
                                <a href="egresado/encuesta/momento_0" class="text-secondary">
                                    <i class="fas fa-location-arrow"></i>
                                </a>
                            </td>
                        </tr>
                        <tr class="TableRow-2">
                            <td class="font-weight-bold">Ficha de egresado</td>
                            <td>Estudiante</td>
                            <td>
                                <icono-estado :estado="estadoFicha"></icono-estado>
                            </td>
                            <td class="font-weight-bold">
                                <a href="egresado/ficha-egresado" class="text-secondary">
                                    <i class="fas fa-location-arrow"></i>
                                </a>
                            </td>
                        </tr>
                        <tr class="TableRow-2">
                            <td class="font-weight-bold">Carga de los documentos de grado</td>
                            <td>Estudiante</td>
                            <td>
                                <icono-estado :estado="info.estado_documentos"></icono-estado>
                            </td>
                            <td>
                                <!--<a href="egresado/carga-documentos" class="text-secondary">
                                    <i class="fas fa-location-arrow"></i>
                                </a>-->
                                <a href="#modalListaDocumentos" data-toggle="modal">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <tr class="TableRow-2">
                            <td class="font-weight-bold">Confirmación de asistencia a ceremonia de grado</td>
                            <td>Estudiante</td>
                            <td>
                                <icono-estado :estado="confirmacionCeremonia"></icono-estado>
                            </td>
                            <td>
                                <a href="#asistenciaCeremoniaModal" data-toggle="modal">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <tr class="TableRow-2">
                            <td class="font-weight-bold">Aprobación del proceso de grado</td>
                            <td>Dirección de programa</td>
                            <td>
                                <icono-estado :estado="estadoPrograma"></icono-estado>
                            </td>
                            <td> - </td>
                        </tr>
                    </tbody>
                </table>
            </card-action>
        </div>
        <div class="col-md-4 col-sm-12">
            <card-action title="Listado de Paz y Salvos" fluid>
                <list-group flush>
                    <list-group-item-flex v-for="ps in info.paz_salvos" class="list-group-item-flex-md" :key="ps.id">
                        <div class="font-weight-bold text-initial">
                            @{{ ps.nombre }}
                        </div>
                        <div>
                            <icono-estado :estado="getEstado(ps.paz_salvo)"></icono-estado>
                        </div>
                    </list-group-item-flex>
                </list-group>
            </card-action>
        </div>
    </div>
</div>

<modal title="Documentos" id="modalListaDocumentos" large>
    <div class="p-1">
        <small class="text-secondary"><span class="font-weight-bold">Nota:</span>
            si no puede ver su documento de identidad por favor acercarse a admisiones o su programa
        </small>
    </div>

    <div class="font-weight-bold mt-2">
        Lista
    </div>
    <div class="mt-3">
        <list-group flush>
            <list-group-item v-for="documento in datos.documentos" :key="documento.id"
                :class="'border-estado-radius border-left-'+getClassEstado(documento.estado)">
                <span>@{{ documento.nombre }}</span>
                <template v-slot:actions>
                    <i title="Cargar Documento" @click="onClickUploadDocumento(documento)"
                        class="fas fa-upload mr-3 text-secondary action-btn" v-if="documento.can_upload"></i>
                    <i title="Ver Documento" @click="onClickVerDocumento(documento)"
                        class="far fa-eye text-primary action-btn" v-if="documento.can_show"></i>
                </template>
                <template v-slot:aditionals>
                    <div class="font-weight-bold">
                        <small class="text-danger" v-if="documento.motivo_rechazo">
                            Motivo Rechazo: @{{ documento.motivo_rechazo }}
                        </small>
                    </div>
                </template>
            </list-group-item>
        </list-group>
    </div>
</modal>

<modal title="ECAES" id="modalFormularioEcaes" :onsubmit="onSubmitFormEcaes" :submit="onSubmitFormEcaes"
    :buttontext="'Enviar'" @onhide="toggleModals()">
    <div class="form-group">
        <app-input label="Código de ECAES" placeholder="Código" v-model="forms.documento.codigo_ecaes" required />
    </div>
    <div class="form-group">
        <p class="font-weight-bold">Documento <app-required></app-required>
        </p>
        <input-file v-model="forms.documento.file" label="Documento"></input-file>
    </div>
</modal>

<carga-documento-modal id="cargaDocumentoModal" :documento="forms.documento" v-on:documento-cargado="toggleModals">
</carga-documento-modal>

<asistencia-ceremonia-modal id="asistenciaCeremoniaModal" v-if="info" :codigo="info.codigo" v-on:complete="fetchData">
</asistencia-ceremonia-modal>

<modal-ver-documento @onhide="toggleModals(false)" :documento="show_documento" v-if="show_documento">
</modal-ver-documento>
@endsection

@push('scripts')
<script>
    $('.popover-dismiss').popover({
        trigger: 'focus'
    });
</script>
<script type="module" src="{{asset('js/egresado/index.js')}}"></script>
@endpush
