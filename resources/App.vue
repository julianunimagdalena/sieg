<template>
    <router-view v-if="!cargando" />
</template>

<script>
import "@fortawesome/fontawesome-free/js/all.js";
import { checkAndRedirect } from "./tools/functions";

export default {
    data: () => ({
        cargando: true
    }),
    created() {
        checkAndRedirect(() => (this.cargando = false));
    }
};
</script>

<style lang="scss">
@import "~@fortawesome/fontawesome-free/scss/fontawesome.scss";
</style>
