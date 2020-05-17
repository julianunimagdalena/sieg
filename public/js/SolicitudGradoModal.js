import http from './http.js';

Vue.component('solicitud-grado-modal', {
    template: '#solicitud-grado-modal',
    props: {
        id: String
    },
    data: () => ({
        cargando: false,
        consultado: false,
        programas: [],
        input: {},
        errors: {},
        datos: {}
    }),
    methods: {
        reset() {
            this.cargando = false;
            this.consultado = false;
            this.programas = [];
            this.input = {};
            this.errors = {};
            this.datos = {};
        },
        async consultar() {
            this.cargando = true;
            const res = await http.get(
                "/programas-por-identificacion/" + this.input.identificacion
            );

            this.cargando = false;
            this.consultado = true;
            this.programas = res.data;
        },
        solicitar() {
            this.cargando = true;
            http
                .post("/solicitar-grado", this.input)
                .then(
                    res => {
                        const text =
                            "Se ha solicitado con éxito su estudio de hoja de vida academica, " +
                            "se le notificará por correo electrónico los pasos siguientes en caso de " +
                            "que su solicitud haya sido aprobada.";

                        $("#" + this.id).modal("hide");
                        swal("Éxito", text, "success");
                    },
                    err => (this.errors = err.response.data.errors)
                )
                .then(() => (this.cargando = false));
        }
    },
    async created() {
        const { data } = await http.get("fechas-grado/activas");
        this.datos.fechas = data;
    },
    mounted() {
        $("#" + this.id).on("hide.bs.modal", () => this.reset());
    }
});
