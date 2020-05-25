@component('component', ['id' => 'input-file-component'])
<div class="input-group">
    <input class="d-none" type="file" :id="id" accept="application/pdf" v-on:change="selectFile">
    <div class="input-group-prepend">
        <button class="btn btn-outline-primary" v-on:click="seleccionar">Seleccionar</button>
    </div>
    <input type="text" class="form-control" :value="value ? value.name : 'Seleccione un archivo'" disabled>
    <div class="input-group-append">
        <button class="btn btn-success" :disabled="buttonDisabled" v-on:click="$emit('submit')">Enviar</button>
    </div>
</div>
@endcomponent

@push('scripts')
<script type="module">
    Vue.component('input-file', {
        template: "#input-file-component",
        props: {
            value: File,
            buttonDisabled: {
                type: Boolean,
                default: false
            }
        },
        data: () => ({
            id: Math.random().toString(36).substring(2)
        }),
        methods: {
            seleccionar() {
                document.getElementById(this.id).click();
            },
            selectFile(event) {
                let file = event.target.files[0];
                this.$emit('input', file);
                this.$emit('change');
            }
        }
    });
</script>
@endpush
