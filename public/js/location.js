import http from './http.js'

export function getPaises()
{
    return http.get('recursos/paises').then(
        ( {data} ) =>
        {
            return data;
        },
        error => []
    );
}

export function getDepartamentos(pais_id)
{
    return http.get(`recursos/departamentos?pais=${pais_id}`).then(
        ( { data } ) =>
        {
            return data;
        },
        err => []
    );
}

export function getMunicipios(departamento_id)
{
    return http.get(`recursos/municipios?departamento=${departamento_id}`).then(
        ( {data} ) =>
        {
            return data
        },
        error => []
    );
}
