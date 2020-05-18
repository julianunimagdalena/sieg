@component('component', ['id' => 'app-input-component'])
<span>
    <label>@{{ label }} </label><small class="text-danger ml-1" v-if="required">*</small>
    <input
    :type="type"
    class="form-control"
    :placeholder="placeholder"
    v-on:input="onChange($event)"
    v-bind:class="{'is-invalid': errors}"
    :value="value">
    <small class="text-danger" v-if="errors">@{{errors[0]}}</small>
</span>
@endcomponent

@push('scripts')
    <script>
        Vue.component('app-input', {
            template: '#app-input-component',
            props: {
                value: String,
                label: String,
                errors: Array,
                placeholder: String,
                type: {
                    type: String,
                    default: 'text'
                },
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
