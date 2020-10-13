import http from '../http.js';
import { openModal, initBootstrapSelect } from '../functions.js';


Vue.component('datos-programas', {
    template: '#datos-programas-component',
    data: () => ({
        datos: {
            facultades: [],
            programas: [],
            jornadas: [],
            distinciones: [],
            modalidades: [],
            programas_egresado: []
        },
        form: {
            programa: {}
        },
        errors: {
            programa: {}
        }
    }),
    props: {
        c_data: {
            type: Object,
        }
    },
    watch: {
        c_data(new_v) {
            if (new_v.id)
                this.getProgramas();
        }
    },
    methods: {
        openModal,
        getProgramas() {
            http.get(`administrador/consultar-graduado-programas?persona_id=${this.c_data.datos.id}`).then(
                ({ data }) => {
                    this.datos.programas_egresado = data.programas;
                }
            );
        },
        onChangeFacultad() {
            this.errors.programa.facultad_id = undefined;

            http.get(`recursos/programas?facultad_id=${this.form.programa.facultad_id}`).then(
                ({ data }) => {
                    this.datos.programas = data;
                    initBootstrapSelect('#select-programas');
                }
            );
        },
        onSubmitFormPrograma() {
            cargando();
            http.post('administrador/update-graduado', { ... this.form.programa, persona_id: this.c_data.datos.id }).then(
                ({ data }) => {
                    alertTareaRealizada('Datos de programa guardados');
                    this.getProgramas();
                    $('#modal-add-programa').modal('hide');
                },
                ({ response }) => {
                    if (response.status === 422)
                        this.errors.programa = response.data.errors;
                    else if (response.status === 400)
                        alertErrorServidor(response.data);
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        }
    },
    mounted() {

        http.get('recursos/facultades').then(
            ({ data }) => {
                this.datos.facultades = data;
            }
        );

        http.get('recursos/modalidades-estudio').then(({ data }) => this.datos.modalidades = data);

        http.get('recursos/distinciones-estudiante').then(({ data }) => this.datos.distinciones = data);

        http.get('recursos/jornadas').then(({ data }) => this.datos.jornadas = data);


    }
});
