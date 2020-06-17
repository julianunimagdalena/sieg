@component('component', ['id' => 'icons-information-component'])
<card-action :border="false" class="app-text-black-1 font-weight-bold">
    <div>
        <span>
            <span>
                Posibles Estados:
            </span>
            <span class="text-warning ml-2">
                <icono-estado estado="pendiente"></icono-estado> Pendiente
            </span>
            <span class="ml-2 mr-2">
                -
            </span>
            <span class="text-success">
                <icono-estado estado="aprobado"></icono-estado> Aprobado
            </span>
            <span class="ml-2 mr-2">
                -
            </span>
            <span class="text-danger">
                <icono-estado estado="rechazado"></icono-estado> Rechazado
            </span>
        </span>
        <span class="ml-2" v-if="$slots.default">
            <slot></slot>
        </span>
    </div>
</card-action>
@endcomponent

@push('scripts')
<script type="module">
    Vue.component('icons-information', {
        template: '#icons-information-component',
        props: {
            actions: Boolean
        }
    });
</script>
@endpush
