@component('component', ['id' => 'filter-estudiante-component'])
<div>
    <div :class="[{'col-md-11 offset-md-2 mt-3': !customcolumn}, filter_class]">
        <div class="row">
            <div class="col-md-3 col-sm-12 form-group">
                <label>Programa</label>
                <select v-model="filter.programa_id" class="form-control bselect" @change="onChange()">
                    <option :value="undefined">Todos</option>
                    <option v-for="(programa, index) in datos.programas" :value="programa.id" :selected="index === 0">@{{ programa.nombre }}</option>
                </select>
            </div>
            <div class="col-md-2 col-sm-12 form-group">
                <label>Tipo de Grado</label>
                <select v-model="filter.tipo_grado_id" class="form-control" @change="onChangeTipoGrado()">
                    <option :value="undefined">Todos</option>
                    <option v-for="(t_grado) in datos.tipos_grado" :value="t_grado.id">@{{ t_grado.nombre }}</option>
                </select>
            </div>
            <div class="col-md-2 col-sm-12 form-group">
                <label>Fecha de Grado</label>
                <select v-model="filter.fecha_grado_id" class="form-control" id="fecha-grado-filter" @change="onChange()">
                    <option :value="undefined">Todas</option>
                    <option v-for="(fecha) in datos.fechas_grado" :value="fecha.id">@{{ fecha.nombre }}</option>
                </select>
            </div>
            <div class="col-md-2 col-sm-12 form-group">
                <label>Estado</label>
                <select v-model="filter.estado" class="form-control" @change="onChange()">
                    <option :value="undefined">Todos</option>
                    <option value="aprobado">Aprobado</option>
                    <option value="no_aprobado">No Aprobado</option>
                    <option value="pendiente">Pendiente</option>
                </select>
            </div>
        </div>
    </div>
    <div class="text-center" :class="buttons_class">
        <button class="btn btn-sm btn-primary btn-icon-split" @click="initFilter()">
            <span class="icon text-white-50">
                <i class="fas fa-times"></i>
            </span>
            <span class="text">Limpiar Filtro</span>
        </button>
        <slot name="buttons"></slot>
    </div>
</div>
@endcomponent

@push('scripts')
<script type="module" src="{{ asset('js/components/filter/estudiante.js')}}">
</script>
@endpush
