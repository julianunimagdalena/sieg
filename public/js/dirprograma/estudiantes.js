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
            estudiante: {}
        },
        filter: {

        },
        estudiante: undefined,
        show_est: false,
        show_dir: false
    }),
    methods: {
        verDocumento,
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

                http.get(`direccion/documentos-estudiante/${data}`).then(
                    ({ data }) =>
                    {
                        this.datos.documentos = data.documentos;
                        this.datos.can_aprobar = data.can_aprobar;
                    }
                );

                this.show_dir = true;
            }
            this.estudiante = {id: data, info: {}};
        },
        showEstudiante(id)
        {
            cargando();
            http.get(`direccion/datos-estudiante/${id}`).then(
                ({ data }) =>
                {
                    this.datos.estudiante           = data;
                    this.datos.estudiante.id        = id;
                    this.datos.estudiante.foto      = data.foto || defaultUserAvatar;
                    $('#modalInformacionEstudiante').modal('show');
                }
            ).then(cerrarCargando);
        },
        actualizarEstudiante()
        {
            cargando();
            http.get(`direccion/actualizar-estudiante/${this.datos.estudiante.id}`).then(
                ({ data }) =>
                {
                    alertTareaRealizada();
                    //$('#modalInformacionEstudiante').modal('hide');
                },
                err =>
                {
                    alertErrorServidor();
                }
            ).then(cerrarCargando);
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
        onDocumentoCargado()
        {
            this.showSidebar(this.estudiante.id, 'dir');
        },
        aprobarEstudiante()
        {
            alertConfirmar().then(
                (ok) =>
                {
                    if(!ok)
                        return;


                        cargando();

                        http.post('direccion/aprobar', {estudiante_id: this.estudiante.id}).then(
                            () =>
                            {
                                this.show_dir = false;
                                this.estudiante = undefined;
                                alertTareaRealizada();
                            },
                            ({response}) =>
                            {
                                if(response.status === 400)
                                    alertErrorServidor(response.data);
                                else
                                    alertErrorServidor();
                            }
                        ).then(cerrarCargando);
                }
            )
        },
        documentCanSomething(documento)
        {
            for(let key in documento)
            {
                if(key.includes('can') && documento[key])
                {
                    return true;
                }
            }
            return false;
        },
        generar(documento)
        {
            cargando();
            http.get(`direccion/generar/${documento.id}`).then(
                ( ) =>
                {
                   alertTareaRealizada();
                   this.showSidebar(this.estudiante.id, 'dir');
                },
                err =>
                {
                    alertErrorServidor();
                }
            ).then(cerrarCargando);
        },
        estadoDocumento(estado, documento_id, motivo)
        {
            cargando();
            http.post(`direccion/${estado}-documento`, {documento_id, motivo}).then(
                ( ) =>
                {
                    alertTareaRealizada();
                    this.showSidebar(this.estudiante.id, 'dir');
                    if(estado === 'rechazar')
                        $('#modalRechazarDocumento').modal('hide');
                },
                ({ response }) =>
                {
                    if(response.status === 400)
                        alertErrorServidor(response.data);
                    else if (response.status === 422)
                        this.errors.documento = response.data.errors;
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        },
        rechazarEstudiante()
        {
            alertConfirmar().then(
                (ok) =>
                {
                    if(!ok)
                        return;

                    cargando();
                    http.post('direccion/no-aprobar', { estudiante_id: this.estudiante.id, motivo: this.estudiante.motivo }).then(
                        ( ) =>
                        {
                            this.show_dir = false;
                            this.estudiante = undefined;
                            $('#modalNoAprobarEstudiante').modal('hide');
                            alertTareaRealizada();
                        },
                        ({ response }) =>
                        {
                            if(response.status === 422)
                                this.errors.estudiante = response.data.errors;
                            else
                                alertErrorServidor();
                        }
                    ).then(cerrarCargando);
                }
            )
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
                            <i class="fas fa-edit text-primary show-estudiante" data-id="${data.id}"></i>
                            <i class="fas fa-user-graduate text-primary ml-3 show-info" data-id="${data.id}" sidebar="est"></i>
                            <i class="fas fa-info-circle text-primary ml-3 show-info" data-id="${data.id}" sidebar="dir"></i>
                        `;
                    }
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




$('#tabla-estudiante').on( 'draw.dt', function () {
    $('.show-info').on('click', function(){
        vue.showSidebar($(this).attr('data-id'), $(this).attr('sidebar'));
    });
    $('.show-estudiante').on('click', function(){
        vue.showEstudiante($(this).attr('data-id'));
    });
});
