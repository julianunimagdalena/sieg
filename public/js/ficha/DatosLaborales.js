import { getPaises, getMunicipios, getDepartamentos } from '../location.js';
import http from '../http.js';
import { initBootstrapSelect } from '../functions.js';
import { resolveExperienciaLaboral } from '../resolvers.js';

Vue.component('datos-laborales', {
    template: '#datos-laborales-component',
    data: () => ({
        datos: {
            paises: [],
            experiencias: [],
            departamentos: [],
            municipios: [],
            niveles_cargo: [],
            duraciones: [],
            tipos_vinculacion: [],
            rangos_salariales: [],
            sectores_empresa: [],
            sectores_economicos: [],
            actividades_economicas: [],
            areas_des: []
        },
        forms: {
            a_laboral: {},
            xp: {
                //contrato_activo: false
            }
        },
        errors: {
            xp: {}
        }
    }),
    methods: {
        initBootstrapSelect,
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
                }
            )
        },
        initModalInformacionLaboral()
        {
            this.openModal('#modalInformacionLaboral');
            this.initBootstrapSelect();
        },
        onChangeActualidadLaboral(laborando)
        {
            http.post('egresado/actualidad-laboral', { laborando }).then(
                ( ) =>
                {
                    this.$emit('updateprogreso');
                }
            );
        },
        onChangePais(pais_id = null)
        {
            this.errors.xp.pais = undefined;
            return getDepartamentos(pais_id || this.forms.xp.pais_id).then( (data) => this.datos.departamentos = data );
        },
        onChangeDepartamento(departamento_id = null)
        {
            this.errors.xp.departamento = undefined;
            return getMunicipios(departamento_id || this.forms.xp.departamento_id).then( (data) => this.datos.municipios = data );
        },
        onEditDatoLaboral(experiencia)
        {
            cargando();
            Promise.all([
                this.onChangePais(experiencia.pais_id),
                this.onChangeDepartamento(experiencia.departamento_id)
            ]).then(
                ( ) =>
                {
                    this.forms.xp = { ...experiencia };
                    this.initModalInformacionLaboral();
                    cerrarCargando();
                }
            );

        },
        onDeleteDatoLaboral(experiencia)
        {
            alertConfirmar('¿Está seguro(a) de eliminar esta experiencia laboral?').then(
                (ok) =>
                {
                    if(!ok)
                        return;

                    cargando('Eliminando...');
                    http.post('egresado/eliminar-experiencia-laboral', {id: experiencia.id}).then(
                        ( )=>
                        {
                            this.init();
                            alertTareaRealizada('Eliminado con exito');
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
        onSubmitDatosLaborales()
        {
            cargando('Enviando experiencia laboral');
            http.post('egresado/experiencia-laboral', {...this.forms.xp, contrato_activo: Boolean(this.forms.xp.contrato_activo === 'true')}).then(
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


        Promise.all([
            http.get('recursos/sectores-empresa'),
            http.get('recursos/sectores-economicos'),
            http.get('recursos/actividades-economicas'),
            http.get('recursos/areas-desempeno')
        ]).then(
            (response) =>
            {
                this.datos.sectores_empresa             = response[0].data;
                this.datos.sectores_economicos          = response[1].data;
                this.datos.actividades_economicas       = response[2].data;
                this.datos.areas_des                    = response[3].data;
            }
        );
    }
});
