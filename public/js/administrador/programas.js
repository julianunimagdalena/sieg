import http from '../http.js';
import { initBootstrapSelect } from '../functions.js';

new Vue({
    el: '#app',
    data: () => ({
        datos: {
            programas: [],
            paz_salvos: [],
        },
        programa: {

        },
        form: {
            programa: {}
        },
        errors: {
            programa: {}
        }
    }),
    methods: {
        openModal,
        onChangePrograma()
        {
            cargando();
            http.get(`administrador/info-programa/${this.programa.id}`).then(
                ({data})=>
                {
                    this.programa = {id: this.programa.id, ...data};
                }
            ).then(cerrarCargando);
        },
        onChangeCargaEcaes()
        {
            http.post('administrador/carga-ecaes', { programa_id: this.programa.id, value: this.programa.carga_ecaes}).then(
                ({data}) =>
                {

                },
                ({response}) =>
                {
                    if(response.status === 400)
                        alertErrorServidor(response.data);
                    else
                        alertErrorServidor();
                }
            );
        },
        onChangeCarga(type="")
        {
            const re = new RegExp('-', 'g')
            let varName = type.replace(re, '_');
            console.log(varName);
            http.post(`administrador/${type}`, { programa_id: this.programa.id, value: this.programa[varName] }).then(
                ({data}) =>
                {

                },
                ({response}) =>
                {
                    if(response.status === 400)
                        alertErrorServidor(response.data);
                    else
                    {
                        alertErrorServidor();
                        this.programa[varName] = ! this.programa[varName];
                    }
                }
            );
        },
        onclickAddPrograma()
        {
            cargando();
            http.post('administrador/registrar-programa').then(
                ({ data }) =>
                {
                    alertTareaRealizada();
                    $('#modalPazSalvos').modal('hide');
                },
                ({ response }) =>
                {
                    if(response.status === 422)
                        this.errors.programa = response.data.errors;
                    else
                        alertErrorServidor();
                }
            ).then(cerrarCargando);
        },
        onClickAddPazSalvo()
        {

        },
        onclickAddDocumento()
        {

        },
        onDeletePazSalvo(data)
        {
            alertConfirmar().then(
                (ok) =>
                {
                    if(!ok)
                        return;

                    cargando();
                    http.post('administrador/borrar-paz-salvo', data).then(
                        ({ data }) =>
                        {
                            alertTareaRealizada();
                        },
                        ({ response }) =>
                        {
                            if(response.status === 400)
                                alertErrorServidor(response.data);
                            else
                                alertErrorServidor();
                        }
                    ).then(cerrarCargando);
                }
            );
        },
        onDeleteDocumento(documento)
        {
            alertConfirmar().then(
                (ok) =>
                {
                    if(!ok)
                        return;

                    cargando();
                    http.post('administrador/borrar-documento', documento).then(
                        ({ data }) =>
                        {
                            alertTareaRealizada();
                        },
                        ({ response }) =>
                        {
                            if(response.status === 400)
                                alertErrorServidor(response.data);
                            else
                                alertErrorServidor();
                        }
                    ).then(cerrarCargando);
                }
            );
        }
    },
    mounted()
    {
        http.get('recursos/programas').then(
            ({ data }) =>
            {
                this.datos.programas = data;
                initBootstrapSelect();
            }
        );


        http.get('recursos/paz-salvos').then(
            ({ data }) =>
            {
                this.datos.paz_salvos = data;
            }
        );
    }
})
