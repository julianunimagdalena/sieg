import http from './http.js';
import { initBootstrapSelect } from './functions.js';

Vue.component('usuario-modal', {
    template: "#usuario-modal-component",
    props: {
        id: String,
        usuario: Object,
        roles: Array,
        programas: Array
    },
    data: () => ({
        input: {},
        errors: {},
        cargando: false,
        dependencias: []
    }),
    computed: {
        title() {
            return (this.usuario ? 'Editar' : 'Nuevo') + ' usuario';
        },
        canElegirProgramas() {
            let can = false;
            const { rol_id } = this.input;

            if (rol_id) {
                let rol = this.roles.filter(r => r.id === rol_id)[0];
                can = rol.canElegirProgramas;
            }

            return can;
        },
        canElegirDependencias() {
            let can = false;
            const { rol_id } = this.input;
            if (rol_id) {
                let rol = this.roles.filter(r => r.id === rol_id)[0];
                can = rol.canElegirDependencias;
            }
            return can;
        },
        rolesDisponibles() {
            return this.roles.filter(r => r.canElegir);
        },
        programasSeleccionados() {
            const programas = this.programas.filter(p => this.input.programa_ids.indexOf(p.id) !== -1);
            return programas;
        },
        dependenciasSeleccionadas() {
            return this.dependencias.filter(d => this.input.dependencia_ids.indexOf(d.id) !== -1);
        }
    },
    watch: {
        usuario(val, oldVal) {
            this.fetchData();
        },
        canElegirProgramas(val, oldVal) {
            initBootstrapSelect();
        },
        canElegirDependencias(val, oldVal) {
            initBootstrapSelect();
        }
    },
    methods: {
        fetchData() {
            this.input = { activo: true, programa_ids: [], dependencia_ids: [] };
            this.errors = {};

            if (this.usuario) {
                http.get('administrador/datos-usuario/?ur_id=' + this.usuario.id).then(res => {
                    const input = res.data;
                    if (input.activo === null) input.activo = true;

                    this.input = input;
                });
            }
        },
        fetchByIdentificacion() {
            http.get('administrador/datos-usuario/?identificacion=' + this.input.identificacion).then(res => {
                const input = {};
                Object.entries(res.data).forEach(v => {
                    if (v[1]) input[v[0]] = v[1];
                });

                if (!input.activo) input.activo = true;

                this.input = input;
            });
        },
        submit() {
            this.cargando = true;
            http.post('administrador/usuario', this.input).then(
                res => {
                    swal('Éxito', 'Operación realizada con éxito', 'success');
                    $('#' + this.id).modal('hide');
                    this.$emit('complete');
                },
                err => {
                    if (err.response.status === 422) this.errors = err.response.data.errors;
                    else {
                        let text = 'Error del servidor, notifique e intente más tarde';

                        if (err.response.status === 400) text = err.response.data;
                        swal('Error', text, 'error');
                    }
                }
            ).then(() => this.cargando = false);
        }
    },
    mounted() {
        http.get('recursos/dependencias').then(
            ({ data }) => {
                this.dependencias = data;
            }
        )
    }
});
