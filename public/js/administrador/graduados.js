import http from '../http.js';
import { baseURL } from '../variables.js';
import { initBootstrapSelect } from '../functions.js';

new Vue({
    el: '#app',
    data: () => ({
        datos: {
            facultades: [],
            programas: [],
            fechas_grado: [],
            tipos_grado: [],
            modalidades: [],
            generos: []
        },
        form: {
            graduados: {}
        },
        errors: {
            graduados: {}
        },
        filter: {},
        dataTable: undefined
    }),
    methods: {
        onChangeFacultad() {
            this.datos.programas = [];
            this.initDataTable();
            http.get(`recursos/programas?facultad_id=${this.filter.facultad_id}`).then(
                ({ data }) => {
                    this.datos.programas = data;
                    initBootstrapSelect('#select-programas');
                }
            )
        },
        onChangeTipoGrado() {
            if (this.filter.tipo_grado_id !== undefined) {

                http.get(`recursos/fechas-grado?tipo_grado_id=${this.filter.tipo_grado_id}`).then(
                    ({ data }) => {
                        this.datos.fechas_grado = data;
                        initBootstrapSelect('#select-fecha-grado');
                    }
                );
            } else {
                this.datos.fechas_grado = [];
            }
            this.initDataTable();
        },
        onSubmitRegistrarGraduados() {
            cargando();

            http.post('administrador/registrar-graduados', this.form.graduados).then(
                ({ data }) => {
                    $('#modalRegistrarGraduados').modal('hide');

                    this.form.graduados = {};
                    this.errors.graduados = {};

                    alertTareaRealizada(`Se registraron ${data.registrados} y actualizaron ${data.actualizados} egresados`);

                },
                ({ response }) => {
                    if (response.status === 422)
                        this.errors.graduados = response.data.errors;
                    if (response.status === 400)
                        alertErrorServidor(response.data);
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        },
        initFilter() {
            this.filter = {};
            this.initDataTable();
            initBootstrapSelect();
        },
        initDataTable() {
            if (this.dataTable) {
                $('.show-info').unbind('click');
                this.dataTable.destroy();
            }

            this.dataTable = $('#tabla-graduados').DataTable(
                {
                    serverSide: true,
                    ajax: {
                        url: baseURL + '/administrador/graduados',
                        type: 'POST',
                        data: this.filter
                    },
                    responsive: true,
                    columns: [
                        { data: "identificacion" },
                        { data: "nombres" },
                        { data: "apellidos" },
                        { data: "facultad" },
                        { data: "programa" },
                        { data: "fecha_grado" },
                        { data: "acciones", "orderable": false }
                    ],
                    rowCallback: row => {
                        const data = $(row).data();
                        const tdAcciones = row.children[row.children.length - 1];



                        tdAcciones.innerHTML = `
                            <a href="${baseURL}/administrador/graduado/${data.id}">
                                <i class="fas fa-user"></i>
                            </a>
                        `;
                    }
                }
            );
        }
    },

    created() {
        http.get('recursos/tipos-grado').then(({ data }) => this.datos.tipos_grado = data);

        http.get('recursos/modalidades-estudio').then(({ data }) => this.datos.modalidades = data);

        http.get('recursos/generos').then(({ data }) => this.datos.generos = data);

        http.get('recursos/facultades').then(({ data }) => {
            this.datos.facultades = data;
            this.initDataTable();
        });


    }
})
