import http from '../http.js';

Vue.component('datos-academicos', {
    template: '#datos-academicos-component',
    data: () => ({
        datos: {
            meses: meses
        },
        input: {
            graduado: "0"
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
            )
        },
        handleSubmitInfoAcademica()
        {
            cargando();
            http.post('egresado/estudio', {...this.input, graduado: Boolean(Number(this.input.graduado))}).then(
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
            this.input = {...info, graduado: Number(info.graduado)};
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
        this.init();
    }
})
