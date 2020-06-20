@component('component', ['id' => 'modal-ver-documento-component'])
<modal :id="id || 'modalVerDocumento'" :title="documento.nombre" large @onhide="$emit('onhide')">
    <div class="pr-2 pl-2 mb-2" v-if="documento.can_aprobar || documento.can_rechazar">
        <span class="font-weight-bold">Acciones: </span>

        <span class="text-success action-btn " v-if="documento.can_aprobar" @click="$emit('aprobar')">
            <i class="fas fa-check" title="Aprobar Documento"></i>
        </span>

        <span class="text-danger action-btn ml-3" v-if="documento.can_rechazar" @click="$emit('rechazar')">
            <i class="fas fa-times" title="Rechazar Documento"></i>
        </span>
    </div>
    <div class="mt-1 mb-3" v-if="documento.motivo_rechazo">
        <span class="font-weight-bold">Motivo de Rechazo: </span> @{{ documento.motivo_rechazo }}
    </div>
    <div>
        <iframe id="frame-documento" :src="src" width="100%" height="500" :name="documento.nombre" frameborder="0">
        </iframe>
    </div>
</modal>
@endcomponent

@push('scripts')
<script type="module" src="{{ asset('js/components/modals/verdocumento.js') }}">
</script>
@endpush
