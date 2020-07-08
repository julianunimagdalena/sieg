@component('component', ['id' => 'modal-informacion-estudiante-component'])
<modal
    id="modalInformacionEstudiante"
    title="Información Estudiante"
    :onsubmit="actualizarEstudiante"
    @onhide="$emit('hide')"
    large
    buttontext="Actualizar">

    <div class="form-group form-row">
        <div class="col-md-6">
            <app-input
                label="Nombre"
                v-model="datos.estudiante.nombres"
                disabled
            />
        </div>
        <div class="col-md-6">
            <app-input
                label="Apellido"
                v-model="datos.estudiante.apellidos"
                disabled
            />
        </div>
    </div>
    <div class="form-group form-row">
        <div class="col-md-4">
            <app-input
                label="Municipio de Expedición"
                v-model="datos.estudiante.municipio_expedicion"
                disabled
            />
        </div>
        <div class="col-md-3">
            <app-input
                label="Fecha de Expedición"
                disabled
                v-model="datos.estudiante.fecha_expedicion"
            />
        </div>
        <div class="col-md-2">
            <app-input
                label="T Documento"
                v-model="datos.estudiante.tipo_documento"
                disabled
            />
        </div>
        <div class="col-md-3">
            <app-input
                label="Documento"
                v-model="datos.estudiante.documento"
                disabled
            />
        </div>
    </div>
    <div class="form-group form-row">
        <div class="col-md-6">
            <app-input
                label="Lugar de Nacimiento"
                v-model="datos.estudiante.lugar_nacimiento"
                disabled
            />
        </div>
        <div class="col-md-6">
            <app-input
                label="Fecha Nacimiento"
                v-model="datos.estudiante.fecha_nacimiento"
                disabled
            />
        </div>
    </div>
    <div class="form-group form-row">
        <div class="col-md-6">
            <app-input
                label="Correo"
                v-model="datos.estudiante.correo"
                disabled
            />
        </div>
        <div class="col-md-6">
            <app-input
                label="Celular"
                v-model="datos.estudiante.celular"
                disabled
            />
        </div>
    </div>
    <div class="form-group form-row">
        <div class="col-md-6">
            <app-input
                label="Programa"
                v-model="datos.estudiante.programa"
                disabled
            />
        </div>
        <div class="col-md-6">
            <app-input
                label="Código"
                v-model="datos.estudiante.codigo"
                disabled
            />
        </div>
    </div>
    <div class="form-group form-row">
        <div class="col-md-6">
            <app-input
                label="Titulo Grado"
                v-model="datos.estudiante.titulo_grado"
                disabled
            />
        </div>
        <div class="col-md-6">
            <app-input
                label="Modalidad Grado"
                v-model="datos.estudiante.modalidad_grado"
                disabled
            />
        </div>
    </div>

    <div class="form-group form-row">
        <div class="col-md-6">
            <app-input
                label="Descripción Opción Grado"
                v-model="datos.estudiante.descripcion_opcion_grado"
                disabled
            />
        </div>
        <div class="col-md-6">
            <app-input
                label="Nota Grado"
                v-model="datos.estudiante.nota_grado"
                disabled
            />
        </div>
    </div>
</modal>
@endcomponent

@push('scripts')
<script type="module" src="{{ asset('js/components/modals/informacion_estudiante.js') }}"></script>
@endpush
