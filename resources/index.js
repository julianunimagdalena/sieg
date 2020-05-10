import Vue from "vue";
import VueRouter from "vue-router";

import { baseURL } from './tools/variables';
import axios from './tools/http';

import App from "./App.vue";

__webpack_public_path__ = baseURL;

async function loadModules() {
    const modules = {
        Login: null,
        Home: null,
        Admin: null,
        SolicitudesGrado: null
    };
    const res = await axios.get('/session-data');

    if (res.data.check) {
        modules.Home = (await import('./components/layouts/Home.vue')).default;
        modules.Admin = (await import('./components/Admin.vue')).default;
        modules.SolicitudesGrado = (await import('./components/SolicitudesGrado.vue')).default;
    }
    else modules.Login = (await import('./components/Login.vue')).default;

    return modules;
}

loadModules().then(modules => {
    Vue.use(VueRouter);

    const base = baseURL;
    const router = new VueRouter({
        mode: 'history',
        base: base,
        routes: [
            {
                path: '/',
                component: App,
                children: [
                    { path: '', component: modules.Login },
                    {
                        path: 'admin',
                        component: modules.Home,
                        props: {
                            path: '/admin',
                            links: [
                                { name: 'Opcion 1', icon: 'check', path: '' },
                                { name: 'Opcion 2', icon: 'times', path: 'op2' },
                                { name: 'Opcion 3', icon: 'user', path: 'op3' },
                            ]
                        },
                        children: [
                            { path: '', component: modules.Admin },
                            { path: 'op2', component: { template: '<div>Hola opcion2</div>' } },
                            { path: 'op3', component: { template: '<div>Hola opcion3</div>' } },
                        ]
                    },
                    {
                        path: 'dirprograma',
                        component: modules.Home,
                        props: {
                            path: '/dirprograma',
                            links: [
                                { name: 'Solicitudes de grado', icon: 'file-alt', path: 'solicitudes' }
                            ]
                        },
                        children: [
                            { path: '', redirect: 'solicitudes' },
                            { path: 'solicitudes', component: modules.SolicitudesGrado },
                        ]
                    }
                ],
            },
            {
                path: '*',
                component: { template: '<div>NOT FOUND</div>' }
            }
        ]
    });

    new Vue({
        router
    }).$mount('#root');
});
