@extends('layouts.principal')
@section('title', 'Estudiantes')

@push('components')
    @include('components.app.Titulo')
    @include('components.app.sidebar')
    @include('components.app.badge')
    @include('components.app.card-action')
    @include('components.app.icono-estado')
    @include('components.app.input')
    @include('components.app.icons-information')
    @include('components.app.modal')
    @include('components.app.select')
    @include('components.modals.verdocumento')
    @include('components.modals.informacion_estudiante')
    @include('components.sidebars.documentos')
    @include('components.sidebars.proceso_grado')
    @include('components.app.list-group')
    @include('components.inputs.InputFile')
    @include('components.filter.estudiante')
    @include('components.modals.CargaDocumentoModal')
    @include('secgeneral.btn_generar_snies')
@endpush


@push('csscomponent')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" />
<link rel="stylesheet" href="{{ asset('css/apptable.css')}}" />
@endpush

@push('scripts')
    <!--<script type="module" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="module" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>-->
    <script type="module" src="{{ asset('js/dirprograma/estudiantes.js') }}"></script>
@endpush


@section('content')
    <titulo>Consultar Aspirantes a Grado</titulo>
    <div>
        <div class="mb-3">
            <filter-estudiante v-model="filter" @initdtable="initDataTable()">
                <template v-slot:buttons>
                    <btn-generar-snies :filter="filter"></btn-generar-snies>
                </template>
            </filter-estudiante>
        </div>

        <div class="mb-3">
            <!--<icons-information>
                <span class="ml-4 font-weight-bold">
                    <span>
                        Posibles Acciones:
                    </span>
                    <span class="text-primary action-btn ml-3 mr-3 text-left">
                        <i class="fas fa-location-arrow"></i> Ver
                    </span>
                    <span class="text-secondary action-btn mr-3 text-center">
                        <i class="fas fa-cog"></i> Generar
                    </span>
                    <span class="text-secondary action-btn mr-3">
                        <i class="fas fa-upload"></i> Cargar
                    </span>
                    <span class="text-success action-btn mr-3 text-right">
                        <i class="fas fa-check"></i> Aprobar
                    </span>
                    <span class="text-danger action-btn ">
                        <i class="fas fa-times"></i> Rechazar
                    </span>
                </span>
            </icons-information>-->
        </div>

        <table class="table dtable table-sm table-responsive-sm AppTable-Separated AppTable-List " id="tabla-estudiante" style="width:100%">
            <thead style="font-size: 13px;">
                <tr>
                    <th>Foto</th>
                    <th>Código</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Fecha de Grado</th>
                    <th>Estado</th>
                    <th>Estado Programa</th>
                    <th>Estado Secretaría</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody class="app-text-black-1">

            </tbody>

        </table>
    </div>
    <sidebar-proceso-grado :show="show_est" @hide="show_est = false" :estudiante_data="estudiante">
    </sidebar-proceso-grado>

    <sidebar-documentos :show="show_dir" @hide="show_dir = false" :estudiante_data="estudiante" @refreshdata="initDataTable()">

    </sidebar-documentos>

    <modal-informacion-estudiante :estudiante_id="estudiante_id" @hide="estudiante_id = undefined">
    </modal-informacion-estudiante>
@endsection


