@component('component', ['id' => 'datos-academicos-component'])
<div>
    Hola desde datos Academicos
</div>
@endcomponent


@push('scripts')
<script type="module" src="{{ asset('js/ficha/DatosAcademicos.js') }}"></script>
@endpush
