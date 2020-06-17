import http from '../http.js';
import { DataTableManager } from '../functions.js';

const manager = new DataTableManager();

new Vue({
    el: '#app',
    data: () => ({
        fechas: [],
        searched: false,
        table: undefined,
        selectedFechaId: null,
        lastFilter: null
    }),
    methods: {
        async buscar(filter, useLastFilter = false) {
            let query = '';

            if (useLastFilter) filter = this.lastFilter

            for (const key in filter) {
                if (filter[key] !== undefined && filter[key] !== '') {
                    query += `${query ? '&' : '?'}${key}=${filter[key]}`
                }
            }

            this.lastFilter = filter;

            cargando();
            const res = await http.get('recursos/fechas-grado' + query);
            cerrarCargando();

            this.fechas = res.data;
            this.searched = true;
            manager.reload();
        },
        fechaGradoModal(fecha = null) {
            this.selectedFechaId = fecha ? fecha.id : null;
            $('#fechaGradoModal').modal('show');
        },
        eliminarFecha(fecha)
        {
            alertConfirmar('¿Está seguro de eliminar la fecha de grado?').then(
                (ok) =>
                {
                    if(!ok)
                        return;

                    cargando();
                    http.post('administrador/eliminar-fecha-grado', { fecha_id: fecha.id } ).then(
                        () =>
                        {
                            this.buscar({}, true);
                            alertTareaRealizada();
                        },
                        err =>
                        {
                            alertErrorServidor();
                        }
                    ).then(cerrarCargando);
                }
            );
        }
    },
});
