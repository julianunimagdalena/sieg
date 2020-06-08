@component('component', ['id' => 'icono-estado-component'])
<i :class="c_class" :title="estado">
</i>
@endcomponent

@push('scripts')
<script type="module">
    Vue.component('icono-estado', {
        template: '#icono-estado-component',
        props: {
            estado: {
                type: String,
            }
        },
        data: () => ({
            c_class: ''
        }),
        methods: {
            initIcon()
            {
                this.c_class = getIconoEstado(this.estado);
            }
        },
        watch:
        {
            estado()
            {
                this.initIcon();
            }
        },
        mounted()
        {
            this.initIcon();
        }
    });
</script>
@endpush
