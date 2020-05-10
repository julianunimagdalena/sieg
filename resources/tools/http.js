import axios from "axios";

import { baseURL } from './variables';

const instance = axios.create({
    baseURL: baseURL
});

export default instance;
