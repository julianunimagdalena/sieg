import http from '../http.js';

Vue.component('fecha-grado-modal', {
    template: "#fecha-grado-modal-component",
    props: {
        id: String,
        fechaId: Number
    },
    data: () => ({
        datos: {
            tipos_grado: []
        },
        ...getForm({
            form: {}
        })
    }),
    computed: {
        title() {
            return `${this.fechaId ? 'Editar' : 'Nueva'} fecha de grado`
        }
    },
    watch: {
        fechaId()
        {
            this.form = { id: this.fechaId };

            if(this.form.id)
            {
                http.get(`administrador/fecha-grado/${this.form.id}`).then(
                    ({ data }) =>
                    {
                        this.form = {...data, estado: Boolean(Number(data.estado)) };
                    }
                );
            }
        }
    },
    methods: {
        onSubmit()
        {
            cargando();
            http.post('administrador/fecha-grado', this.form).then(
                () =>
                {
                    $('#'+this.id).modal('hide');
                    this.$emit('refresh');
                    alertTareaRealizada();
                },
                ({ response }) =>
                {
                    if(response.status === 422)
                        this.errors.form = response.data.errors;
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        }
    },
    async mounted()
    {
        let response = await http.get('recursos/tipos-grado');

        this.datos.tipos_grado = response.data;
    }
});
