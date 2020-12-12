@component('component', ['id' => 'usuario-modal-component'])
<modal :id="id" :title="title" :onSubmit="submit" :buttonDisabled="cargando">
    <div class="row">
        <div class="col-md-6 form-group">
            <label>Identificación</label>
            <input type="text" class="form-control" placeholder="Identificación" v-model="input.identificacion"
                v-on:input="errors.identificacion=undefined" v-on:blur="fetchByIdentificacion">
            <small class="text-danger" v-if="errors.identificacion">@{{errors.identificacion[0]}}</small>
        </div>
        <div class="col-md-6 form-group">
            <label>Usuario</label>
            <input type="text" class="form-control" placeholder="Usuario" v-model="input.username"
                v-on:input="errors.username=undefined">
            <small class="text-danger" v-if="errors.username">@{{errors.username[0]}}</small>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 form-group">
            <label>Nombres</label>
            <input type="text" class="form-control" placeholder="Nombres" v-model="input.nombres"
                v-on:input="errors.nombres=undefined" :disabled="input.id">
            <small class="text-danger" v-if="errors.nombres">@{{errors.nombres[0]}}</small>
        </div>
        <div class="col-md-6 form-group">
            <label>Apellidos</label>
            <input type="text" class="form-control" placeholder="Apellidos" v-model="input.apellidos"
                v-on:input="errors.apellidos=undefined" :disabled="input.id">
            <small class="text-danger" v-if="errors.apellidos">@{{errors.apellidos[0]}}</small>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 form-group">
            <label>Rol</label>
            <select class="form-control" v-model="input.rol_id" v-on:input="errors.rol_id=undefined">
                <option :value="undefined" hidden>Seleccione una opción</option>
                <option v-for="rol in rolesDisponibles" :value="rol.id">@{{rol.nombre}}</option>
            </select>
            <small class="text-danger" v-if="errors.rol_id">@{{errors.rol_id[0]}}</small>
        </div>
        <div class="col-md-4 form-group mt-auto">
            <label class="float-left">Activo</label>
            <div class="custom-control custom-switch float-right">
                <input type="checkbox" class="custom-control-input" id="usuario-activo" v-model="input.activo">
                <label class="custom-control-label" for="usuario-activo">@{{input.activo ? 'Si' : 'No'}}</label>
            </div>
        </div>
    </div>
    <div v-if="canElegirProgramas">
        <div class="form-group">
            <label>Programas</label>
            <select class="form-control bselect" multiple v-model="input.programa_ids"
                v-on:input="errors.programa_ids=undefined">
                <option v-for="prg in programas" :value="prg.id">@{{prg.nombre}}</option>
            </select>
            <small class="text-danger" v-if="errors.programa_ids">@{{errors.programa_ids[0]}}</small>
        </div>
        <ul>
            <li v-for="prg in programasSeleccionados">@{{prg.nombre}}</li>
        </ul>
    </div>
    <div v-if="canElegirDependencias">
        <div class="form-group">
            <label>Dependencias</label>
            <select class="form-control bselect" multiple v-model="input.dependencia_ids"
                v-on:input="errors.dependencia_ids=undefined">
                <option v-for="dep in dependencias" :value="dep.id">@{{dep.nombre}}</option>
            </select>
            <small class="text-danger" v-if="errors.dependencia_ids">@{{errors.dependencia_ids[0]}}</small>
        </div>
        <ul>
            <li v-for="dependencia in dependenciasSeleccionadas">@{{dependencia.nombre}}</li>
        </ul>
    </div>
</modal>
@endcomponent

@push('scripts')
<script type="module" src="{{asset('js/UsuarioModal.js')}}"></script>
@endpush
