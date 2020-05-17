@component('component', ['id' => 'datos-basicos-component'])
<div>
    Hola desde datos Basicos
</div>
@endcomponent

@push('scripts')
<script type="module" src="{{ asset('js/ficha/DatosBasicos.js') }}"></script>
@endpush
