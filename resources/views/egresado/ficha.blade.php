@extends('layouts.principal')
@section('title', 'Ficha de Egresado')


@section('css')
<style>
    .tab-content {
        padding: 15px 0;
    }

    .nav-link {
        border: 0 !important;
    }

    .tab .nav-link {
        color: black !important;
    }

    .tab .nav-link.active {
        border-bottom: 3px solid #D17900 !important;
        font-weight: 700;
    }

    .decorative.tab-content {
        border: 1px solid transparent;
        padding: 15px 0;
        border-radius: 0 5px 5px 5px;
    }

    .decorative.nav-tabs {
        border: 0;
    }

    .decorative .nav-link.active {
        border: 0;
        background-color: transparent !important;
        color: black !important;
    }

    .no-opacity {
        opacity: 0;
    }
</style>
<link rel="stylesheet" href="https://unpkg.com/driver.js/dist/driver.min.css">
@endsection

@push('components')
@include('components.tab.tabcontent')
@include('components.tab.tabpane')
@include('components.app.card')
@include('components.app.card-action')
@include('components.app.list-group')
@include('components.ficha.user-avatar')
@include('components.app.modal')
@include('components.app.required')
@include('components.app.input')
@include('components.app.select')
@include('components.ficha.datosbasicos')
@include('components.ficha.datosacademicos')
@include('components.ficha.hojadevida')
@include('components.ficha.datoslaborales')
@include('components.ficha.datosprogramas')
@endpush

<?php
    $isAdminView = (isset($isAdmin) && $isAdmin == true) ? 'true' : 'false';

    $isRegister = isset($register) && $register == true;

?>

@section('content')
<input type="hidden" value="{{ $isRegister }}" id="is-register" />
<div class="row">
    <div class="col col-md-8">
        <h3 class="text-primary text-uppercase">Ficha de Egresado</h3>
        <div>Los campos con <span class="text-danger">*</span> son requeridos</div>
    </div>
    @if(!$isRegister)
    <div class="col col-md-4" id="progreso-view">
        <div class="font-weight-bold app-text-back-1 mb-3">
            Progreso
        </div>
        <div class="progress" style="height: 10px;">
            <div class="progress-bar progress-bar-animated font-weight-bold"
                :class="{'bg-success': progress == 100, 'bg-warning': progress < 100}" role="progressbar"
                :style="{width: progress+'%'}" :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100">
                @{{ progress }} %
            </div>
        </div>
    </div>
    @endif
</div>
<div class="mt-3 decorative">
    <ul class="nav nav-tabs tab" role="tablist" id="tab-list">
        <li class="nav-item">
            <a v-bind:class="{'active' : active.datos_basicos}" class="nav-link" id="datos-basicos-tab"
                data-toggle="tab" href="#datos-basicos" role="tab" @click="setActiveTab('datos_basicos')"
                aria-controls="datos-basicos" aria-selected="true">Datos básicos</a>
        </li>
        @if($isRegister)
        <li class="nav-item">
            <a v-bind:class="{'active' : active.datos_programas}" class="nav-link" id="datos-programas-tab"
                v-if="egresado_data.datos.id || egresado_data.id" data-toggle="tab" href="#datos-programas" role="tab"
                @click="setActiveTab('datos_programas')" aria-controls="datos-programas" aria-selected="false">
                Datos Programas</a>
        </li>
        @else
        <li class="nav-item">
            <a v-bind:class="{'active' : active.datos_academicos}" class="nav-link" id="datos-academicos-tab"
                data-toggle="tab" href="#datos-academicos" role="tab" @click="setActiveTab('datos_academicos')"
                aria-controls="datos-academicos" aria-selected="false">Datos académicos</a>
        </li>
        <li class="nav-item">
            <a v-bind:class="{'active' : active.hoja_de_vida}" class="nav-link" id="hoja-vida-tab" data-toggle="tab"
                href="#hoja-vida" role="tab" @click="setActiveTab('hoja_de_vida')" aria-controls="hoja-vida"
                aria-selected="false">Datos de hoja de vida</a>
        </li>
        <li class="nav-item">
            <a v-bind:class="{'active' : active.datos_laborales}" class="nav-link" id="datos-laborales-tab"
                data-toggle="tab" href="#datos-laborales" role="tab" @click="setActiveTab('datos_laborales')"
                aria-controls="datos-laborales" aria-selected="false">Datos laborales</a>
        </li>
        @endif
    </ul>

    <tab-content>
        <tab-pane id="datos-basicos" :active="active.datos_basicos">
            <datos-basicos :admin="{{$isAdminView}}" :register="{{ $isRegister ? 'true' : 'false' }}"
                :c_data="egresado_data" :updateregister="updateRegister" />
        </tab-pane>


        @if($isRegister)
        <tab-pane id="datos-programas" :active="active.datos_programas">
            <datos-programas :c_data="egresado_data" />
        </tab-pane>
        @else
        <tab-pane id="datos-academicos" :active="active.datos_academicos">
            <datos-academicos @updateprogreso="updateProgreso()" :admin="{{$isAdminView}}" />
        </tab-pane>

        <tab-pane id="hoja-vida" :active="active.hoja_de_vida">
            <hoja-de-vida @updateprogreso="updateProgreso()" :admin="{{$isAdminView}}" />
        </tab-pane>

        <tab-pane id="datos-laborales" :active="active.datos_laborales">
            <datos-laborales @updateprogreso="updateProgreso()" :admin="{{$isAdminView}}" />
        </tab-pane>
        @endif
    </tab-content>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.25.3/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.25.3/locale/es.min.js"></script>
<script src="https://unpkg.com/driver.js/dist/driver.min.js"></script>
<script type="module" src="{{ asset('js/ficha/main.js')}}"></script>
@if(!session('ur')->isRol('administrador'))
<script type="module" src="{{ asset('js/ficha/tutorial.js')}}"></script>
@endif
@endpush
