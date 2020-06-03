@extends('layouts.principal')
@section('title', 'Ver Estudiante')

@push('components')

@endpush

@push('csscomponent')

@endpush

@push('scripts')
    @include('components.app.Titulo')
    @include('components.app.card')
    @include('components.app.input')
    <script type="module" src="{{ asset('js/dirprograma/estudiante.js') }}"></script>
@endpush

@section('content')

<div class="row mb-3">

    <div class="col-md-10">
        <titulo>estudiante</titulo>
    </div>

    <div class="col-md-2">
        <button class="btn btn-primary btn-icon-split float-right" @click="actualizar()">
            <span class="icon text-white-50">
                <i class="fas fa-sync-alt"></i>
            </span>
            <span class="text">Actualizar</span>
        </button>
    </div>
</div>


<card title="DATOS DEL ESTUDIANTE">
    <div class="text-center">
        <img :src="estudiante.foto" alt="" class="img-fluid img-perfil">
    </div>
    <form>
        <div class="form-group form-row">
            <div class="col-md-4">
                <app-input
                    label="Nombre"
                    v-model="estudiante.nombres"
                    disabled
                />
            </div>
            <div class="col-md-4">
                <app-input
                    label="Apellido"
                    v-model="estudiante.apellidos"
                    disabled
                />
            </div>
            <div class="col-md-1">
                <app-input
                    label="T Documento"
                    v-model="estudiante.tipo_documento"
                    disabled
                />
            </div>
            <div class="col-md-3">
                <app-input
                    label="Documento"
                    v-model="estudiante.documento"
                    disabled
                />
            </div>
        </div>
        <div class="form-group form-row">
            <div class="col-md-4">
                <app-input
                    label="Municipio de Expedición"
                    v-model="estudiante.municipio_expedicion"
                    disabled
                />
            </div>
            <div class="col-md-4">
                <app-input
                    label="Lugar de Nacimiento"
                    v-model="estudiante.lugar_nacimiento"
                    disabled
                />
            </div>
            <div class="col-md-4">
                <app-input
                    label="Fecha Nacimiento"
                    v-model="estudiante.fecha_nacimiento"
                    disabled
                />
            </div>
        </div>
        <div class="form-group form-row">
            <div class="col-md-3">
                <app-input
                    label="Correo"
                    v-model="estudiante.correo"
                    disabled
                />
            </div>
            <div class="col-md-3">
                <app-input
                    label="Celular"
                    v-model="estudiante.celular"
                    disabled
                />
            </div>
            <div class="col-md-3">
                <app-input
                    label="Programa"
                    v-model="estudiante.programa"
                    disabled
                />
            </div>
            <div class="col-md-3">
                <app-input
                    label="Código"
                    v-model="estudiante.codigo"
                    disabled
                />
            </div>
        </div>
    </form>
</card>

<div class="">

</div>

@endsection
