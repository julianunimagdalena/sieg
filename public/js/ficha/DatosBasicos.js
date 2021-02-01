import http from '../http.js';



Vue.component('datos-basicos', {
    template: '#datos-basicos-component',
    data: () => ({
        datos: {
            paises: [],
            departamentos: [],
            departamentos_residencia: [],
            municipios: [],
            municipios_residencia: []
        },
        input: {},
        errors: {
        }
    }),
    props: {
        admin: {
            type: Boolean,
            default: false
        },
        register: {
            type: Boolean,
            default: false
        },
        updateregister: Function,
        c_data: Object
    },
    watch: {
        c_data(new_v) {
            this.input = new_v.datos;
            if (new_v.id) {
                this.input.id = new_v.id;
                this.onChangeFechaNacimiento();

                this.onChangePaisNacimiento();
                this.onChangeDepartamentoNacimiento();

                this.onChangePaisResidencia();
                this.onChangeDepartamentoResidencia();
            }
        }
    },
    methods: {
        onChangeFechaNacimiento() {
            this.input.edad = moment().diff(this.input.fecha_nacimiento, 'years', false);
        },
        handleSubmit() {
            cargando('Enviando datos');
            http.post('egresado/datos', this.input).then(
                ({ data }) => {
                    swal('Info', 'Se han guardado satisfactoriamente los datos', 'success');

                    if (this.register && this.updateregister) {
                        this.input = { ...this.input, id: data.id };
                        this.updateregister(this.input);
                    }
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
        },
        onChangePaisNacimiento() {
            this.getDepartamentos(this.input.pais_nacimiento_id).then((data) => this.datos.departamentos = data);
        },
        onChangeDepartamentoNacimiento() {
            this.getMunicipios(this.input.departamento_nacimiento_id).then((data) => this.datos.municipios = data);
        },
        onChangePaisResidencia() {
            this.getDepartamentos(this.input.pais_residencia_id).then((data) => this.datos.departamentos_residencia = data);
        },
        onChangeDepartamentoResidencia() {
            this.getMunicipios(this.input.departamento_residencia_id).then((data) => this.datos.municipios_residencia = data);
        }
    },
    mounted() {

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


        if (!this.register) {
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

                    this.onChangeFechaNacimiento();

                    this.datos = { ...this.datos, ...datos };
                }
            );
        }
    }
});
