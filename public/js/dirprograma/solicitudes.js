import http from '../http.js';


const actualizarNumeroSolicitudes = () =>
{
    http.get('solicitud-grado/numero-solicitudes').then(
        ({ data }) =>
        {
            $('#numero-de-solicitudes').text(data);
        }
    )
}


new Vue({
    el: '#app',
    data: () => ({
        solicitudes: [],
        form: {
            solicitud: {}
        },
        errors: {
            solicitud: {}
        }
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
                    actualizarNumeroSolicitudes();
                }
            });
        },
        rechazarSolicitud()
        {

            alertConfirmar().then(
                (ok) =>
                {
                    if(!ok)
                        return;

                    cargando();
                    http.post('solicitud-grado/eliminar', this.form.solicitud).then(
                        ( ) =>
                        {
                            alertTareaRealizada();
                            $('#modalRechazarSolicitud').modal('hide');
                            this.form.solicitud = {};
                            this.fetchSolicitudes();
                            actualizarNumeroSolicitudes();
                        },
                        ({ response }) =>
                        {
                            if(response.status === 422)
                                this.errors.solicitud = response.data.errors;
                            else
                                alertErrorServidor();
                        }
                    ).then(cerrarCargando);
                }
            );
        }
    }
});
