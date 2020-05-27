import http from '../http.js';

new Vue({
    el: '#app',
    data: () => ({
        datos: { roles: [], programas: [] },
        usuarios: [],
        usuario: undefined
    }),
    async created() {
        this.fetchUsuarios();

        const rolesRes = await http.get('recursos/roles');
        const programasRes = await http.get('recursos/programas');

        this.datos.roles = rolesRes.data;
        this.datos.programas = programasRes.data;
    },
    methods: {
        async fetchUsuarios() {
            const res = await http.get('administrador/usuarios');
            this.usuarios = res.data;
        },
        usuarioModal(usuario = null) {
            this.usuario = usuario ? { ...usuario } : null;
            $('#usuarioModal').modal('show');
        },
        eliminar(usuario) {

        }
    }
});
