import http from '../../http.js';
import {  defaultUserAvatar } from '../../variables.js';


Vue.component('sidebar-proceso-grado', {
    template: '#sidebar-proceso-grado-component',
    props: {
        estudiante_data: Object,
        show: Boolean
    },
    data: () => ({
        estudiante: undefined
    }),
    methods: {
        initData()
        {
            http.get(`direccion/proceso-grado/${this.estudiante_data.id}`).then(
                ({ data }) =>
                {
                    this.estudiante = data;
                    this.estudiante.info.foto = data.info.foto || defaultUserAvatar;
                }
            );
        }
    },
    watch: {
        show(new_v, old_v)
        {
            if(new_v)
            {
                this.initData();
            }
        }
    }
});
