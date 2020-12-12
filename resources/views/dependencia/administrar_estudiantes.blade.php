@extends('layouts.principal')
@section('title', 'Administrar Usuarios')

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
@include('components.app.card-action')
@include('components.app.modal')
@endpush

@section('content')
<titulo>ADMINISTRAR USUARIOS</titulo>

<div class="container">
    <card-action title="Filtro">
        <template slot="actions">
            <i class="text-primary fas fa-redo-alt action-btn" title="Reiniciar Filtro" @click="limpiarFiltro()"></i>
        </template>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Programa</label>
                    <select class="form-control bselect" v-model="input.programa_id" @change="onChangeFilter()">
                        <option :value="undefined" selected>Seleccione una Opción</option>
                        <option v-for="programa in programas" :value="programa.id">@{{ programa.nombre }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label title="Fecha Grado Inicial">Fecha G. Inicio</label>
                    <input class="form-control" type="date" v-model="input.fecha_grado_inicio"
                        @change="onChangeFilter()" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label title="Fecha Grado Final">Fecha G. Fin</label>
                    <input class="form-control" type="date" v-model="input.fecha_grado_final"
                        @change="onChangeFilter()" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Estado</label>
                    <select class="form-control" v-model="input.estado" @change="onChangeFilter()">
                        <option value="pendiente" selected>PENDIENTE</option>
                        <option value="aprobado">APROBADO</option>
                    </select>
                </div>
            </div>
        </div>
    </card-action>
</div>
<div class="mt-4">
    <table style="width: 100%" class="table dtable table-sm table-responsive-sm AppTable-Separated AppTable-List"
        id="tabla-estudiantes">
        <thead>
            <tr>
                <th>Foto</th>
                <th>Nombre</th>
                <th>Código</th>
                <th>Programa</th>
                <th>Fecha de Grado</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
</div>

<modal id="modalMotivo" title="Motivo de Rechazo" :onsubmit="onSubmitMotivo" buttontext="Enviar"
    @onhide="onmodalHide()">
    <div class="form-group">
        <label>Motivo</label>
        <textarea class="form-control" v-model="form_motivo.motivo" placeholder="Motivo">

        </textarea>
        <small class="text-danger" v-if="errors.form_motivo">@{{errors.form_motivo[0]}}</small>
    </div>
</modal>
@endsection


@push('scripts')
<script type="module" src="{{asset('js/dependencia/administrar_estudiantes.js')}}"></script>
@endpush
