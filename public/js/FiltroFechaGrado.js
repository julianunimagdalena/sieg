import http from './http.js';

Vue.component('filtro-fecha-grado', {
    template: "#filtro-fecha-grado-component",
    data: () => ({
        input: {},
        datos: { tipos: [] }
    }),
    async created() {
        const res = await http.get('recursos/tipos-grado');
        this.datos.tipos = res.data;
    }
});
