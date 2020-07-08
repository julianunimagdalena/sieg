import { baseURL } from '../variables.js';
import { objectToParameter } from '../functions.js';
import http from '../http.js';

const vue = new Vue({
    el: '#app',
    data: () => ({
        filter: {

        },
        estudiante: {

        },
        estudiante_id: undefined,
        isBackup: false,
        show_sidebar: false,
        show_est: false,
        dataTable: undefined
    }),
    methods: {
        objectToParameter,
        showSidebar(data, sidebar)
        {
            this.estudiante = {id: data, info: {}, extra: {}};
            if(sidebar === 'est')
                this.show_est = true;
            else
                this.show_sidebar = true;
        },

        showEstudiante(id)
        {
            this.estudiante_id = id;
        },
        downloadBackup()
        {
            cargando('Procesando...')
            http.post('backup/estudiantes', this.filter).then(
                ({ data }) =>
                {
                    const a = document.createElement('a');

                    data.forEach(file => {
                        a.download = file;
                        a.href = baseURL + '/backup/archivo/' + file;
                        a.click();
                    });


                    alertTareaRealizada(data.length > 0 ? 'Copia de seguridad realizada con éxito' : 'No hay estudiantes para descargar');
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
                        url: baseURL + '/secgeneral/estudiantes',
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
                        {data: "estado_secretaria"},
                        {data: "estado_programa", "orderable": false},
                        {data: "acciones", "orderable": false}
                    ],
                    rowCallback: row =>
                    {
                        const data                      = $(row).data();
                        const tdFoto                    = row.children[0];
                        const tdEstadoEst               = row.children[5];
                        const tdEstadoPrograma          = row.children[6];
                        const tdAcciones                = row.children[ row.children.length - 1];

                        const fotoUrl                   = tdFoto.innerText || '/img/sin_perfil.png';


                        $(row).addClass('TableRow TableRow-Centered TableRow-Rounded');

                        tdFoto.innerHTML = `<img src="${baseURL}${fotoUrl}" alt="foto-estudiante" class="img-fluid Table-Image"/>`;

                        tdEstadoEst.innerHTML = `<i class="badge ${getBadgeClass(tdEstadoEst.innerText)}">${tdEstadoEst.innerText}</i>`;
                        tdEstadoPrograma.innerHTML = `<i class="badge ${getBadgeClass(tdEstadoPrograma.innerText)}">${tdEstadoPrograma.innerText}</i>`;

                        if(!this.isBackup)
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
    mounted()
    {
        this.isBackup = Boolean(Number($('#input-isBackup').val()));
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

