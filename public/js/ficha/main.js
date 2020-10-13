import http from '../http.js';


new Vue({
    el: '#app',
    data: () => ({
        progress: 0,
        active: {
            datos_basicos: true,
            datos_academicos: false,
            hoja_de_vida: false,
            datos_laborales: false,
            datos_programas: false
        },
        egresado_data: {
            datos: {}
        },
        register: false
    }),
    methods: {
        setActiveTab(tab) {
            sessionStorage.setItem('active', tab);
            for (let key in this.active) {
                this.active[key] = key === tab;
            }
        },
        updateProgreso() {
            if (!this.register) {
                http.get('egresado/progreso-ficha').then(
                    ({ data }) => {
                        this.progress = data;
                    }
                );
            }
        },
        updateRegister(data) {
            this.egresado_data.data = data;
            console.log(data);
        },
        runAlertBusqueda() {
            swal({
                title: "Cédula Egresado",
                buttons: {
                    cancel: false,
                    confirm: "Buscar",
                },
                closeOnClickOutside: false,
                closeOnEsc: false,
                content: {
                    element: "input",
                    attributes: {
                        placeholder: "CC",
                    },
                }
            }).then(
                (text) => {
                    if (!text)
                        return;

                    cargando('Buscando...');
                    http.get(`administrador/consultar-graduado?identificacion=${text}`).then(
                        ({ data }) => {
                            if (data.length !== 0)
                                this.egresado_data = data;
                            else {
                                this.egresado_data = { datos: { identificacion: text } };
                            }

                        },
                        err => {
                            // this.runAlertBusqueda();
                        }
                    ).then(cerrarCargando);
                }
            );
        }
    },
    mounted: function () {
        let tabActive = sessionStorage.getItem('active');

        this.register = Boolean(Number($('#is-register').val()));

        if (!tabActive || this.register) {
            sessionStorage.setItem('active', 'datos_basicos');
        } else
            this.setActiveTab(tabActive);




        if (this.register) {
            this.runAlertBusqueda();
        } else
            this.updateProgreso();

        $(document).on('submit', () => {
            setTimeout(this.updateProgreso, 150);
        });
    },
    beforeUpdate: function () {

    }
})
