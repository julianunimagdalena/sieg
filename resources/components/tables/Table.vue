<template>
    <table>
        <slot />
    </table>
</template>

<style scoped>
* {
    color: black;
    text-align: center;
}

th,
td {
    text-transform: uppercase;
}

thead.bg-primary th {
    color: white !important;
}
</style>
