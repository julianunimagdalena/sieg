@push('csscomponent')
<style>
    i.upload:hover {
        color: #ccc !important;
        cursor: pointer;
    }
</style>
@endpush

@component('component', ['id' => 'documento-card-component'])
<card :color="color">
    <div class="row no-gutters align-items-center">
        <div class="col mr-2">
            <div class="text-xs font-weight-bold text-uppercase mb-1">@{{documento.nombre}}</div>
            <div class="row no-gutters align-items-center">
                <div class="col">
                    <div class="progress progress-sm mr-2">
                        <div :class="['progress-bar', 'bg-' + color]" role="progressbar"
                            :style="{width: progress + '%'}" :aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-auto">
            <i class="upload fas fa-eye fa-lg text-gray-300 mr-2" v-if="canSee" v-on:click="verDocumento"
                title="Ver documento"></i>
            <i class="upload fas fa-upload fa-lg text-gray-300" v-on:click="cargar" title="Cargar documento"></i>
        </div>
    </div>
</card>
@endcomponent

@push('scripts')
<script type="module" src="{{asset('js/DocumentoCard.js')}}"></script>
@endpush
