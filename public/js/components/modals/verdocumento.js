import { getDocumentoRoute } from '../../functions.js';

Vue.component('modal-ver-documento', {
    template: '#modal-ver-documento-component',
    data: () => ({
        src: ''
    }),
    props: {
        id: String,
        documento: Object
    },
    watch: {
        documento(d_new)
        {
            this.src = getDocumentoRoute(d_new.id);
            $('#frame-documento').scrollTop(0);
        }
    }
});
