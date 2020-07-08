@component('component', ['id' => 'btn-generar-snies-component'])
<a :href="'{{ Request::root() }}/secgeneral/generar-snies?'+ objectToParameter(filter)">
    <button class="btn btn-sm btn-warning btn-icon-split ml-2">
        <span class="icon text-white-50">
            <i class="far fa-file-excel"></i>
        </span>
        <span class="text">Descarga SNIES</span>
    </button>
</a>
@endcomponent

@push('scripts')
<script type="module" src="{{ asset('js/secgeneral/btn_generar_snies.js') }}"></script>
@endpush
