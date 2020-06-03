import http from '../http.js';
import { defaultUserAvatar } from '../variables.js';


new Vue({
    el: '#app',
    data: () => ({
        estudiante: {}
    }),
    methods: {
        actualizar()
        {

        }
    },
    mounted()
    {
        const id = getUrlData();

        http.get(`direccion/datos-estudiante/${id}`).then(
            ({ data }) =>
            {
                this.estudiante         = data;
                this.estudiante.foto    = data.foto || defaultUserAvatar;
            }
        );
    }
})
