@component('component', ['id' => 'carga-documento-modal-component'])
<modal :id="id" title="Cargar documento">
    <div v-if="documento">
        <p class="font-weight-bold">
            Documento a cargar: <span class="text-primary">@{{documento.nombre}}</span>
        </p>
        <input-file v-model="input.file" v-on:change="errors.file=undefined" :buttonDisabled="cargando"
            v-on:submit="enviar"></input-file>
    </div>
</modal>
@endcomponent

@push('scripts')
<script type="module" src="{{asset('js/CargaDocumentoModal.js')}}"></script>
@endpush
