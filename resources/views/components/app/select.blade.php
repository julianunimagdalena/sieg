@component('component', ['id' => 'app-select-component'])
<span>
    <label>@{{ label }} </label><small class="text-danger ml-1" v-if="required">*</small>
    <select :id="id" class="form-control" :class="input_class" :placeholder="placeholder" v-model="c_value"
        v-on:change="$emit('input', c_value)">
        <option :value="c_value === null ? null : undefined" selected :disabled="disabled">
            @{{ default_text || "Seleccione una opción"}}</option>
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
            id: {
                type: String,
                default: undefined
            },
            disabled: {
                type: Boolean,
                default: true
            },
            value: String | Number,
            label: String,
            errors: Array,
            default_text: String,
            placeholder: String,
            required: {
                type: Boolean,
                default: false
            },
            input_class: String
        },
        data: () => ({
            c_value: undefined
        }),
        watch: {
            value(n, o) {
                this.c_value = n;
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
