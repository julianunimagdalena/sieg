@component('component', ['id' => 'selector-info-grado-component'])
<div>
    <div class="d-inline-block">
        <span class="font-weight-bold">PROGRAMA:</span>
    </div>
    <div class="d-inline-block">
        <select v-model="info" v-on:change="handleChange" >
            <option v-for="inf in infos" :value="inf">@{{inf.programa}}</option>
        </select>
    </div>
</div>
@endcomponent

@push('scripts')
<script>
    Vue.component('selector-info-grado', {
        template: '#selector-info-grado-component',
        props: {
            value: Object,
            infos: Array
        },
        data: () => ({
            info: undefined
        }),
        watch: {
            value: function(n, o) {
                this.info = n;
            }
        },
        methods: {
            handleChange() {
                this.$emit('input', this.info);
            }
        }
    })
</script>
@endpush
