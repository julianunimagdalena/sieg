import axios from "./http";
import { baseURL } from "./variables";

/**
 * @param {string} rol
 * @param {() => void} redirCallback
 */
function redirectByRol(rol, redirCallback) {
    const actualURL = window.location.pathname;
    let url = baseURL;

    if (rol) {
        const router = [
            { name: 'Estudiante', path: 'estudiante' },
            { name: 'Administrador Egresados', path: 'admin' },
            { name: 'Secretaría General', path: 'secgeneral' },
            { name: 'Dependencia', path: 'dependencia' },
            { name: 'Coordinador de programa', path: 'dirprograma' },
        ];

        const route = router.filter(r => r.name === rol)[0];
        url = baseURL + route.path;
    }

    if (actualURL.indexOf(url) === -1) window.location.href = url;
    else redirCallback();
}

/**
 * @param {() => void} redirCallback
 */
async function checkAndRedirect(redirCallback = null) {
    const res = await axios.get("/session-data");
    const { data } = res;
    let rol = null;

    if (data.check) rol = data.data.rol;

    redirectByRol(data.data.rol, redirCallback);
}

export {
    checkAndRedirect
};
