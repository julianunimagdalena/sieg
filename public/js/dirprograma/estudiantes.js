import { baseURL, defaultUserAvatar } from '../variables.js';
import http from '../http.js';

let vue = new Vue({
    el: '#app',
    data: () => ({
        datos: {
            tipos_grado: [],
            fechas_grado: [],
            programas: [],
        },
        dataTable: undefined,
        filter: {

        },
        estudiante: undefined,
        show_est: false,
        show_dir: false
    }),
    methods: {
        showSidebar(data, sidebar)
        {
            if(sidebar === 'est')
            {
                this.show_est = true;

                http.get(`direccion/proceso-grado/${data}`).then(
                    ({ data }) =>
                    {
                        this.estudiante = data;
                        this.estudiante.info.foto = data.info.foto || defaultUserAvatar;
                    }
                );
            }else {
                this.show_dir = true;
            }
        },
        onChangeTipoGrado()
        {
            if(this.filter.tipo_grado_id !== undefined)
                http.get(`recursos/fechas-grado?tipo_grado_id=${this.filter.tipo_grado_id}`).then(
                    ({ data }) =>
                    {
                        this.datos.fechas_grado = data;
                        this.initDataTable();
                    }
                );
        },
        initFilter()
        {
            this.filter = {
                programa_id: String(this.datos.programas[0].id)
            };
            this.datos.fechas_grado = [];
            this.initDataTable();
        },
        initDataTable()
        {
            if (this.dataTable) {
                this.dataTable.destroy()
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
                        {data: "identificacion"},
                        {data: "celular"},
                        {data: "estado_programa", "orderable": false},
                        {data: "estado_secretaria", "orderable": false},
                        {data: "acciones", "orderable": false}
                    ],
                    rowCallback: row => {
                        const data                      = $(row).data();
                        const tdFoto                    = row.children[0];
                        const tdNombres                 = row.children[2];
                        const tdApellidos               = row.children[3];
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



                        tdEstadoPrograma.innerHTML = `<span class="badge ${getBadgeClass(tdEstadoPrograma.innerText)}">${tdEstadoPrograma.innerText}</span>`;
                        tdEstadoSecretaria.innerHTML = `<span class="badge ${getBadgeClass(tdEstadoSecretaria.innerText)}">${tdEstadoSecretaria.innerText}</span>`;

                        tdAcciones.innerHTML = `
                            <a href="${baseURL}/direccion/estudiante/${data.id}"><i class="fas fa-edit text-warning" ></i></a>
                            <i class="fas fa-user-graduate text-primary ml-3 show-info" data-id="${data.id}" sidebar="est"></i>
                            <i class="fas fa-info-circle text-primary ml-3 show-info" data-id="${data.id}" sidebar="dir"></i>
                        `;
                    }
                });

                $('#tabla-estudiante').on( 'draw.dt', function () {
                    $('.show-info').on('click', function(){
                        vue.showSidebar($(this).attr('data-id'), $(this).attr('sidebar'));
                    });
                });
        }
    },
    mounted: function ()
    {
        http.get('recursos/tipos-grado').then(
            ({ data }) =>
            {
                this.datos.tipos_grado = data;
            }
        );

        http.get('direccion/programas-coordinados').then(
            ({ data }) =>
            {
                this.datos.programas  = data;



                $(document).ready( () => {
                    this.initFilter();
                });
            }
        );

    }
});




