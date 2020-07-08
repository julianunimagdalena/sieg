import http from '../../http.js';
import {  defaultUserAvatar } from '../../variables.js';

Vue.component('modal-informacion-estudiante', {
    template: '#modal-informacion-estudiante-component',
    props: {
        estudiante_id: String
    },
    data: () => ({
        datos: {
            estudiante: {}
        }
    }),
    methods: {
        initEstudianteInfo(show_cargar = true)
        {
            if(show_cargar)cargando();
            http.get(`direccion/datos-estudiante/${this.estudiante_id}`).then(
                ({ data }) =>
                {
                    this.datos.estudiante           = data;
                    this.datos.estudiante.id        = this.estudiante_id;
                    this.datos.estudiante.foto      = data.foto || defaultUserAvatar;
                    $('#modalInformacionEstudiante').modal('show');
                }
            ).then(cerrarCargando);
        },
        actualizarEstudiante()
        {
            cargando();
            http.get(`direccion/actualizar-estudiante/${this.datos.estudiante.id}`).then(
                ({ data }) =>
                {
                    alertTareaRealizada();
                    this.initEstudianteInfo(false);
                    //$('#modalInformacionEstudiante').modal('hide');
                },
                err =>
                {
                    alertErrorServidor();
                }
            ).then(cerrarCargando);
        }
    },
    watch: {
        estudiante_id(new_v)
        {
            if(new_v !== undefined)
                this.initEstudianteInfo();
        }
    }
});
