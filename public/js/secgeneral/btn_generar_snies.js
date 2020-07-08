import { objectToParameter } from '../functions.js';

Vue.component('btn-generar-snies', {
    template: '#btn-generar-snies-component',
    props: {
        filter: Object
    },
    methods: {
        objectToParameter
    }
})
