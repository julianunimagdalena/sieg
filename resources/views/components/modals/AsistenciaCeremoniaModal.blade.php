@component('component', ['id' => 'asistencia-ceremonia-modal-component'])
<modal :id="id" title="Confirmar asistencia a ceremonia" v-on:show="fetchData" :onSubmit="submit" buttonText="Confirmar"
    :buttonDisabled="cargando">
    <div class="form-group">
        <div class="d-inline-block">
            <label>¿El egresado asistirá a la ceremonia de grado?</label>
        </div>
        <div class="d-inline-block ml-2">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="asistencia-si" v-model="input.confirmacion_asistencia"
                    v-on:input="errors.confirmacion_asistencia=undefined" :value="true">
                <label class="form-check-label" for="asistencia-si">Si</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="asistencia-no" v-model="input.confirmacion_asistencia"
                    v-on:input="errors.confirmacion_asistencia=undefined" :value="false">
                <label class="form-check-label" for="asistencia-no">No</label>
            </div>
        </div>
        <span class="text-danger" v-if="errors.confirmacion_asistencia">@{{errors.confirmacion_asistencia[0]}}</span>
    </div>
    <div v-if="input.confirmacion_asistencia">
        <div class="form-group row">
            <label class="col-md-5">Talla camisa</label>
            <div class="col-md-7">
                <select class="form-control" v-model="input.talla_camisa" v-on:input="errors.talla_camisa=undefined">
                    <option :value="undefined" hidden>Seleccione una opción</option>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                    <option value="XXL">XXL</option>
                </select>
                <span class="text-danger" v-if="errors.talla_camisa">
                    @{{errors.talla_camisa[0]}}
                </span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-5">Estatura (m)</label>
            <div class="col-md-7">
                <input class="form-control" type="number" step="0.01" v-model="input.estatura"
                    v-on:input="errors.estatura=undefined" placeholder="Estatura">
                <span class="text-danger" v-if="errors.estatura">
                    @{{errors.estatura[0]}}
                </span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-5">Tamaño del birrete</label>
            <div class="col-md-7">
                <select class="form-control" v-model="input.tamano_birrete"
                    v-on:input="errors.tamano_birrete=undefined">
                    <option :value="undefined" hidden>Seleccione una opción</option>
                    <option value="PEQUEÑO">PEQUEÑO</option>
                    <option value="MEDIANO">MEDIANO</option>
                    <option value="GRANDE">GRANDE</option>
                </select>
                <span class="text-danger" v-if="errors.tamano_birrete">
                    @{{errors.tamano_birrete[0]}}
                </span>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-5">Número de acompañantes</label>
            <div class="col-md-7">
                <select class="form-control" v-model="input.num_acompanantes"
                    v-on:input="errors.num_acompanantes=undefined">
                    <option :value="undefined" hidden>Seleccione una opción</option>
                    <option value="NINGUNO">NINGUNO</option>
                    <option value="UNO">UNO</option>
                    <option value="DOS">DOS</option>
                </select>
                <span class="text-danger" v-if="errors.num_acompanantes">
                    @{{errors.num_acompanantes[0]}}
                </span>
            </div>
        </div>
    </div>
</modal>
@endcomponent

@push('scripts')
<script type="module">
    import http from '{{Request::root()}}/js/http.js';

    Vue.component('asistencia-ceremonia-modal', {
        template: "#asistencia-ceremonia-modal-component",
        props: {
            id: String,
            codigo: String
        },
        data: () => ({
            input: {},
            errors: {},
            cargando: false
        }),
        methods: {
            async fetchData() {
                this.cargando = true;

                http.get('egresado/info-asistencia-ceremonia/' + this.codigo).then(res => {
                    const input = {};

                    Object.entries(res.data).forEach(v => {
                        if (v[1]) input[v[0]] = v[1];
                    });

                    this.input = input;
                    this.errors = {};
                    this.cargando = false;
                });
            },
            submit() {
                this.cargando = true;

                http.post('egresado/asistencia-ceremonia', this.input).then(
                    res => {
                        this.$emit('complete');
                        $('#' + this.id).modal('hide');
                        swal('Éxito', 'Asistencia a ceremonia confirmada', 'success');
                    },
                    err => {
                        if (err.response.status === 422) this.errors = err.response.data.errors;
                        else swal('Error', err.response.message, 'error');
                    }
                ).then(() => this.cargando = false);
            }
        }
    });
</script>
@endpush
