@component('component', ['id' => 'titulo-component'])
<h3 class="text-uppercase text-primary">
    <slot />
</h3>
@endcomponent

@push('scripts')
<script type="module">
    Vue.component('titulo', {
        template: '#titulo-component'
    });
</script>
@endpush
