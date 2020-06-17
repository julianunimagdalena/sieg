@component('component', ['id' => 'fecha-grado-modal-component'])
<modal :id="id" :title="title" large :onsubmit="onSubmit" @onHide="errors.form = {}">
    <div class="form-row">
        <div class="col-md-4 col-sm-12">
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="sw-estado" v-model="form.estado">
                    <label class="custom-control-label" for="sw-estado">Activo</label>
                </div>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-6 col-sm-12 form-group">
            <app-select
                label="Tipo de Grado"
                required
                v-model="form.tipo_grado_id"
                :errors="errors.form.tipo_grado_id"
                @input="errors.form.tipo_grado_id = undefined"
            >
                <option v-for="t_grado in datos.tipos_grado" :value="t_grado.id">@{{t_grado.nombre}}</option>
            </app-select>
        </div>
        <div class="col-md-6 col-sm-12 form-group">
            <app-input
                label="Fecha"
                type="date"
                required
                v-model="form.fecha"
                :errors="errors.form.fecha"
                @input="errors.form.fecha = undefined"
            />
        </div>
    </div>

    <div class="form-row">
        <div class="col-md-6 col-sm-12 form-group">
            <app-input
                label="Inscripción Inicio"
                type="date"
                required
                v-model="form.inscripcion_fecha_inicio"
                :errors="errors.form.inscripcion_fecha_inicio"
                @input="errors.form.inscripcion_fecha_inicio = undefined"
            />
        </div>
        <div class="col-md-6 col-sm-12 form-group">
            <app-input
                label="Inscripción Fin"
                type="date"
                required
                v-model="form.inscripcion_fecha_fin"
                :errors="errors.form.inscripcion_fecha_fin"
                @input="errors.form.inscripcion_fecha_fin = undefined"
            />
        </div>
    </div>

    <div class="form-row">
        <div class="col-md-6 col-sm-12 form-group">
            <app-input
                label="Fecha Limite Dirección de Programa"
                type="date"
                required
                v-model="form.direccion_prog_fecha_fin"
                :errors="errors.form.direccion_prog_fecha_fin"
                @input="errors.form.direccion_prog_fecha_fin = undefined"
            />
        </div>

        <div class="col-md-6 col-sm-12 form-group">
            <app-input
                label="Fecha Limite Secretaría General"
                type="date"
                required
                v-model="form.secretaria_gen_fecha_fin"
                :errors="errors.form.secretaria_gen_fecha_fin"
                @input="errors.form.secretaria_gen_fecha_fin = undefined"
            />
        </div>
    </div>
    <div class="form-row">

        <div class="col-md-6 col-sm-12 form-group">
            <app-input
                label="Fecha Limite Entrega de Documentos"
                type="date"
                required
                v-model="form.doc_est_fecha_fin"
                :errors="errors.form.doc_est_fecha_fin"
                @input="errors.form.doc_est_fecha_fin = undefined"
            />
        </div>

        <div class="col-md-6 col-sm-12 form-group">
            <app-input
                label="Paz y Salvos Fecha Fin"
                type="date"
                required
                v-model="form.paz_salvo_fecha_fin"
                :errors="errors.form.paz_salvo_fecha_fin"
                @input="errors.form.paz_salvo_fecha_fin = undefined"
            />
        </div>
    </div>

    <div class="form-row">
        <div class="col-md-12 col-sm-12 form-group">
            <app-input
                label="Nombre"
                required
                v-model="form.nombre"
                :errors="errors.form.nombre"
                @input="errors.form.nombre = undefined"
            />
        </div>
    </div>

    <div class="form-row">
        <div class="col-md-12 col-sm-12 form-group">
            <app-input
                label="Observaciones"
                type="textarea"
                v-model="form.observacion"
                :errors="errors.form.observacion"
                @input="errors.form.observacion = undefined"
            />
        </div>
    </div>
</modal>
@endcomponent

@push('scripts')
<script type="module" src="{{ asset('js/administrador/fecha_grado_modal.js')}}"></script>
@endpush
