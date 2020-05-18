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


function alertConfirmar() {
    return swal({
        text: '¿Está seguro de realizar esta acción?',
        icon: "warning",
        closeOnClickOutside: false,
        buttons: ["Cancelar", "Continuar"]
    });
}

function alertErrorServidor() {
    swal("Error", 'Ha ocurrido un error en el servidor', 'error');
}

function alertTareaRealizada() {
    swal('Info', 'La tarea ha sido realizada con exito', 'success');
}
