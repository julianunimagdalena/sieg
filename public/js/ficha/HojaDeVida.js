import http from '../http.js';

Vue.component('hoja-de-vida', {
    template: '#hoja-de-vida-component',
    data: () => ({
        datos: {
            idiomas: [],
            niveles: [],
            user_idiomas: [],
            distinciones: [],
            asociaciones: [],
            consejos: [],
            discapacidades: [],
            t_consejos: [],
            t_discapacidades: []
        },
        forms: {
            idioma: {},
            distincion: {},
            asociacion: {},
            consejo: {},
            discapacidad: {},
            perfil: {}
        },
        errors: {
            idioma: {},
            distincion: {},
            asociacion: {},
            consejo: {},
            discapacidad: {},
            perfil: {}
        }
    }),
    methods: {
        initUserIdiomas()
        {
            http.get('egresado/idiomas').then(
                ({ data }) =>
                {
                    data = data.map( (data) => ({...data, resolve: resolveIdiomas(data, this.datos.idiomas, this.datos.niveles)}))
                    this.datos.user_idiomas = data;
                }
            );
        },
        initDistinciones()
        {
            http.get('egresado/distinciones').then(
                ({ data }) =>
                {
                    this.datos.distinciones = data;
                }
            );
        },
        initAsociaciones()
        {
            http.get('egresado/asociaciones').then(
                ({ data }) =>
                {
                    this.datos.asociaciones = data;
                }
            );
        },
        initConsejos()
        {
            http.get('egresado/concejos').then(
                ({ data }) =>
                {
                    this.datos.consejos = data;
                }
            )
        },
        initDiscapacidades()
        {
            http.get('egresado/discapacidades').then(
                ({ data }) =>
                {
                    this.datos.discapacidades = data;
                }
            );
        },
        editItem(varName,data)
        {
            this.forms[varName] = { ...data };
            $('#modal-'+varName).modal('show');
        },
        deleteItem(api, data, callBack)
        {
            alertConfirmar().then(
                (ok) =>
                {
                    if(!ok)
                        return;

                    cargando('Eliminando...')
                    http.post(`egresado/${api}`, data).then(
                        ( ) =>
                        {
                            alertTareaRealizada('Se ha eliminado la información');
                            callBack();
                            this.$emit('updateprogreso');
                        },
                        err =>
                        {
                            alertErrorServidor();
                        }
                    ).then(cerrarCargando);
                }
            )
        },
        onSubmitPerfil()
        {
            cargando('Guardando...')
            http.post('egresado/perfil-profesional', this.forms.perfil).then(
                ( ) =>
                {
                    alertTareaRealizada('Guardado');
                },
                ({ response }) =>
                {
                    if(response.status === 422)
                        this.errors.perfil = response.data.errors;
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        },
        onSubmitLanguaje() {
            cargando('Enviando...')
            http.post('egresado/idioma', this.forms.idioma).then(
                ( ) =>
                {
                    alertTareaRealizada('Se ha añadido satisfactoriamente el idioma');

                    this.initUserIdiomas();

                    $('#modal-idioma').modal('hide');
                },
                ({ response }) =>
                {
                    if(response.status === 422)
                        this.errors.idioma = response.data.errors;
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        },
        onSubmitDistincion()
        {
            cargando('Enviando Distinción...');
            http.post('egresado/distincion', this.forms.distincion).then(
                ( ) =>
                {
                    alertTareaRealizada('Se ha añadido con exito la distinción');

                    $('#modal-distincion').modal('hide');

                    this.initDistinciones();
                },
                ({ response }) =>
                {
                    if(response.status === 422)
                        this.errors.distincion = response.data.errors;
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        },

        onSubmitAsociacion()
        {
            cargando('Enviando Asociación...');
            http.post('egresado/asociacion', this.forms.asociacion).then(
                ( ) =>
                {
                    alertTareaRealizada('Se ha añadido con exito la asociación');

                    $('#modal-asociacion').modal('hide');

                    this.initAsociaciones();
                },
                ({ response }) =>
                {
                    if(response.status === 422)
                        this.errors.asociacion = response.data.errors;
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        },

        onSubmitConsejo()
        {
            cargando('Enviando Consejo...');
            http.post('egresado/concejo', this.forms.consejo).then(
                ( ) =>
                {
                    alertTareaRealizada('Se ha añadido con exito el consejo profesional');

                    $('#modal-consejo').modal('hide');

                    this.initConsejos();
                },
                ({ response }) =>
                {
                    if(response.status === 422)
                        this.errors.distincion = response.data.errors;
                    else if(response.status === 400)
                        swal('Error!', response.data.errors, 'error');
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        },
        onSubmitDiscapacidad()
        {
            cargando();
            http.post('egresado/agregar-discapacidad', this.forms.discapacidad).then(
                ( ) =>
                {
                    alertTareaRealizada();

                    $('#modal-discapacidad').modal('hide');

                    this.initDiscapacidades();
                },
                ({ response }) =>
                {
                    if(response.status === 422)
                        this.errors.distincion = response.data.errors;
                    else if(response.status === 400)
                        swal('Error!', response.data.errors, 'error');
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        },
        openModal(modalName) {
            $('#' + modalName).modal('show');
        }
    },
    mounted: function () {

        Promise.all([
            http.get('recursos/idiomas'),
            http.get('recursos/niveles-idioma'),
            http.get('recursos/discapacidades'),
            http.get('recursos/consejos'),
            http.get('egresado/perfil')
        ]).then(
            ( response ) =>
            {
                this.datos.idiomas              = response[0].data;
                this.datos.niveles              = response[1].data;
                this.datos.t_discapacidades     = response[2].data;
                this.datos.t_consejos           = response[3].data;

                this.forms.perfil.perfil        = response[4].data;

                this.initUserIdiomas();
            }
        );

        this.initDistinciones();
        this.initAsociaciones();
        this.initConsejos();
        this.initDiscapacidades();
    }
})
