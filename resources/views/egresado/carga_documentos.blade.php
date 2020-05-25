@extends('layouts.principal')
@section('title', 'Egresado')

@push('components')
@include('components.app.Titulo')
@include('components.app.alert')
@include('components.app.SelectorInfoGrado')
@include('components.app.card')
@include('components.cards.DocumentoCard')
@include('components.modals.Modal')
@include('components.modals.CargaDocumentoModal')
@include('components.inputs.InputFile')
@endpush

@section('content')

<titulo>Carga documentos de grado</titulo>
<alert color="primary">A continuacion los documentos que debe cargar como estudiante: </alert>
<card>
    <selector-info-grado v-model="info" :infos="infos"></selector-info-grado>
    <div v-if="info">
        <span class="font-weight-bold">NOMBRE DEL ESTUDIANTE:</span> <span class="text-primary">@{{info.nombre}}</span>
        <br>
        <span class="font-weight-bold">CODIGO DEL ESTUDIANTE:</span> <span class="text-primary">@{{info.codigo}}</span>
    </div>
</card>
<br>
<div v-if="info">
    <div class="row">
        <div class="col-lg-4 col-md-6 col-12" v-for="doc in info.documentos">
            <documento-card :documento="doc" :seleccionar="modalDocumento"></documento-card>
        </div>
    </div>
    <carga-documento-modal id="cargaDocumentoModal" :documento="documento" :codigo="info.codigo"
        v-on:documento-cargado="fetchData">
    </carga-documento-modal>
</div>
@endsection

@push('scripts')
<script type="module" src="{{asset('js/egresado/carga_documentos.js')}}"></script>
@endpush
