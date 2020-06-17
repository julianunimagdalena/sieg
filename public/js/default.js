


const swalHtml = document.createElement("div");

const setLoadingText = (text) =>
{
    swalHtml.innerHTML = `
        <div class="loadingContent2" aria-hidden="true">
            <div class="loader"></div>
            <span>${text}</span>
        </div>
    `;
};

function cargando(text = 'Cargando...') {
    setLoadingText(text);
    swal({
        content: swalHtml,
        button: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
        showLoaderOnConfirm: true
    })
}

function cerrarCargando() {
    swal.close();
}


function alertConfirmar(text = '¿Está seguro de realizar esta acción?') {
    return swal({
        text,
        icon: "warning",
        closeOnClickOutside: false,
        buttons: ["Cancelar", "Continuar"]
    });
}

function alertErrorServidor(text = 'Ha ocurrido un error en el servidor') {
    swal("Error", text, 'error');
}

function alertTareaRealizada(text = 'La tarea ha sido realizada con exito') {
    swal('Info', text, 'success');
}


const meses = [
    'ENERO',
    'FEBRERO',
    'MARZO',
    'ABRIL',
    'MAYO',
    'JUNIO',
    'JULIO',
    'AGOSTO',
    'SEPTIEMBRE',
    'OCTUBRE',
    'NOVIEMBRE',
    'DICIEMBRE'
];


function getNivelIdioma(id, niveles = [])
{
    return niveles.find((element) => id === element.id);
}


function resolveIdiomas(data, idiomas = [], niveles = [])
{
    return {
        idioma: idiomas.find( (element) => { return element.id === data.idioma_id } ).nombre,
        nivel_habla: getNivelIdioma(data.nivel_habla_id, niveles).nombre,
        nivel_escritura: getNivelIdioma(data.nivel_escritura_id, niveles).nombre,
        nivel_lectura: getNivelIdioma(data.nivel_lectura_id, niveles).nombre
    }
}


function openModal(target="")
{
    $(target).modal('show');
}


function getEstados (
        estado = '',
        class_sin_cargar='badge-secondary',
        class_pendiente= 'badge-warning',
        class_aprobado='badge-success',
        class_rechazado = 'badge-danger')
{
    estado = estado.toLowerCase();

    return {
        [class_sin_cargar]: estado === 'sin cargar' ,
        [class_pendiente]: estado === 'pendiente' || estado === 'por aprobar' || estado === 'no generado' || estado === 'generar',
        [class_aprobado]: estado === 'aprobado',
        [class_rechazado]: estado === 'rechazado' || estado === 'no aprobado'
    }
}

function getBadgeClass(estado)
{
   let classes = getEstados(estado);
    for (let key in classes)
    {
        if (classes[key] === true)
            return key;
    }
}

function getUrlData()
{
    return location.href.split('/').pop();
}


function getIconoEstado(estado)
{
    if(estado)
    {
        switch(estado.toLowerCase())
        {
            case "aprobado":
                return'far fa-check-circle text-success';
            case 'pendiente':
                return 'far fa-clock text-warning';
            case 'rechazado':
                return 'far fa-times-circle text-danger';
        }
    }
}

function getForm(obj, form = false)
{
    let data = form ? { form: { ...obj } } : { ...obj };
    return {
        ...data,
        errors: {
            ...obj
        }
    }
}
