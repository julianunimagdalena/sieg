import http from '../http.js';

new Vue({
    el: '#app',
    data: () => ({
        infos: [],
        info: undefined
    }),
    created() {
        http.get('egresado/info-grado').then(res => {
            this.infos = res.data;
            this.info = this.infos[0];
        });
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
            return this.getEstado(this.info.estado_programa);
        },
        estadoSecretaria() {
            return this.getEstado(this.info.estado_secretaria);
        },
        confirmacionCeremonia() {
            return this.getEstado(this.info.confirmacion_ceremonia !== null);
        }
    },
    methods: {
        getEstado(value) {
            return value ? 'APROBADO' : 'PENDIENTE';
        },
        elegirPrograma(event) {
            console.log(event)
        }
    }
});
