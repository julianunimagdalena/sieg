import http from '../http.js';

new Vue({
    el: '#app',
    data: () => ({
        solicitudes: []
    }),
    created() {
        this.fetchSolicitudes();
    },
    methods: {
        async fetchSolicitudes() {
            const { data } = await http.get("solicitud-grado/pendientes");
            this.solicitudes = data;
        },
        activarEstudiante(solicitud) {
            swal(
                "¡Atención!",
                "¿Desea activar a " + solicitud.nombre_estudiante + "?",
                "warning",
                //@ts-ignore
                {
                    buttons: {
                        cancel: "Cancelar",
                        activar: {
                            text: "Activar",
                            closeModal: false
                        }
                    },
                    closeOnClickOutside: false,
                    closeOnEsc: false
                }
            ).then(async value => {
                if (value) {
                    const data = { solicitud_id: solicitud.id };
                    await http.post("dirprograma/activar-estudiante", data);

                    swal("Exito", "Estudiante aprobado con éxito", "success");
                    this.fetchSolicitudes();
                }
            });
        }
    }
});
