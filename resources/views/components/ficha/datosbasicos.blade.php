@component('component', ['id' => 'datos-basicos-component'])
<form @submit.prevent="handleSubmit">
    <div class="row">
        <div class="col col-md-10">
            <card title="Datos Personales">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nombres</label>
                            <input type="text" class="form-control" disabled placeholder="Nombres"
                                v-model="input.nombres" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Apellidos</label>
                            <input type="text" class="form-control" disabled placeholder="Apellidos"  v-model="input.apellidos"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Sexo</label>
                            <select class="form-control" v-model="input.genero_id" @input="errors.genero_id=undefined">
                                <option value="" selected hidden>Seleccione una opción</option>
                                <option v-for="(sexo, key) in datos.sexos" :value="sexo.id">@{{ sexo.nombre }}</option>
                            </select>
                            <small class="text-danger" v-if="errors.genero_id">@{{errors.genero_id[0]}}</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Etnia</label>
                            <input type="text" class="form-control" disabled placeholder="Etnia">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>País de nacimiento</label>
                            <select class="form-control" v-model="input.pais_nacimiento_id" :disabled="input.pais_nacimiento_id">
                                <option value="" selected hidden>Seleccione una opción</option>
                                <option v-for="(pais) in datos.paises" :value="pais.id">@{{ pais.nombre }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Departamento de nacimiento</label>
                            <select class="form-control" v-model="input.departamento_nacimiento_id" :disabled="input.departamento_nacimiento_id">
                                <option value="" selected hidden>Seleccione una opción</option>

                                <option v-for="(departamento) in datos.departamentos" :value="departamento.id">@{{ departamento.nombre }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Municipio de nacimiento</label>
                            <select class="form-control" v-model="input.municipio_nacimiento_id" :disabled="input.municipio_nacimiento_id">
                                <option value="" disabled>Seleccione una opción</option>
                                <option v-for="(municipio) in datos.municipios" :value="municipio.id">@{{ municipio.nombre }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha de nacimiento</label>
                            <input type="date" class="form-control" disabled v-model="input.fecha_nacimiento">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Edad</label>
                            <input type="number" class="form-control" placeholder="Edad" disabled>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Estado civil</label>
                            <select class="form-control" ng-model="input.estado_civil_id">
                                <option value="" selected hidden>Seleccione una opción</option>
                                <option v-for="(estado) in datos.estados_civiles" :value="estado.id">@{{ estado.nombre }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <app-input
                                v-model="input.estrato"
                                type="number"
                                label="Estrato"
                                @input="errors.estrato = undefined"
                                placeholder="Estrato"
                                v-bind:errors="errors.estrato"/>
                        </div>
                    </div>
                </div>
            </card>
        </div>
        <div class="col col-md-2 mt-3">
            <div class="text-center">
                <img src="{{ asset('img/sin_perfil.png') }}" alt="" class="img-fluid">
                <br>
                <div class="btn-group-vertical" style="width:100%;" role="group" aria-label="...">
                    <button type="button" class="btn btn-light btn-block" title="Actualizar foto">
                        <i class="fa fas-pencil"></i> Actualizar
                    </button>
                    <button type="button" class="btn btn-success btn-block" title="Aprobar foto">
                        <i class="fa fa-plus"></i> &nbsp;Aprobar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <card title="Información Documento">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tipo</label>
                        <select class="form-control" v-model="input.tipo_documento_id">
                            <option value="" selected hidden>Seleccione una opción</option>
                            <option v-for="(t_documento) in datos.tipos_documento" :value="t_documento.id">@{{ t_documento.abrv || t_documento.nombre }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Numero</label>
                        <input type="number" class="form-control" disabled placeholder="Numero de documento" v-model="input.identificacion">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Lugar de expedición</label>
                        <input type="text" class="form-control" disabled placeholder="Lugar de expedicion" v-model="input.lugar_expedicion_documento">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <app-input
                        v-model="input.fecha_expedicion_documento"
                        type="date"
                        label="Fecha De Expedición"
                        @input="errors.fecha_expedicion_documento = undefined"
                        placeholder="Fecha"
                        v-bind:errors="errors.fecha_expedicion_documento"/>
                    </div>
                </div>
            </div>
        </card>
    </div>
    <div class="mt-3">
        <card title="Información de Contacto">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>País de residencia</label>
                        <select class="form-control" v-model="input.pais_residencia_id">
                            <option value="" selected hidden>Seleccione una opción</option>
                            <option v-for="(pais) in datos.paises" :value="pais.id">@{{ pais.nombre }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Departamento de residencia</label>
                        <select class="form-control" v-model="input.departamento_residencia_id">
                            <option value="" selected hidden>Seleccione una opción</option>
                            <option v-for="(departamento) in datos.departamentos_residencia" :value="departamento.id">@{{ departamento.nombre }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Municipio de residencia</label>
                        <select class="form-control" v-model="input.municipio_residencia_id" @input="errors.municipio_residencia_id = undefined">
                            <option value="" selected hidden>Seleccione una opción</option>
                            <option v-for="(municipio) in datos.municipios_residencia" value="municipio.id">@{{ municipio.nombre }}</option>
                        </select>
                        <small class="text-danger" v-if="errors.municipio_residencia_id">@{{ errors.municipio_residencia_id[0] }}</small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <app-input
                        v-model="input.telefono_fijo"
                        type="number"
                        label="Telefono Fijo"
                        @input="errors.telefono_fijo = undefined"
                        placeholder="Telefono"
                        v-bind:errors="errors.telefono_fijo"/>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <app-input
                        v-model="input.celular"
                        type="number"
                        label="Celular"
                        @input="errors.celular = undefined"
                        placeholder="Celular"
                        v-bind:errors="errors.celular"/>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <app-input
                        v-model="input.celular2"
                        type="number"
                        label="Celular 2"
                        @input="errors.celular2 = undefined"
                        placeholder="Celular 2"
                        v-bind:errors="errors.celular2"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <app-input
                        v-model="input.direccion"
                        label="Dirección"
                        @input="errors.direccion = undefined"
                        placeholder="Dirección"
                        v-bind:errors="errors.direccion"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <app-input
                        v-model="input.barrio"
                        label="Barrio / Sector"
                        @input="errors.barrio = undefined"
                        placeholder="Barrio o Sector"
                        v-bind:errors="errors.barrio"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <app-input
                        v-model="input.correo"
                        type="email"
                        label="Correo Electronico"
                        @input="errors.correo = undefined"
                        placeholder="Correo Electronico"
                        v-bind:errors="errors.correo"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <app-input
                        v-model="input.correo2"
                        type="email"
                        label="Correo Electronico 2 (Opcional)"
                        @input="errors.correo2 = undefined"
                        placeholder="Correo Electronico"
                        v-bind:errors="errors.correo2"/>
                    </div>
                </div>
            </div>
        </card>
    </div>
    <div class="mt-3">
        <button type="submit" class="btn btn-primary btn-block">
            Guardar
        </button>
    </div>
</form>
@endcomponent

@push('scripts')
<script type="module" src="{{ asset('js/ficha/DatosBasicos.js') }}"></script>
@endpush
