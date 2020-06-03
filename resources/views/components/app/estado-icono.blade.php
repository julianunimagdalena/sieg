@component('component', ['id' => 'estado-icono-component'])
<i :class="c_class">
</i>
@endcomponent

@push('scripts')
<script type="module">
    Vue.component('estado-icono', {
        template: '#estado-icono-component',
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
                if(this.estado)
                {
                    switch(this.estado.toLowerCase())
                    {
                        case "aprobado":
                            this.c_class = 'far fa-check-circle text-success';
                        break;
                        case 'pendiente':
                            this.c_class = 'far fa-clock text-warning';
                        break;
                        case 'rechazado':
                            this.c_class = 'far fa-times-circle text-danger';
                        break;
                    }
                }
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
