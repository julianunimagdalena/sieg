import http from '../http.js';

new Vue({
    el: '#app',
    data: () => ({
        infos: [],
        info: undefined,
        documento: undefined
    }),
    created() {
        this.fetchData();
    },
    methods: {
        fetchData() {
            let codigo = null;
            if (this.info) codigo = this.info.codigo;

            http.get('egresado/documentos-grado').then(res => {
                this.infos = res.data;

                if (!codigo) this.info = this.infos[0];
                else this.info = this.infos.filter(i => i.codigo === codigo)[0];
            });
        },
        modalDocumento(documento) {
            this.documento = { ...documento };
            $('#cargaDocumentoModal').modal('show');
        }
    }
});
