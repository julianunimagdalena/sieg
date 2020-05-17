import { baseURL } from './variables.js';

const instance = axios.create({
    baseURL: baseURL
});

export default instance;
