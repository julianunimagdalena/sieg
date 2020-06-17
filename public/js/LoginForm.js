import http from './http.js';

Vue.component('login-form', {
    template: '#login-form',
    data: () => ({
        cargando: false,
        roles: [],
        input: {},
        errors: {}
    }),
    watch: {
        'input.rol_id': function (n, o) {
            const rol = this.roles.find(r => r.id === n);
            console.log('hola', n, rol);

            if (rol) {
                this.input.estudiante_id = rol.estudiante_id;
            }
        }
    },
    methods: {
        handleSubmit() {
            this.cargando = true;
            let message = "Ha ocurrido un error, favor intente mas tarde";

            http.post("/autenticar", this.input)
                .then(
                    res => {
                        const { data } = res;

                        if (data !== "ok") this.roles = data;
                        else window.location.reload();
                    },
                    err => {
                        if (err.response.status === 422)
                            this.errors = err.response.data.errors;
                        else {
                            if (err.response.status === 400)
                                message = err.response.data;
                            swal("Error", message, "error");
                        }
                    }
                )
                .then(() => (this.cargando = false));
        }
    }
});
