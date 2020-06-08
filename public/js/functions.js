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

function initBootstrapSelect(time = 10) {
    $.fn.selectpicker.Constructor.DEFAULTS.style = 'form-control';
    $.fn.selectpicker.Constructor.DEFAULTS.styleBase = 'form-control';
    $.fn.selectpicker.Constructor.DEFAULTS.noneSelectedText = 'Seleccione';
    $.fn.selectpicker.Constructor.DEFAULTS.liveSearch = true;
    $.fn.selectpicker.Constructor.DEFAULTS.countSelectedText = '{0} elementos seleccionados';
    $.fn.selectpicker.Constructor.DEFAULTS.selectedTextFormat = 'count';
    setTimeout(() => $('.bselect').selectpicker('refresh'), time);
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

function verDocumento(documento_id, url = '/documento/ver')
{
    let link = document.createElement('a');
    link.setAttribute('href', `${baseURL}${url}/${documento_id}?rnd=${Math.floor(Math.random() * 10000)}`);
    link.target = '_blank';
    link.click();

    link.remove();
//window.open(`${baseURL}${url}/${documento_id}`, '__blank');
}

export { objectToFormData, initBootstrapSelect, DataTableManager, verDocumento };
