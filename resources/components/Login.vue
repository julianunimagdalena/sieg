<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <h2>Información</h2>
                    <p>
                        Bienvenido al Sistema de Administración de Egresados y Graduados
                        <strong>SAEG</strong>
                        <br />Si eres estudiante, puedes iniciar tu solicitud para el estudio
                        de tu hoja de vida academica a través del siguiente enlace:
                    </p>
                    <a
                        href="#solicitudGradoModal"
                        data-toggle="modal"
                        class="btn btn-primary"
                    >Solicitar estudio de hoja de vida academica</a>
                </div>
                <div class="col-xs-12 col-md-6">
                    <h2>Acceso</h2>
                    <div class="acceso">
                        <login-form />
                    </div>
                </div>
            </div>
        </div>
        <solicitud-grado-modal id="solicitudGradoModal" />
    </div>
</template>

<script>
import LoginForm from "./forms/LoginForm.vue";
import SolicitudGradoModal from "./modals/SolicitudGradoModal.vue";

import axios from "../tools/http";

export default {
    components: { LoginForm, SolicitudGradoModal },
    methods: {
        sayHi() {
            console.log("hola");
        }
    }
};
</script>

<style>
.bg-primary {
    background-color: #004a87 !important;
}

.acceso {
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 15px;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
