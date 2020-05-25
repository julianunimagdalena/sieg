import { getPaises, getMunicipios, getDepartamentos } from '../location.js';
import http from '../http.js';
import { resolveExperienciaLaboral } from '../resolvers.js';

Vue.component('datos-laborales', {
    template: '#datos-laborales-component',
    data: () => ({
        datos: {
            paises: [],
            departamentos: [],
            municipios: [],
            niveles_cargo: [],
            duraciones: [],
            tipos_vinculacion: [],
            rangos_salariales: []
        },
        forms: {
            a_laboral: {},
            xp: {}
        },
        errors: {
            xp: {}
        }
    }),
    methods: {
        init()
        {
            http.get('egresado/datos-laborales').then(
                ({ data }) =>
                {
                    const experiencias = data.experiencias.map( (element) =>
                    {
                        return {...element,
                            resolve: resolveExperienciaLaboral(element, this.datos.niveles_cargo,
                                this.datos.duraciones, this.datos.tipos_vinculacion, this.datos.rangos_salariales)}
                    })

                    this.datos.experiencias         = experiencias;
                    this.forms.a_laboral            = Number(data.actualidad_laboral);
                    console.log(this.forms.a_laboral);
                }
            )
        },
        onChangeActualidadLaboral(laborando)
        {
            http.post('egresado/actualidad-laboral', { laborando }).then(
                ( ) =>
                {

                }
            );
        },
        onChangePais()
        {
            this.errors.xp.pais = undefined;
            getDepartamentos(this.forms.xp.pais_id).then( (data) => this.datos.departamentos = data );
        },
        onChangeDepartamento()
        {
            this.errors.xp.departamento = undefined;
            getMunicipios(this.forms.xp.departamento_id).then( (data) => this.datos.municipios = data );
        },
        onEditDatoLaboral(experiencia)
        {
            this.forms.xp = { ...experiencia };
            $('#modalInformacionLaboral').modal('show');
            this.onChangePais();
            this.onChangeDepartamento();
        },
        onDeleteDatoLaboral(experiencia)
        {
            alertConfirmar('¿Está seguro(a) de eliminar esta experiencia laboral?').then(
                (ok) =>
                {
                    if(!ok)
                        return;

                    cargando('Eliminando...');
                    http('egresado/eliminar-experiencia-laboral', {id: experiencia.id}).then(
                        ( )=>
                        {
                           alertTareaRealizada('Eliminado con exito');
                           this.init();
                        },
                        err =>
                        {
                            alertErrorServidor();
                        }
                    ).then(cerrarCargando);
                }
            )
        },
        onSubmitDatosLaborales()
        {
            cargando('Enviando experiencia laboral');
            http.post('egresado/experiencia-laboral', this.forms.xp).then(
                ( ) =>
                {
                    $('#modalInformacionLaboral').modal('hide');
                    alertTareaRealizada('Se ha añadido con exito la experiencia.');
                    this.init();
                },
                ({ response }) =>
                {
                    if(response.status === 422)
                        this.errors.xp = response.data.errors;
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        },
        openModal
    },
    mounted: function() {

        getPaises().then( data => this.datos.paises = data);


        Promise.all([
            http.get('recursos/niveles-cargo'),
            http.get('recursos/duraciones-laborales'),
            http.get('recursos/tipos-vinculacion'),
            http.get('recursos/salarios'),
            http.get('egresado/datos-laborales')
        ]).then(
            (response) =>
            {
                this.datos.niveles_cargo        = response[0].data;
                this.datos.duraciones           = response[1].data;
                this.datos.tipos_vinculacion    = response[2].data;
                this.datos.rangos_salariales    = response[3].data;

                this.init();

            }
        );
    }
})
