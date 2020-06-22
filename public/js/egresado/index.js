import http from '../http.js';
import { objectToFormData } from '../functions.js';

new Vue({
    el: '#app',
    data: () => ({
        datos: {
            documentos: []
        },
        forms: {
            documento: {}
        },
        errors: {
            documento: {}
        },
        show_documento: {},
        infos: [],
        info: {}
    }),
    created() {
        this.fetchData();
        this.initDocumentos();
    },
    computed: {
        programas() {
            return this.infos.map(inf => inf.programa);
        },
        estadoEncuesta() {
            return this.getEstado(this.info.estado_encuesta);
        },
        estadoFicha() {
            return this.getEstado(this.info.estado_ficha);
        },
        estadoPrograma() {
            return this.info.estado_programa;
        },
        estadoSecretaria() {
            return this.info.estado_secretaria;
        },
        confirmacionCeremonia() {
            return this.getEstado(this.info.confirmacion_ceremonia !== null);
        }
    },
    methods: {
        getClassEstado,
        initDocumentos() {
            http.get('egresado/documentos-grado').then(
                ({ data }) => {
                    this.datos.documentos = data[0].documentos;
                }
            );
        },
        fetchData() {
            let codigo = null;
            if (this.info) codigo = this.info.codigo;

            http.get('egresado/info-grado').then(res => {
                this.infos = res.data;

                if (!codigo) this.info = this.infos[0];
                else this.info = this.infos.filter(i => i.codigo === codigo)[0];
            });
        },
        getEstado(value) {
            return value ? 'APROBADO' : 'PENDIENTE';
        },
        elegirPrograma(event) {
            console.log(event)
        },
        toggleModals(init = true) {
            if (init) this.initDocumentos();
            this.forms.documento = {};
            $('#modalFormularioEcaes').modal('hide');
            $('#cargaDocumentoModal').modal('hide');
            $('#modalListaDocumentos').modal('show');
            /* if(this.forms.documento.is_ecaes)

                else
            */
        },
        onClickUploadDocumento(documento) {
            this.forms.documento = documento;
            $('#modalListaDocumentos').modal('hide');
            if (documento.is_ecaes)
                $('#modalFormularioEcaes').modal('show');
            else
                $('#cargaDocumentoModal').modal('show');
        },
        onClickVerDocumento(documento) {
            this.show_documento = documento;
            $('#modalListaDocumentos').modal('hide');
            $('#modalVerDocumento').modal('show');
        },
        onSubmitFormEcaes() {
            cargando();

            let data = {
                id: this.forms.documento.id,
                codigo_ecaes: this.forms.documento.codigo_ecaes,
                file: this.forms.documento.file,
            };

            http.post('documento/cargar', objectToFormData(data)).then(
                () => {
                    alertTareaRealizada();
                    this.toggleModals();
                },
                ({ response }) => {
                    if (response.status === 422)
                        this.errors.documento = response.data.errors;
                    else
                        alertErrorServidor();
                }
            );
        }
    }
});
