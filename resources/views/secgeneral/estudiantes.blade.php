@extends('layouts.principal')
@section('title', 'Estudiantes')


@push('components')
@include('components.app.Titulo')
@include('components.app.modal')
@include('components.app.card-action')
@include('components.filter.estudiante')
@include('components.app.input')
@include('components.inputs.InputFile')
@include('components.app.icono-estado')
@include('components.app.sidebar')
@include('components.app.badge')
@include('components.app.icons-information')
@include('components.app.select')
@include('components.app.list-group')
@include('components.modals.CargaDocumentoModal')
@include('components.modals.verdocumento')
@include('components.sidebars.documentos')
@include('components.sidebars.proceso_grado')
@include('components.modals.informacion_estudiante')
@include('secgeneral.btn_generar_snies')
@endpush


@push('csscomponent')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" />
<link rel="stylesheet" href="{{ asset('css/apptable.css')}}" />
@endpush


@push('scripts')
<script type="module" src="{{ asset('js/secgeneral/estudiantes.js') }}"></script>
@endpush


@section('content')
<titulo>ESTUDIANTES</titulo>



<card-action title="ACCIONES">
    <filter-estudiante v-model="filter" @initdtable="initDataTable()"
        filter_class="col-md-11 offset-md-{{ isset($backup) ? '3' : '2' }}" buttons_class="mt-3" :secretaria="true"
        :isbackup="isBackup" customcolumn>

        @if (isset($backup) && $backup)
        <template v-slot:buttons>
            <input type="hidden" id="input-isBackup" value="{{$backup}}" />
            <button class="btn btn-sm btn-success btn-icon-split ml-2" @click="downloadBackup()">
                <span class="icon text-white-50">
                    <i class="fas fa-cloud-download-alt"></i>
                </span>
                <span class="text">Descargar Backup</span>
            </button>
        </template>

        @else
        <template v-slot:buttons>
            <btn-generar-snies :filter="filter"></btn-generar-snies>
        </template>
        @endif
    </filter-estudiante>
</card-action>

<div class="mt-3 mb-3">
    <table class="table dtable table-sm table-responsive-sm AppTable-Separated AppTable-List " id="tabla-estudiante"
        style="width:100%">
        <thead style="font-size: 13px;">
            <tr>
                <th>Foto</th>
                <th>Código</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Fecha de Grado</th>
                <th>Estado</th>
                <th>Estado Programa</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody class="app-text-black-1">

        </tbody>
    </table>
</div>

<sidebar-proceso-grado :show="show_est" @hide="show_est = false" :estudiante_data="estudiante">
</sidebar-proceso-grado>

<sidebar-documentos :show="show_sidebar" @hide="show_sidebar = false" :estudiante_data="estudiante" :secretaria="true">

</sidebar-documentos>

<modal-informacion-estudiante :estudiante_id="estudiante_id" @hide="estudiante_id = undefined">
</modal-informacion-estudiante>
@endsection
