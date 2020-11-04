import { toBase64 } from '../functions.js';
import http from '../http.js';


Vue.component('user-avatar', {
    template: '#user-avatar-component',
    props: {
        imgstyle: Object,
        actions: {
            type: Boolean,
            default: true
        }
    },
    data: () => ({
        foto: null,
        valid: false,
        aprobar: true,
        foto_aprobada: null
    }),
    methods: {
        onChangeFoto(files) {
            this.valid = false;
            toBase64(files[0]).then(
                (data) => {

                    cargando('Validando...');
                    const foto = data.split(',')[1];

                    http.post('egresado/validar-foto', { foto }).then(
                        (response) => {
                            if (response.data.success) {
                                this.foto = data;
                                this.valid = true;
                                this.aprobar = false;
                            } else {
                                swal('Información', 'Su fotografía no cumple con el formato solicitado', 'info');
                            }
                        }
                    ).catch(
                        (err) => {
                            this.foto = null;
                            alertErrorServidor('Ha ocurrido un error al validar la foto')
                        }
                    ).finally(cerrarCargando);

                }
            ).catch(() => alertErrorServidor('Error al cargar'));
        },
        onclickAprobar() {
            cargando('Subiendo...');
            if (this.valid) {
                const data = { aprobar: this.aprobar };

                if (!this.aprobar) {
                    data.foto = this.foto.split(',')[1];
                }

                http.post('egresado/cargar-foto', data).then(
                    ({ data }) => {
                        if (data.success) {
                            alertTareaRealizada();
                            this.initFoto();
                        }
                    }
                ).finally(cerrarCargando);
            } else {
                alertErrorServidor('La foto aún no ha sido validada');
            }
        },
        onClickUpload() {
            $('#foto-input').click();
        },
        initFoto() {
            http.get('egresado/foto').then(
                ({ data }) => {
                    this.foto_aprobada = data.foto_aprobada;
                    if (data.foto) {
                        this.foto = 'data:image/*;base64,' + data.foto;
                        this.valid = true;
                    }
                }
            );
        }
    },

    mounted: function () {
        this.initFoto();
    }
});
