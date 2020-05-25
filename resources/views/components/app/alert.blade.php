@component('component', ['id' => 'alert-component'])
<div :class="'alert alert-' + color" role="alert">
    <slot />
</div>
@endcomponent

@push('scripts')
<script type="module">
    Vue.component('alert', {
        template: '#alert-component',
        props: {
            color: {
                type: String,
                required: true
            }
        }
    });
</script>
@endpush
