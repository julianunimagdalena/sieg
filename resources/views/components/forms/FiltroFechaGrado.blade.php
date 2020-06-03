@component('component', ['id' => 'filtro-fecha-grado-component'])
<form class="row" @submit.prevent="$emit('buscar', input)">
    <div class="form-group col-md-3">
        <label>Activa</label>
        <select class="form-control" v-model="input.activa">
            <option :value="undefined">TODAS</option>
            <option :value="1">SI</option>
            <option :value="0">NO</option>
        </select>
    </div>
    <div class="form-group col-md-3">
        <label>Tipo de grado</label>
        <select class="form-control" v-model="input.tipo_grado_id">
            <option :value="undefined">TODAS</option>
            <option v-for="tipo in datos.tipos" :value="tipo.id">@{{tipo.nombre}}</option>
        </select>
    </div>
    <div class="form-group col-md-3">
        <label>Fecha minima</label>
        <input type="date" class="form-control" placeholder="Fecha" v-model="input.fecha_min">
    </div>
    <div class="form-group col-md-3">
        <label>Fecha máxima</label>
        <input type="date" class="form-control" placeholder="Fecha" v-model="input.fecha_max">
    </div>
    <input type="submit" value="Buscar" class="btn btn-primary btn-block btn-sm">
</form>
@endcomponent

@push('scripts')
<script type="module" src="{{asset('js/FiltroFechaGrado.js')}}"></script>
@endpush
