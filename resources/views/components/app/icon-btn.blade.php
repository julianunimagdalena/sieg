@component('component', ['id' => 'icon-button-component'])
<button class="btn btn-sm btn-icon-split" :class="['btn-'+ color,  {'btn-sm': sm}, c_class]" @click="$emit('click')">
    <span class="icon text-white-50">
        <slot />
    </span>
    <span class="text">@{{ text }}</span>
</button>
@endcomponent

@push('scripts')
<script type="module">
    Vue.component('icon-button', {
        template: '#icon-button-component',
        props: {
            color: {
                type: String,
                required: true
            },
            c_class: {
                type: String,
                default: ''
            },
            text: String,
            sm: {
                type: String,
                default: false,
                required: false
            }
        }
    });
</script>
@endpush
