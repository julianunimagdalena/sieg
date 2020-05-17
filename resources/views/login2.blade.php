@extends('layouts.login')
@section('title', 'Iniciar sesión')

@push('components')
@include('components.modals.SolicitudGradoModal')
@include('components.forms.LoginForm')
@endpush

@section('content')
<div id="app" class="container">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h2>Información</h2>
            <p>
                Bienvenido al Sistema de Administración de Egresados y Graduados
                <strong>SAEG</strong>
                <br />Si eres estudiante, puedes iniciar tu solicitud para el estudio
                de tu hoja de vida academica a través del siguiente enlace:
            </p>
            <a href="#solicitudGradoModal" data-toggle="modal" class="btn btn-primary">Solicitar estudio de hoja de vida
                academica</a>
        </div>
        <div class="col-xs-12 col-md-6">
            <h2>Acceso</h2>
            <div class="acceso">
                <login-form />
            </div>
        </div>
    </div>
    <solicitud-grado-modal id="solicitudGradoModal" />
</div>
@endsection

@push('scripts')
<script type="module">
    new Vue({ el: '#app', data: () => ({mensaje: 'hola'}) })
</script>
@endpush
