import { baseURL } from './variables.js';

Vue.component('documento-card', {
    template: '#documento-card-component',
    props: {
        documento: Object,
        seleccionar: Function
    },
    computed: {
        color() {
            let color;

            switch (this.documento.estado) {
                case 'SIN CARGAR':
                    color = 'secondary'
                    break;

                case 'APROBADO':
                    color = 'primary'
                    break;

                case 'RECHAZADO':
                    color = 'danger'
                    break;

                case 'PENDIENTE':
                    color = 'warning'
                    break;
            }

            return color;
        },
        progress() {
            let progress;

            switch (this.documento.estado) {
                case 'SIN CARGAR':
                    progress = 0;
                    break;

                case 'APROBADO':
                    progress = 100
                    break;

                case 'RECHAZADO':
                    progress = 100
                    break;

                case 'PENDIENTE':
                    progress = 50
                    break;
            }

            return progress;
        },
        canSee() {
            let can = true;
            if (this.documento.estado === 'SIN CARGAR') can = false;

            return can;
        }
    },
    methods: {
        cargar() {
            this.seleccionar(this.documento);
        },
        verDocumento() {
            const url = baseURL + '/documento/ver/' + this.documento.id + '?time=' + (new Date()).getTime();
            window.open(url, '_blank');
        }
    }
});
