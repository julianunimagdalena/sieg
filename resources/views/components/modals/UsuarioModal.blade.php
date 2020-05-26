@component('component', ['id' => 'usuario-modal-component'])
<modal :id="id" :title="title">

</modal>
@endcomponent

@push('scripts')
<script type="module" src="{{asset('js/UsuarioModal.js')}}"></script>
@endpush
