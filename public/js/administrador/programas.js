import http from '../http.js';
import { initBootstrapSelect } from '../functions.js';

new Vue({
    el: '#app',
    data: () => ({
        datos: {
            programas: [],
            paz_salvos: [],
            facultades: [],
            modalidades: [],
            niveles_estudio: [],
            jornadas: [],
            documentos: []
        },
        programa: {

        },
        form: {
            programa: {},
            paz: {},
            documento: {}
        },
        errors: {
            programa: {},
            paz: {},
            documento: {}
        }
    }),
    methods: {
        openModal,
        initProgramas() {
            http.get('recursos/programas').then(
                ({ data }) => {
                    this.datos.programas = data;
                    initBootstrapSelect();
                }
            );
        },
        onChangePrograma(carga = true) {
            if (carga) cargando();
            http.get(`administrador/info-programa/${this.programa.id}`).then(
                ({ data }) => {
                    this.programa = { id: this.programa.id, ...data };
                }
            ).then(carga ? cerrarCargando : () => { });
        },
        onChangeCargaEcaes() {
            http.post('administrador/carga-ecaes', { programa_id: this.programa.id, value: this.programa.carga_ecaes }).then(
                ({ data }) => {

                },
                ({ response }) => {
                    if (response.status === 400)
                        alertErrorServidor(response.data);
                    else
                        alertErrorServidor();
                }
            );
        },
        onChangeCarga(type = "") {
            const re = new RegExp('-', 'g')
            let varName = type.replace(re, '_');

            http.post(`administrador/${type}`, { programa_id: this.programa.id, value: this.programa[varName] }).then(
                ({ data }) => {
                    this.onChangePrograma(false);
                },
                ({ response }) => {
                    if (response.status === 400)
                        alertErrorServidor(response.data);
                    else {
                        alertErrorServidor();
                        this.programa[varName] = !this.programa[varName];
                    }
                }
            );
        },
        onclickAddPrograma() {
            cargando();
            http.post('administrador/registrar-programa', this.form.programa).then(
                ({ data }) => {
                    alertTareaRealizada();
                    $('#modalAddPrograma').modal('hide');
                    this.initProgramas();
                },
                ({ response }) => {
                    if (response.status === 422)
                        this.errors.programa = response.data.errors;
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        },
        onClickAddPazSalvo() {
            cargando();
            http.post('administrador/paz-salvo', getNonNull({ ...this.form.paz, programa_id: this.programa.id })).then(
                ({ data }) => {
                    alertTareaRealizada();
                    $('#modalPazSalvos').modal('hide');
                    this.onChangePrograma(false);
                },
                ({ response }) => {
                    if (response.status === 422)
                        this.errors.paz = response.data.errors;
                    else if (response.status === 400)
                        alertErrorServidor(response.data);
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        },
        onclickAddDocumento() {
            cargando();
            http.post('administrador/documento', getNonNull({ ...this.form.documento, programa_id: this.programa.id })).then(
                ({ data }) => {
                    alertTareaRealizada();
                    $('#modalDocumentos').modal('hide');
                    this.onChangePrograma(false);
                },
                ({ response }) => {
                    if (response.status === 422)
                        this.errors.documento = response.data.errors;
                    else if (response.status === 400)
                        alertErrorServidor(response.data);
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        },
        onDeletePazSalvo(data) {
            alertConfirmar().then(
                (ok) => {
                    if (!ok)
                        return;

                    cargando();
                    http.post('administrador/borrar-paz-salvo', data).then(
                        ({ data }) => {
                            alertTareaRealizada();
                            this.onChangePrograma(false);
                        },
                        ({ response }) => {
                            if (response.status === 400)
                                alertErrorServidor(response.data);
                            else
                                alertErrorServidor();
                        }
                    ).then(cerrarCargando);
                }
            );
        },
        onDeleteDocumento(documento) {
            alertConfirmar().then(
                (ok) => {
                    if (!ok)
                        return;

                    cargando();
                    http.post('administrador/borrar-documento', documento).then(
                        ({ data }) => {
                            alertTareaRealizada();
                            this.onChangePrograma(false);
                        },
                        ({ response }) => {
                            if (response.status === 400)
                                alertErrorServidor(response.data);
                            else
                                alertErrorServidor();
                        }
                    ).then(cerrarCargando);
                }
            );
        }
    },
    mounted() {

        this.initProgramas();

        http.get('recursos/paz-salvos').then(
            ({ data }) => {
                this.datos.paz_salvos = data;
            }
        );

        http.get('recursos/facultades').then(({ data }) => this.datos.facultades = data);

        http.get('recursos/modalidades-estudio').then(({ data }) => this.datos.modalidades = data);

        http.get('recursos/niveles-estudio').then(({ data }) => this.datos.niveles_estudio = data);

        http.get('recursos/jornadas').then(({ data }) => this.datos.jornadas = data);


        http.get('recursos/documentos').then(({ data }) => this.datos.documentos = data);
    }
})
