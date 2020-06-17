import http from '../../http.js';
import { getDocumentoRoute } from '../../functions.js';

Vue.component('sidebar-documentos', {
    template: '#sidebar-documentos-component',
    data: () => ({
        form: {
            estudiante: {
                extra: {}
            },
            documento:{
            }
        },
        errors: {
            estudiante: {
                extra: {}
            },
            documento:{
            }
        },
        datos: {
            documentos: [],
            can_aprobar: undefined,
        }
    }),
    watch: {
        estudiante_data(new_v, old_v)
        {
            if(!old_v || new_v.id !== old_v.id){
                this.datos.documentos = [];
                this.form.estudiante.extra = {};
                this.initInfoExtraEstudiante();
                this.initDocumentos();
            }
        }
    },
    props: {
        estudiante_data: {
            type: Object,
            default: undefined
        },
        show: Boolean
    },
    methods: {
        getDocumentoRoute,
        verDocumento(documento)
        {
            this.form.documento = documento;
            $('#modalVerDocumento').modal('show');
        },
        onClickRechazarDocumento(documento)
        {
            this.form.documento = documento;
            $('#modalRechazarDocumento').modal('show');
            $('#modalVerDocumento').modal('hide');
        },
        initDocumentos()
        {

            if(this.estudiante_data)
                http.get(`direccion/documentos-estudiante/${this.estudiante_data.id}`).then(
                    ({ data }) =>
                    {
                        this.datos.documentos = data.documentos;
                        this.datos.can_aprobar = data.can_aprobar;
                    }
                );
        },
        initInfoExtraEstudiante()
        {
            if(this.estudiante_data)
                http.get(`direccion/info-adicional-estudiante/${this.estudiante_data.id}`).then(
                    ({ data }) =>
                    {
                        this.form.estudiante.extra = data;

                    }
                );
        },
        generar(documento)
        {
            cargando();
            http.get(`direccion/generar/${documento.id}`).then(
                ( ) =>
                {
                   alertTareaRealizada();
                   this.initDocumentos();
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
                    this.initDocumentos();
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
        aprobarEstudiante()
        {
            alertConfirmar().then(
                (ok) =>
                {
                    if(!ok)
                        return;


                        cargando();

                        http.post('direccion/aprobar', {estudiante_id: this.estudiante_data.id}).then(
                            () =>
                            {
                                /*this.show_dir = false;
                                this.estudiante = undefined;*/
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
        rechazarEstudiante()
        {
            alertConfirmar().then(
                (ok) =>
                {
                    if(!ok)
                        return;

                    cargando();
                    http.post('direccion/no-aprobar', {
                        estudiante_id       : this.estudiante_data.id,
                        motivo              : this.form.estudiante.motivo
                    }).then(
                        ( ) =>
                        {
                            /*this.show_dir = false;
                            this.estudiante = undefined;
                            $('#modalNoAprobarEstudiante').modal('hide');
                            alertTareaRealizada();*/
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
            );
        },
        onSubmitInfoExtra()
        {
            cargando('Enviando...');
            http.post('direccion/info-adicional-estudiante', {...this.form.estudiante.extra, estudiante_id: this.estudiante_data.id}).then(
                () =>
                {
                    alertTareaRealizada('Información guardada con exito');
                },
                ({ response }) =>
                {
                    if(response.status === 422)
                    this.errors.estudiante.extra = response.data.errors;
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        },
        onDocumentoCargado()
        {
            this.initDocumentos();
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
        }
    }
});
