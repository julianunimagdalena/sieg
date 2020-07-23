import http from '../http.js';



Vue.component('datos-basicos', {
    template: '#datos-basicos-component',
    data: () => ({
        datos: {},
        input: {},
        errors: {
        }
    }),
    props: {
        admin: {
            type: Boolean,
            default: false
        }
    },
    methods: {
        handleSubmit() {
            cargando('Enviando datos');
            http.post('egresado/datos', this.input).then(
                () => {
                    swal('Info', 'Se han guardado satisfactoriamente los datos', 'success');
                },
                ({ response }) => {
                    if (response.status === 422)
                        this.errors = response.data.errors;
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        },
        getDepartamentos(pais_id) {
            return http.get(`recursos/departamentos?pais=${pais_id}`).then(
                ({ data }) => {
                    return data;
                },
                err => []
            );
        },
        getMunicipios(departamento_id) {
            return http.get(`recursos/municipios?departamento=${departamento_id}`).then(
                ({ data }) => {
                    return data
                },
                error => []
            );
        }
    },
    mounted: function () {
        http.get('recursos/generos').then(
            ({ data }) => {
                this.datos.sexos = data;
            }
        );

        http.get('recursos/tipos-documento').then(
            ({ data }) => {
                this.datos.tipos_documento = data;
            }
        );

        http.get('recursos/estados-civiles').then(
            ({ data }) => {
                this.datos.estados_civiles = data;
            }
        );

        http.get('recursos/paises').then(
            ({ data }) => {
                this.datos.paises = data;
            },
        );


        http.get('egresado/datos').then(
            async ({ data }) => {
                let departamentos = await this.getDepartamentos(data.pais_nacimiento_id);
                let municipios = await this.getMunicipios(data.departamento_nacimiento_id);
                let departamentos_residencia = data.pais_nacimiento_id === data.pais_residencia_id ? departamentos : await this.getDepartamentos(data.pais_residencia_id);
                let municipios_residencia = data.departamento_nacimiento === data.departamento_residencia ? municipios : await this.getMunicipios(data.departamento_residencia);
                return {
                    data,
                    departamentos,
                    municipios,
                    departamentos_residencia,
                    municipios_residencia
                }
            },
            error => {

            }
        ).then(
            ({ data, ...datos }) => {
                this.input = data;

                this.input.edad = moment().diff(data.fecha_nacimiento, 'years', false);

                this.datos = { ...this.datos, ...datos };


            }
        );
    }
});
