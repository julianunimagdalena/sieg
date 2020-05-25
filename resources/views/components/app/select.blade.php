@component('component', ['id' => 'app-select-component'])
<span>
    <label>@{{ label }} </label><small class="text-danger ml-1" v-if="required">*</small>
    <select
        class="form-control"
        :placeholder="placeholder"
        v-on:input="onChange($event)"
        :value="value" >
        <option :value="undefined" selected hidden>@{{ default_text || "Seleccione una opción"}}</option>
        <slot />
    </select>
    <small class="text-danger" v-if="errors">@{{errors[0]}}</small>
</span>
@endcomponent

@push('scripts')
    <script>
        Vue.component('app-select', {
            template: '#app-select-component',
            props: {
                value: String | Number,
                label: String,
                errors: Array,
                default_text: String,
                placeholder: String,
                required: {
                    type: Boolean,
                    default: false
                }
            },
            methods: {
                onChange(e)
                {
                    this.$emit('input', e.target.value);
                }
            }
        })
    </script>
@endpush
