import http from '../http.js';
import { initBootstrapSelect } from '../functions.js';
import { baseURL } from '../variables.js';

const vue = new Vue({
    el: '#app',
    data: () => ({
        input: {
            estado: 'pendiente'
        },
        programas: [],
        estudiante_id: null,
        dataTable: undefined,
        form_motivo: {},
        errors: {
            form_motivo: []
        }
    }),
    watch: {
    },
    methods: {
        onChangeFilter() {
            this.initDataTable();
        },
        limpiarFiltro() {
            this.input = {
                estado: 'pendiente'
            };
            initBootstrapSelect();
            this.initDataTable();
        },
        onmodalHide() {
            this.form_motivo = {};
            this.errors.form_motivo = [];
        },
        showModalMotivo(estudiante_id) {
            this.estudiante_id = estudiante_id;
            $('#modalMotivo').modal('show');
        },
        onSubmitMotivo() {
            if (this.form_motivo.motivo && this.form_motivo.motivo.length > 0) {
                this.cambiarEstadoEstudiante(this.estudiante_id, this.form_motivo.motivo, (err) => {
                    console.log(err);
                });
            } else {
                console.log(this.form_motivo.motivo);
                this.errors.form_motivo = ["El campo es obligatorio"];
            }
        },
        cambiarEstadoEstudiante(estudiante_id, motivo = null, onerror = undefined) {
            cargando();
            http.post('dependencia/cambiar-estado', { estudiante_id, motivo }).then(
                () => {
                    if (motivo) {
                        $('#modalMotivo').modal('hide');
                        this.estudiante_id = null;
                    }
                    this.initDataTable();
                    alertTareaRealizada();
                },
                err => {
                    if (onerror) onerror(err);
                }
            ).finally(cerrarCargando);
        },
        initDataTable() {
            if (this.dataTable) {
                /* $('.aprobar-estudiante').unbind('click');
                 $('.rechazar-estudiante').unbind('click');*/
                this.dataTable.destroy();
            }
            this.dataTable = $('#tabla-estudiantes').DataTable({
                serverSide: true,
                ajax: {
                    url: baseURL + '/dependencia/estudiantes',
                    type: 'POST',
                    data: this.input
                },
                responsive: true,
                columns: [
                    { data: "foto", "orderable": false },
                    { data: "nombre" },
                    { data: "codigo" },
                    { data: "programa" },
                    { data: "fecha_grado" },
                    { data: "acciones" },
                ],
                rowCallback: row => {
                    const data = $(row).data();
                    const tdFoto = row.children[0];
                    const tdAcciones = row.children[row.children.length - 1];

                    $(row).addClass('TableRow TableRow-Centered TableRow-Rounded');

                    let foto = !tdFoto.innerText ? baseURL + '/img/sin_perfil.png' : 'data:image/*;base64,' + tdFoto.innerText;

                    tdFoto.innerHTML = `<img src="${foto}" id="foto-${data.id}" alt="foto-estudiante" class="img-fluid Table-Image"/>`;

                    if (this.input.estado !== 'aprobado') {
                        tdAcciones.innerHTML += `<i class="fas fa-check text-success aprobar-estudiante" data-id="${data.id}"></i>`;
                    }

                    tdAcciones.innerHTML += `<i class="fas fa-times text-danger ml-3 rechazar-estudiante" data-id="${data.id}" ></i>`;
                }
            });
        }
    },
    mounted() {
        http.get('recursos/programas').then(
            ({ data }) => {
                this.programas = data;
                initBootstrapSelect();
            }
        );
        this.initDataTable();
    }
});


$('#tabla-estudiantes').on('draw.dt', function () {
    $('.aprobar-estudiante').on('click', function () {
        let id = $(this).attr('data-id');
        vue.cambiarEstadoEstudiante(id);
    });
    $('.rechazar-estudiante').on('click', function () {
        let id = $(this).attr('data-id');
        vue.showModalMotivo(id);
    });
});
