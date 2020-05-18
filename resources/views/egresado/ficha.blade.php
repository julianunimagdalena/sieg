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
</style>

@endsection

@push('components')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.25.3/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.25.3/locale/es.min.js"></script>
    @include('components.tab.tabcontent')
    @include('components.tab.tabpane')
    @include('components.app.card')
    @include('components.app.input')
    @include('components.ficha.datosbasicos')
    @include('components.ficha.datosacademicos')
    @include('components.ficha.hojadevida')
    @include('components.ficha.datoslaborales')
@endpush


@section('content')
<h3 class="text-primary text-uppercase">Ficha de Egresado</h3>
<div>Los campos con <span class="text-danger">*</span> son requeridos</div>
<div class="mt-3 decorative">
    <ul class="nav nav-tabs tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="datos-basicos-tab" data-toggle="tab" href="#datos-basicos" role="tab"
                aria-controls="datos-basicos" aria-selected="true">Datos básicos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="datos-academicos-tab" data-toggle="tab" href="#datos-academicos" role="tab"
                aria-controls="datos-academicos" aria-selected="false">Datos académicos</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="hoja-vida-tab" data-toggle="tab" href="#hoja-vida" role="tab"
                aria-controls="hoja-vida" aria-selected="false">Datos de hoja de vida</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="datos-laborales-tab" data-toggle="tab" href="#datos-laborales" role="tab"
                aria-controls="datos-laborales" aria-selected="false">Datos laborales</a>
        </li>
    </ul>

    <tab-content>
        <tab-pane id="datos-basicos" active>
            <datos-basicos />
        </tab-pane>

        <tab-pane id="datos-academicos">
            <datos-academicos />
        </tab-pane>

        <tab-pane id="hoja-vida">
            <hoja-de-vida />
        </tab-pane>

        <tab-pane id="datos-laborales">
            <datos-laborales />
        </tab-pane>
    </tab-content>
</div>
@endsection

@push('scripts')
<script type="module">

    new Vue({ el: '#app' })
</script>
@endpush
