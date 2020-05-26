import http from './http.js';

Vue.component('usuario-modal', {
    template: "#usuario-modal-component",
    props: {
        id: String,
        usuario: Object
    },
    data: () => ({
        input: {},
        errors: {}
    }),
    computed: {
        title() {
            return (this.usuario ? 'Editar' : 'Nuevo') + ' usuario';
        }
    },
    watch: {
        usuario(val, oldVal) {
            this.fetchData();
        }
    },
    methods: {
        fetchData() {
            this.input = {};

            if (this.usuario) {
                http.get('administrador/datos-usuario/' + this.usuario.id).then(res => this.input = res.data);
            }
        }
    }
});
