import http from '../http.js';
import { DataTableManager } from '../functions.js';

const manager = new DataTableManager();

new Vue({
    el: '#app',
    data: () => ({
        fechas: [],
        searched: false,
        table: undefined
    }),
    methods: {
        async buscar(filter) {
            let query = '';

            for (const key in filter) {
                if (filter[key] !== undefined && filter[key] !== '') {
                    query += `${query ? '&' : '?'}${key}=${filter[key]}`
                }
            }

            swal('Cargando...');
            const res = await http.get('recursos/fechas-grado' + query);
            swal.close();

            this.fechas = res.data;
            this.searched = true;
            manager.reload();
        }
    },
});
