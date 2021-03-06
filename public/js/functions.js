import { baseURL } from './variables.js';

function objectToFormData(obj, rootName, ignoreList) {
    var formData = new FormData();

    function appendFormData(data, root) {
        if (!ignore(root)) {
            root = root || '';
            if (data instanceof File) {
                formData.append(root, data);
            } else if (Array.isArray(data)) {
                for (var i = 0; i < data.length; i++) {
                    appendFormData(data[i], root + '[' + i + ']');
                }
            } else if (typeof data === 'object' && data) {
                for (var key in data) {
                    if (data.hasOwnProperty(key)) {
                        if (root === '') {
                            appendFormData(data[key], key);
                        } else {
                            appendFormData(data[key], root + '.' + key);
                        }
                    }
                }
            } else {
                if (data !== null && typeof data !== 'undefined') {
                    formData.append(root, data);
                }
            }
        }
    }

    function ignore(root) {
        return Array.isArray(ignoreList)
            && ignoreList.some(function (x) { return x === root; });
    }

    appendFormData(obj, rootName);

    return formData;
}

function initBootstrapSelect(id = null, time = 10) {
    $.fn.selectpicker.Constructor.DEFAULTS.style = 'form-control';
    $.fn.selectpicker.Constructor.DEFAULTS.styleBase = 'form-control';
    $.fn.selectpicker.Constructor.DEFAULTS.noneSelectedText = 'Seleccione';
    $.fn.selectpicker.Constructor.DEFAULTS.liveSearch = true;
    $.fn.selectpicker.Constructor.DEFAULTS.countSelectedText = '{0} elementos seleccionados';
    $.fn.selectpicker.Constructor.DEFAULTS.selectedTextFormat = 'count';
    setTimeout(() => $(id || '.bselect').selectpicker('refresh'), time);
}

class DataTableManager {
    constructor(selector = '.data-table') {
        this.selector = selector;
        this.table = null;
    }

    reload() {
        if (this.table) this.table.destroy();

        return new Promise(resolve => {
            setTimeout(() => {
                this.table = $(this.selector).DataTable({
                    language: {
                        url: baseURL + '/Spanish.json'
                    }
                });

                resolve();
            }, 10);
        });
    }
}
function getDocumentoRoute(documento_id, url = '/documento/ver') {
    return `${baseURL}${url}/${documento_id}?rnd=${Math.floor(Math.random() * 10000)}`;
}

function verDocumento(documento_id, url = '/documento/ver') {
    let link = document.createElement('a');
    link.setAttribute('href', getDocumentoRoute(documento_id, url));
    link.target = '_blank';
    link.click();

    link.remove();
    //window.open(`${baseURL}${url}/${documento_id}`, '__blank');
}

function objectToParameter(obj) {
    let result = '';

    for (let key in obj) {
        result += `${key}=${obj[key]}&`;
    }

    return result;
}


function openModal(target = "") {
    $(target).modal('show');
}


const toBase64 = file => new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = error => reject(error);
});

function fileResponse(response) {
    var blob = new Blob([response.data], { type: response.headers['content-type'] });
    var filename = response.headers['content-disposition'].split('filename=')[1];
    var a = document.createElement('a');

    a.href = URL.createObjectURL(blob);
    a.download = filename;
    a.click();

    return filename;
}


export {
    objectToFormData,
    initBootstrapSelect,
    DataTableManager,
    verDocumento,
    getDocumentoRoute,
    objectToParameter,
    openModal,
    toBase64,
    fileResponse
};
