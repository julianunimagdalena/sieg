
function findById(id, data)
{
    return data.find((element) => element.id === id);
}


export function resolveExperienciaLaboral(data,
    niveles_cargo = [],
    duraciones = [],
    tipos_vinculacion = [],
    rangos_salariales = [])
{
    return {
        nivel_cargo: findById(data.nivel_cargo_id, niveles_cargo).nombre,
        duracion: findById(data.duracion_id, duraciones).nombre,
        tipo_vinculacion: findById(data.tipo_vinculacion_id, tipos_vinculacion).nombre,
        rango_salarial: findById(data.salario_id, rangos_salariales).rango
    }
}
