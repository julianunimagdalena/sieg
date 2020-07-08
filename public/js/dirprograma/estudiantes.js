import { baseURL, defaultUserAvatar } from '../variables.js';
import { verDocumento  } from '../functions.js';
import http from '../http.js';

let vue = new Vue({
    el: '#app',
    data: () => ({
        datos: {
            tipos_grado: [],
            fechas_grado: [],
            programas: [],
            documentos: [],
            estudiante: {}
        },
        dataTable: undefined,
        forms: {
            documento: {},
        },
        errors: {
            documento: {},
            estudiante: {
                extra: {}
            }
        },
        filter: {

        },
        estudiante_id: undefined,
        estudiante: undefined,
        show_est: false,
        show_dir: false
    }),
    methods: {
        verDocumento,
        initInfoExtraEstudiante()
        {
            http.get(`direccion/info-adicional-estudiante/${this.estudiante.id}`).then(
                ({ data }) =>
                {
                    this.estudiante.extra = data;
                }
            );
        },
        showSidebar(data, sidebar)
        {
            this.estudiante = {id: data, info: {}, extra: {}};

            if(sidebar === 'est')
                this.show_est = true;
            else
                this.show_dir = true;

        },
        showEstudiante(id)
        {
            this.estudiante_id = id;
        },
        actualizarEstudiante()
        {
            cargando();
            http.get(`direccion/actualizar-estudiante/${this.datos.estudiante.id}`).then(
                ({ data }) =>
                {
                    alertTareaRealizada();
                },
                err =>
                {
                    alertErrorServidor();
                }
            ).then(cerrarCargando);
        },
        initDataTable()
        {
            if (this.dataTable) {
                $('.show-info').unbind('click');
                this.dataTable.destroy();
            }

            this.dataTable = $('#tabla-estudiante').DataTable(
                {
                    serverSide: true,
                    ajax: {
                        url: baseURL + '/direccion/obtener-estudiantes',
                        type: 'POST',
                        data: this.filter
                    },
                    responsive: true,
                    columns: [
                        {data: "foto", "orderable": false},
                        {data: "codigo"},
                        {data: "nombres"},
                        {data: "apellidos"},
                        {data: "fecha_grado"},
                        {data: "estado"},
                        {data: "estado_programa", "orderable": false},
                        {data: "estado_secretaria", "orderable": false},
                        {data: "acciones", "orderable": false}
                    ],
                    rowCallback: row => {
                        const data                      = $(row).data();
                        const tdFoto                    = row.children[0];
                        const tdNombres                 = row.children[2];
                        const tdApellidos               = row.children[3];
                        const tdEstadoEst               = row.children[5];
                        const tdEstadoPrograma          = row.children[6];
                        const tdEstadoSecretaria        = row.children[7];
                        const tdAcciones                = row.children[ row.children.length - 1];
                        const fotoUrl                   = tdFoto.innerText || '/img/sin_perfil.png';


                        $(row).addClass('TableRow TableRow-Centered TableRow-Rounded');
                        //$(row.children[1]).addClass('font-weight-bold');
                        $(tdNombres).addClass('text-capitalize');
                        $(tdApellidos).addClass('text-capitalize');
                        $(tdEstadoPrograma).addClass('TableItem-Center');
                        $(tdEstadoSecretaria).addClass('TableItem-Center');

                        tdNombres.innerText     = tdNombres.innerText.toLowerCase();
                        tdApellidos.innerText   = tdApellidos.innerText.toLowerCase();


                        tdFoto.innerHTML = `<img src="${baseURL}${fotoUrl}" alt="foto-estudiante" class="img-fluid Table-Image"/>`;


                        tdEstadoEst.innerHTML = `<i class="badge ${getBadgeClass(tdEstadoEst.innerText)}">${tdEstadoEst.innerText}</i>`;
                        tdEstadoPrograma.innerHTML = `<i class="badge ${getBadgeClass(tdEstadoPrograma.innerText)}">${tdEstadoPrograma.innerText}</i>`;
                        tdEstadoSecretaria.innerHTML = `<i class="badge ${getBadgeClass(tdEstadoSecretaria.innerText)}">${tdEstadoSecretaria.innerText}</i>`;

                        tdAcciones.innerHTML = `
                            <i class="fas fa-info-circle text-primary show-estudiante" data-id="${data.id}"></i>
                            <i class="fas fa-user-graduate text-primary ml-3 show-info" data-id="${data.id}" sidebar="est"></i>
                            <i class="fas fa-edit text-primary ml-3 show-info" data-id="${data.id}" sidebar="dir"></i>
                        `;
                    }
            });
        }
    },
    mounted: function ()
    {
    }
});




$('#tabla-estudiante').on( 'draw.dt', function () {
    $('.show-info').on('click', function(){
        vue.showSidebar($(this).attr('data-id'), $(this).attr('sidebar'));
    });
    $('.show-estudiante').on('click', function(){
        vue.showEstudiante($(this).attr('data-id'));
    });
});
