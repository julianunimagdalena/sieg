@component('component', ['id' => 'required-component'])
<span>
    <i class="fa fa-asterisk text-danger" aria-hidden="true" v-if="icon"></i>
    <span class="text-danger" v-else>*</span>
    <slot />
</span>
@endcomponent

@push('scripts')
<script type="module">
    Vue.component('app-required', {
        template: '#required-component',
        props: {
            icon: {
                type: Boolean,
                default: false
            }
        }
    });
</script>
@endpush
