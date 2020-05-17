@component('component', ['id' => 'solicitud-grado-modal'])
<div>
    <modal :id="id" title="Solicitar estudio de hoja de vida academica" :onSubmit="!consultado ? consultar : solicitar"
        :buttonDisabled="cargando || !input.identificacion || (consultado && programas.length === 0)"
        :buttonText="consultado ? 'Solicitar' : 'Continuar'">
        Ingrese su numero de identificación para continuar:
        <br />
        <br />
        <div class="form-group">
            <label>Numero de identificación</label>
            <input class="form-control" placeholder="Numero de identificación" type="text"
                v-model="input.identificacion" @change="errors.identificacion = undefined" />
            <small class="text-danger" v-if="errors.identificacion">@{{errors.identificacion[0]}}</small>
        </div>
        <div v-if="consultado">
            <hr />
            <div v-if="programas.length === 0">No se encuentra ningun programa para este documento.</div>
            <div v-else>
                <div class="form-group">
                    <label>Seleccione el programa al cual aspira graduarse:</label>
                    <div class="radio" v-for="prg in programas" :key="prg">
                        <label>
                            <input type="radio" :value="prg" v-model="input.programa"
                                @change="errors.programa=undefined" />
                            @{{prg}}
                        </label>
                    </div>
                    <small class="text-danger" v-if="errors.programa">@{{errors.programa[0]}}</small>
                </div>
                <div class="form-group">
                    <label>Seleccione la fecha de grado a la que aspira graduarse:</label>
                    <select class="form-control" v-model="input.fecha_id" @change="errors.fecha_id = undefined">
                        <option :value="undefined" hidden selected>Seleccione una opción</option>
                        <option v-for="itm in datos.fechas" :key="itm.id" :value="itm.id">@{{itm.nombre}}</option>
                    </select>
                    <small class="text-danger" v-if="errors.fecha_id">@{{errors.fecha_id[0]}}</small>
                </div>
            </div>
        </div>
    </modal>
</div>
@endcomponent

@push('components')
@include('components.modals.Modal')
@endpush

@push('scripts')
<script type="module" src="{{asset('js/SolicitudGradoModal.js')}}"></script>
@endpush
