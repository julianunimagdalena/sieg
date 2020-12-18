import { baseURL } from '../variables.js';
import { objectToParameter, fileResponse, initBootstrapSelect } from '../functions.js';
import http from '../http.js';

const vue = new Vue({
    el: '#app',
    data: () => ({
        filter: {

        },
        estudiante: {

        },
        form: {
            encuesta: {}
        },
        errors: {
            encuesta: {}
        },
        programas: [],
        estudiante_id: undefined,
        isBackup: false,
        show_sidebar: false,
        show_est: false,
        dataTable: undefined
    }),
    methods: {
        objectToParameter,
        showSidebar(data, sidebar, foto = null) {

            this.estudiante = { id: data, info: {}, extra: {}, foto };

            if (sidebar === 'est')
                this.show_est = true;
            else
                this.show_dir = true;

        },
        showEstudiante(id) {
            this.estudiante_id = id;
        },
        downloadBackup() {
            cargando('Procesando...')
            http.post('backup/estudiantes', this.filter).then(
                ({ data }) => {
                    const a = document.createElement('a');

                    data.forEach(file => {
                        a.download = file;
                        a.href = baseURL + '/backup/archivo/' + file;
                        a.click();
                    });


                    alertTareaRealizada(data.length > 0 ? 'Copia de seguridad realizada con éxito' : 'No hay estudiantes para descargar');
                },
                err => {
                    alertErrorServidor();
                }
            ).then(cerrarCargando);
        },
        onLoadProgramas(data) {
            this.programas = data;
        },
        onClickDescargarEncuesta() {
            this.form.encuesta = {};
            initBootstrapSelect('#s-programas');
            $('#modalDescargarEncuesta').modal('show');
        },
        onSubmitDescargarEncuesta() {
            cargando();
            const options = { 'responseType': 'arraybuffer' };
            http.post('administrador/descargar-encuesta', { ...this.form.encuesta, key: 'momento_0' }, options).then(
                (response) => {
                    this.form.encuesta = {};
                    fileResponse(response);
                    alertTareaRealizada();
                    $('#modalDescargarEncuesta').modal('hide');
                },
                ({ response }) => {
                    if (response.status === 422) {
                        alertErrorServidor('Faltan algunos campos obligatorios por llenar');
                        //this.errors.encuesta = response.data.errors;
                    }
                }
            ).finally(cerrarCargando);
        },
        initDataTable() {
            if (this.dataTable) {
                $('.show-info').unbind('click');
                this.dataTable.destroy();
            }

            this.dataTable = $('#tabla-estudiante').DataTable(
                {
                    serverSide: true,
                    ajax: {
                        url: baseURL + '/secgeneral/estudiantes',
                        type: 'POST',
                        data: this.filter
                    },
                    responsive: true,
                    columns: [
                        { data: "foto", "orderable": false },
                        { data: "codigo" },
                        { data: "nombres" },
                        { data: "apellidos" },
                        { data: "fecha_grado" },
                        { data: "estado_secretaria" },
                        { data: "estado_programa", "orderable": false },
                        { data: "acciones", "orderable": false }
                    ],
                    rowCallback: row => {
                        const data = $(row).data();
                        const tdFoto = row.children[0];
                        const tdEstadoEst = row.children[5];
                        const tdEstadoPrograma = row.children[6];
                        const tdAcciones = row.children[row.children.length - 1];

                        const fotoUrl = tdFoto.innerText || '/img/sin_perfil.png';


                        $(row).addClass('TableRow TableRow-Centered TableRow-Rounded');

                        let foto = !tdFoto.innerText ? baseURL + fotoUrl : 'data:image/*;base64,' + fotoUrl;
                        tdFoto.innerHTML = `<img src="${foto}" id="foto-${data.id}" alt="foto-estudiante" class="img-fluid Table-Image"/>`;

                        tdEstadoEst.innerHTML = `<i class="badge ${getBadgeClass(tdEstadoEst.innerText)}">${tdEstadoEst.innerText}</i>`;
                        tdEstadoPrograma.innerHTML = `<i class="badge ${getBadgeClass(tdEstadoPrograma.innerText)}">${tdEstadoPrograma.innerText}</i>`;

                        if (!this.isBackup)
                            tdAcciones.innerHTML = `
                            <i class="fas fa-info-circle text-primary show-estudiante" data-id="${data.id}"></i>
                            <i class="fas fa-user-graduate text-primary ml-3 show-info" data-id="${data.id}" sidebar="est"></i>
                            <i class="fas fa-edit text-primary ml-3 show-info" data-id="${data.id}" sidebar="dir"></i>
                        `;
                    }
                }
            );
        }
    },
    mounted() {
        this.isBackup = Boolean(Number($('#input-isBackup').val()));
    }
});



$('#tabla-estudiante').on('draw.dt', function () {
    $('.show-info').on('click', function () {
        let id = $(this).attr('data-id');
        vue.showSidebar(id, $(this).attr('sidebar'), $(`#foto-${id}`).attr('src'));
    });
    $('.show-estudiante').on('click', function () {
        vue.showEstudiante($(this).attr('data-id'));
    });
});

