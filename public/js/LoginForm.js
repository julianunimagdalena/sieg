import http from './http.js';

Vue.component('login-form', {
    template: '#login-form',
    data: () => ({
        cargando: false,
        roles: [],
        input: {},
        errors: {}
    }),
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
