import http from '../../http.js';
import { defaultUserAvatar } from '../../variables.js';


Vue.component('sidebar-proceso-grado', {
    template: '#sidebar-proceso-grado-component',
    props: {
        estudiante_data: Object,
        show: Boolean
    },
    data: () => ({
        estudiante: undefined
    }),
    methods: {
        initData() {
            http.get(`direccion/proceso-grado/${this.estudiante_data.id}`).then(
                ({ data }) => {
                    this.estudiante = data;
                    this.estudiante.info.foto = this.estudiante_data.foto;
                }
            );
        },
        updatePazSalvos() {
            cargando();
            http.post('direccion/actualizar-paz-salvos', { estudiante_id: this.estudiante_data.id }).then(
                ({ data }) => {
                    if (data.success) {
                        alertTareaRealizada();
                        this.initData();
                    }
                    else {
                        let elements = '';

                        data.errors.forEach((element) => {
                            elements += `<li class="list-group-item font-weight-bold">${element}</li>`;
                        });

                        swalHtml.innerHTML = `
                        <div class="app-text-black-1">
                            <div class="p-2">
                                <small class="font-weight-bold">
                                    Paz y salvos actualizados, los siguientes presentaron errores:
                                </small>
                            </div>
                            <div class="mt-3">
                                <ul class="list-group list-group-flush">
                                    ${elements}
                                </ul>
                            </div>
                        </div>
                        `;

                        swal({
                            title: "Información",
                            content: swalHtml,
                            icon: "warning"
                        });
                    }
                },
                error => {
                    alertErrorServidor();
                }
            ).then(cerrarCargando);
        }
    },
    watch: {
        show(new_v, old_v) {
            if (new_v) {
                this.initData();
            }
        }
    }
});
