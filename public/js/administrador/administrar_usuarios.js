import http from '../http.js';

new Vue({
    el: '#app',
    data: () => ({
        usuarios: [],
        usuario: undefined
    }),
    async created() {
        const res = await http.get('administrador/usuarios');
        this.usuarios = res.data;
    },
    methods: {
        usuarioModal(usuario = null) {
            this.usuario = usuario ? { ...usuario } : null;
            $('#usuarioModal').modal('show');
        },
        editar() {

        },
        eliminar(usuario) {

        }
    }
});
