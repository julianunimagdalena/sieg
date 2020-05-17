@component('component', ['id' => 'HojaDeVida-component'])
<div>
    Hola desde datos Hoja de Vida
</div>
@endcomponent


@push('scripts')
<script type="module" src="{{ asset('js/ficha/HojaDeVida.js') }}"></script>
@endpush
