import http from '../http.js';

Vue.component('datos-academicos', {
    template: '#datos-academicos-component',
    data: () => ({
        datos: {
            meses: meses,
            niveles_estudio: []
        },
        input: {
            graduado: false
        },
        errors: {
        }
    }),
    methods: {
        init()
        {
            http.get('egresado/datos-academicos').then(
                ( { data } ) =>
                {
                    this.datos = {...this.datos, ...data};
                },
                err =>
                {
                    if(err)
                        alertErrorServidor();
                }
            );
        },
        handleSubmitInfoAcademica()
        {
            cargando();
            http.post('egresado/estudio', {...this.input }).then(
                ( ) =>
                {
                    swal('Info', 'Información académica añadida con exito', 'success');
                    this.input = {
                        graduado: "0"
                    };
                    this.init();
                    $('#modalAddInfoAcademica').modal('hide');
                },
                ({response}) =>
                {
                    if(response.status === 422)
                        this.errors = response.data.errors;
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        },
        editInfoAcademica(info)
        {
            this.input = {...info };
            $('#modalAddInfoAcademica').modal('show');
        },
        deleteInfoAcademica(info)
        {
            alertConfirmar().then( ( ok ) =>
            {
                if(!ok)
                    return;

                cargando();
                http.post('egresado/eliminar-estudio', { id: info.id }).then (
                    ( ) =>
                    {
                        alertTareaRealizada();
                        this.init();
                        this.$emit('updateprogreso');
                    },
                    err =>
                    {
                        alertErrorServidor();
                    }
                ).then(cerrarCargando);
            })
        }
    },
    mounted: function()
    {
        http.get('recursos/niveles-estudio').then(
            ({ data }) =>
            {
                this.datos.niveles_estudio = data;
            }
        );

        this.init();
    }
})
