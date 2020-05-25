import http from './http.js';
import { objectToFormData } from './functions.js';

Vue.component('carga-documento-modal', {
    template: "#carga-documento-modal-component",
    props: {
        id: String,
        documento: Object,
        codigo: String
    },
    data: () => ({
        input: {},
        errors: {},
        cargando: false
    }),
    methods: {
        enviar() {
            const data = { ...this.input, id: this.documento.id, codigo: this.codigo };
            this.cargando = true;

            http.post('egresado/cargar-documento', objectToFormData(data)).then(
                res => {
                    this.$emit('documento-cargado');
                    $('#' + this.id).modal('hide');

                    swal('Éxito', 'Se ha cargado el documento satisfactoriamente', 'success');
                },
                err => {
                    if (err.response.status === 422) this.errors = err.response.data
                    else swal('error', 'Ha ocurrido un error por favor intente mas tarde', 'error');
                }
            ).then(() => this.cargando = false);
        }
    },
    watch: {
        documento(n, o) {
            this.input = {};
            this.errors = {};
            this.cargando = false;
        }
    }
});
