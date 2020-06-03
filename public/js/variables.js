/** @type {HTMLMetaElement} */
const meta = document.querySelector('meta[name="root"]');
const baseURL = meta.content;

const defaultUserAvatar = baseURL + '/img/sin_perfil.png';

export { baseURL, defaultUserAvatar };
