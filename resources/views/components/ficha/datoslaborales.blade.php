@component('component', ['id' => 'datos-laborales-component'])
<div>
    Hola desde datos laborales
</div>
@endcomponent


@push('scripts')
<script stype="module" src="{{ asset('js/ficha/DatosLaborales.js') }}"></script>
@endpush
