import http from '../http.js';

new Vue({
    el: '#app',
    data: () => ({
        tipoEncuesta: null,
        encuesta: null,
        modulos: [],
        respuesta_encuesta: {}
    }),
    methods: {
        isOpen(pregunta_id, respuesta) {
            //console.log(this.respuesta_encuesta[pregunta_id]);
            return respuesta.abierta == true && this.respuesta_encuesta[pregunta_id] == respuesta.id;
        },
        onChangeRespuesta(pregunta_id, respuesta) {
            this.$set(this.respuesta_encuesta, pregunta_id, {
                respuesta_id: respuesta.id,
                texto: !respuesta.abierta ? respuesta.valor : '',
                open: respuesta.abierta || undefined
            });
            /* if (respuesta.to_pregunta && !multiple) {
                 console.log("Pasa por aca");
             }*/
            //this.$forceUpdate();
        },
        enviarEncuesta() {
            for (let respuesta of this.respuesta_encuesta) {
                if (respuesta.respuesta_id) {
                    console.log(respuesta.id, respuesta.valor);
                }
            }
        }
    },
    created() {
        this.tipoEncuesta = $('#tipo_encuesta').val();

        cargando('Cargando Datos...');

        http.get(`egresado/datos-encuesta/${this.tipoEncuesta}`).then(
            ({ data }) => {
                const { modulos, ...encuesta } = data;

                console.log(modulos[3]);

                for (let key in modulos) {
                    for (let pregunta of modulos[key].preguntas) {
                        this.$set(this.respuesta_encuesta, pregunta.id, {
                            respuesta_id: undefined,
                            texto: ''
                        });
                    }
                }
                this.encuesta = encuesta;
                this.modulos = modulos;

            }
        ).catch(
            () => alertErrorServidor('Ha ocurrido un error al traer la encuesta')
        ).finally(cerrarCargando);
    }
});
