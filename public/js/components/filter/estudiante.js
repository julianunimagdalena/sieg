import http from '../../http.js';
import { initBootstrapSelect } from '../../functions.js';

Vue.component('filter-estudiante', {
    template: '#filter-estudiante-component',
    data: () => ({
        datos: {
            programas: [],
            fechas_grado: []
        },
        filter: {}
    }),
    props: {
        programas: Array,
        customcolumn: {
            type: Boolean,
            default: false
        },
        secretaria: Boolean,
        filter_class: String,
        buttons_class: String,
        isbackup: Boolean
    },
    watch: {
        value(new_v, old_v) {
            this.filter = new_v;
        }
    },
    methods: {
        onChangeTipoGrado() {
            if (this.filter.tipo_grado_id !== undefined) {
                let aditions = !this.isbackup ? '&activa=1' : '';

                http.get(`recursos/fechas-grado?tipo_grado_id=${this.filter.tipo_grado_id}${aditions}`).then(
                    ({ data }) => {
                        this.datos.fechas_grado = data;
                        initBootstrapSelect('#fecha-grado-filter');
                        this.onChange();
                    }
                );
            } else {
                this.datos.fechas_grado = [];
                this.onChange();
            }
        },
        onChange() {
            this.$emit('input', this.filter);
            this.$emit('initdtable');
        },
        initFilter() {
            if (!this.secretaria) {
                this.filter = {
                    programa_id: this.datos.programas.length > 0 ? this.datos.programas[0].id : 0
                };
            } else if (this.isbackup) {
                this.filter = {
                    estado: 'aprobado'
                };
            }
            else this.filter = {};
            initBootstrapSelect('#fecha-grado-filter');
            initBootstrapSelect();
            this.datos.fechas_grado = [];
            this.onChange();
        }
    },
    mounted() {
        let urlProgramas = this.secretaria ? 'recursos/programas' : 'direccion/programas-coordinados'

        http.get('recursos/tipos-grado').then(
            ({ data }) => {
                this.datos.tipos_grado = data;
            }
        );

        /*http.get('recursos/fechas-grado?activa=1').then(
            ({ data }) => {
                this.datos.fechas_grado = data;
                console.log(data);
            }
        );*/

        http.get(urlProgramas).then(
            ({ data }) => {
                this.datos.programas = data;

                this.initFilter();

                this.$emit('loadprogramas', data);

                initBootstrapSelect();
            }
        );
    }
});
