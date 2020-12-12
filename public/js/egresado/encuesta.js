import http from '../http.js';
import { baseURL } from '../variables.js';



new Vue({
    el: '#app',
    data: () => ({
        tipoEncuesta: null,
        encuesta: null,
        modulos: [],
        diligenciada: false,
        respuesta_encuesta: {},
        preguntas_d: {}// PREGUNTAS DESACTIVADAS
    }),
    methods: {
        getPregunta(pregunta_id) {
        },
        scrollTo(element) {
            $([document.documentElement, document.body]).animate({
                scrollTop: $(element).offset().top
            });
        },
        onChangeMultiple(pregunta_id, respuesta) {
            if (respuesta.to_pregunta) {
                this.$set(this.respuesta_encuesta, respuesta.to_pregunta, {
                    ...this.respuesta_encuesta[respuesta.to_pregunta],
                    obligatoria: this.respuesta_encuesta[pregunta_id].multiple.includes(respuesta.valor)
                });
            }
        },
        onChangeRespuesta(pregunta_id, respuesta) {

            let keys = Object.keys(this.respuesta_encuesta);

            this.respuesta_encuesta[pregunta_id].preguntas_d.forEach(
                (element) => {
                    this.$set(this.preguntas_d, element, false);
                    this.$set(this.respuesta_encuesta, element, {
                        ...this.respuesta_encuesta[element],
                        respuesta_id: undefined,
                        texto: ''
                    })
                }
            );

            this.respuesta_encuesta[pregunta_id].preguntas_d = [];

            if (respuesta.to_pregunta) {
                for (let i = keys.indexOf(String(pregunta_id)) + 1; i < keys.indexOf(String(respuesta.to_pregunta)); i++) {
                    this.respuesta_encuesta[pregunta_id].preguntas_d.push(keys[i]);

                    if (this.respuesta_encuesta[keys[i]].subprs
                        && this.respuesta_encuesta[keys[i]].subprs.length > 0) {
                        this.respuesta_encuesta[keys[i]].subprs.forEach((e) => this.$set(this.preguntas_d, e, true));
                    }
                    this.$set(this.preguntas_d, keys[i], true);
                }

                if (!respuesta.abierta) {
                    setTimeout(() => {
                        $([document.documentElement, document.body]).animate({
                            scrollTop: $(`#pregunta-${respuesta.to_pregunta}`).offset().top
                        });
                    }, 200);
                }
            }
            this.$set(this.respuesta_encuesta, pregunta_id, {
                ...this.respuesta_encuesta[pregunta_id],
                respuesta_id: respuesta.id,
                texto: !respuesta.abierta ? respuesta.valor : '',
                open: respuesta.abierta || undefined,
                to_pregunta: respuesta.to_pregunta || undefined
            });

        },
        enviarEncuesta() {
            const respuesta_encuesta = JSON.parse(JSON.stringify(this.respuesta_encuesta));
            let data = [];
            for (let pregunta_id in respuesta_encuesta) {
                const respuestaPregunta = respuesta_encuesta[pregunta_id];

                const isBlank = respuestaPregunta.texto.replace(/\s/g, "") === '';
                const obligatoria = respuestaPregunta.obligatoria && !this.preguntas_d[pregunta_id];

                if (!respuestaPregunta.multiple) {
                    if (/*!respuestaPregunta.respuesta_id && respuestaPregunta.obligatoria ||*/
                        (respuestaPregunta.respuesta_id && isBlank) ||
                        (!respuestaPregunta.respuesta_id && obligatoria && isBlank)
                    ) {
                        console.log(respuestaPregunta, { isBlank }, `#pregunta-${pregunta_id}`);
                        this.scrollTo(`#pregunta-${pregunta_id}`);
                        data = [];
                        return;
                    }
                    if (respuestaPregunta.to_pregunta) {
                        respuesta_encuesta[respuestaPregunta.to_pregunta].obligatoria = true;
                    }
                } else if (respuestaPregunta.multiple
                    && respuestaPregunta.obligatoria
                    && respuestaPregunta.multiple.length == 0) {
                    this.scrollTo(`#pregunta-${pregunta_id}`);
                    data = [];
                    return;
                }
                data.push({
                    pregunta_id,
                    texto: respuestaPregunta.texto,
                    multiple: respuestaPregunta.multiple
                });
            }
            cargando('Enviando Encuesta...');
            http.post(`egresado/encuesta/${this.tipoEncuesta}`, data).then(
                () => {
                    alertTareaRealizada('Encuesta Enviada');
                    setTimeout(() => {
                        window.location.href = `${baseURL}/egresado/`;
                    }, 2 * 1000);
                },
                (err) => {
                    alertErrorServidor();
                }
            ).finally(cerrarCargando);
        }
    },
    created() {
        this.tipoEncuesta = $('#tipo_encuesta').val();

        cargando('Cargando Datos...');

        http.get(`egresado/datos-encuesta/${this.tipoEncuesta}`).then(
            ({ data }) => {
                let { modulos, diligenciada, ...encuesta } = data;
                this.diligenciada = diligenciada;

                //modulos = modulos.slice(5, 6);

                for (let key in modulos) {
                    for (let pregunta of modulos[key].preguntas) {
                        let aditionals = {};
                        /*const respuesta = {
                            id: !pregunta.abierta ? pregunta.respuestas[0] ? pregunta.respuestas[0].id : undefined : undefined,
                            texto: pregunta.respuestas[0] ? pregunta.respuestas[0].valor : 'test',
                            to_pregunta: pregunta.respuestas[0] ? pregunta.respuestas[0].to_pregunta : undefined,
                        }*/
                        let subprs = []
                        //console.log(pregunta.orden, !pregunta.abierta ? pregunta.respuestas[0].id || undefined : 'test');
                        pregunta.preguntas.forEach(subpr => {
                            subprs.push(subpr.id);
                            this.$set(this.respuesta_encuesta, subpr.id, {
                                orden: pregunta.orden,
                                respuesta_id: undefined,
                                obligatoria: subpr.obligatoria,
                                texto: '',
                                preguntas_d: []
                            });
                        });
                        if (pregunta.multiple) {
                            aditionals.multiple = [];
                        }
                        aditionals.preguntas_d = [];
                        this.$set(this.respuesta_encuesta, pregunta.id, {
                            orden: pregunta.orden,
                            respuesta_id: undefined,
                            texto: '',
                            to_pregunta: undefined,
                            obligatoria: pregunta.obligatoria,
                            subprs, // SUBPREGUNTAS
                            ...aditionals
                        });
                        subprs = [];
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
