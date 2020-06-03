@component('component', ['id' => 'badge-component'])
<span class="badge" v-bind:class="( c_type ||'badge-'+type)">
    <slot />
</span>
@endcomponent

@push('scripts')
<script type="module">
    Vue.component('badge', {
        template: '#badge-component',
        data: () => ({
            c_type: undefined
        }),
        props: {
            type: {
                type: String,
                default: 'secondary'
            },
            estado: String
        },
        created()
        {
            if(this.estado)
                this.c_type = getBadgeClass(this.estado);
        }
    });
</script>
@endpush
