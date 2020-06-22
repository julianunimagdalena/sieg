@extends('layouts.principal')
@section('title', 'Estudiantes')


@push('components')
    @include('components.app.Titulo')
    @include('components.app.modal')
    @include('components.app.card-action')
    @include('components.filter.estudiante')
    @include('components.app.input')
    @include('components.inputs.InputFile')
    @include('components.app.sidebar')
    @include('components.app.badge')
    @include('components.app.icons-information')
    @include('components.app.select')
    @include('components.app.list-group')
    @include('components.modals.CargaDocumentoModal')
    @include('components.modals.verdocumento')
    @include('components.sidebars.documentos')

@endpush


@push('csscomponent')
<link rel="stylesheet" href="{{ asset('css/apptable.css')}}" />
@endpush


@push('scripts')
    <script type="module" src="{{ asset('js/secgeneral/estudiantes.js') }}"></script>
@endpush


@section('content')
    <titulo>ESTUDIANTES</titulo>



    <card-action title="FILTROS">
        <filter-estudiante v-model="filter"
        @initdtable="initDataTable()"
        filter_class="col-md-11 offset-md-{{ isset($backup) ? '3' : '2' }}"
        buttons_class="mt-3"
        :secretaria="true"
        :isbackup="isBackup"
        customcolumn>

            @if (isset($backup) && $backup)
                <template v-slot:buttons>
                    <input type="hidden" id="input-isBackup" value="{{$backup}}"/>
                    <button class="btn btn-sm btn-success btn-icon-split ml-2" @click="downloadBackup()">
                        <span class="icon text-white-50">
                            <i class="fas fa-cloud-download-alt"></i>
                        </span>
                        <span class="text">Descargar Backup</span>
                    </button>
                </template>
            @endif
        </filter-estudiante>
    </card-action>

    <div class="mt-3 mb-3">
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
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody class="app-text-black-1">

            </tbody>
        </table>
    </div>

    <sidebar-documentos :show="show_sidebar" @hide="show_sidebar = false" :estudiante_data="estudiante" :secretaria="true">

    </sidebar-documentos>
@endsection
