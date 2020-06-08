@component('component', ['id' => 'datos-laborales-component'])
<div >
    <!--<card title="Actualidad Laboral" id="actualidad-laboral-form">
        <form>
            <div class="form-group">
                <label>¿Actualmente se encuentra laborando?</label>

                <div class="custom-control custom-radio custom-control-inline ml-3">
                    <input
                    type="radio"
                    id="graduado-laborando1"
                    name="graduado-laborando"
                    class="custom-control-input"
                    @input="onChangeActualidadLaboral(true)"
                    v-model="forms.a_laboral"
                    value="1">
                    <label class="custom-control-label" for="graduado-laborando1">Si</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input
                        type="radio"
                        id="graduado-laborando2"
                        name="graduado-laborando"
                        class="custom-control-input"
                        @input="onChangeActualidadLaboral(false)"
                        v-model="forms.a_laboral" value="0">
                    <label class="custom-control-label" for="graduado-laborando2">No</label>
                </div>
            </div>
        </form>
    </card>-->

    <div class="">
        <card-action title="Información de Experiencias Laborales" fluid @onAdd="initModalInformacionLaboral()" id="xp-laboral-form">
            <table class="table table-sm">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Cargo</th>
                    <th scope="col">Empresa</th>
                    <th scope="col">Nivel Cargo</th>
                    <th scope="col">Duración</th>
                    <th scope="col">Meritos</th>
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody class="app-text-black-1">
                    <tr class="TableRow-2" v-for="(experiencia) in datos.experiencias">
                       <td class="font-weight-bold">@{{ experiencia.cargo }}</td>
                       <td>@{{ experiencia.empresa }}</td>
                       <td>@{{ experiencia.resolve.nivel_cargo }}</td>
                       <td>@{{ experiencia.resolve.duracion}}</td>
                       <td>@{{ experiencia.funciones }}</td>
                       <td class="font-weight-bold">
                            <i class="far fa-edit text-warning action-btn" @click="onEditDatoLaboral(experiencia)"></i>
                            <i class="far fa-trash-alt text-danger action-btn ml-3" @click="onDeleteDatoLaboral(experiencia)"></i>
                       </td>
                    </tr>
                </tbody>

            </table>
        </card-action>
    </div>

    <modal id="modalInformacionLaboral" title="Añadir Información Laboral" :onSubmit="onSubmitDatosLaborales" @onHide="forms.xp = {}" large>
        <div class="row">
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <app-input
                        label ="Empresa"
                        required
                        placeholder="Empresa"
                        v-model="forms.xp.empresa"
                        :errors="errors.xp.empresa"
                        @input="errors.xp.empresa = undefined"
                    />
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <app-select
                        label="Sector"
                        required
                        placeholder="Sector"
                        v-model="forms.xp.sector_id"
                        :errors="errors.xp.sector_id"
                        @input="errors.xp.sector_id = undefined"
                    >
                        <option v-for="sector in datos.sectores_empresa" :value="sector.id">@{{ sector.nombre }}</option>
                    </app-select>
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <app-select
                        label="Sector Económico"
                        required
                        v-model="forms.xp.sector_economico_id"
                        :errors="errors.xp.sector_economico_id"
                        @input="errors.xp.sector_economico_id = undefined"
                    >
                        <option v-for="sector in datos.sectores_economicos" :value="sector.id">@{{ sector.nombre }}</option>
                    </app-select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <app-select
                        label="País"
                        default_text="Seleccione un País"
                        required
                        v-model="forms.xp.pais_id"
                        :errors="errors.xp.pais_id"
                        @input="onChangePais()"
                        >
                        <option v-for="(pais) in datos.paises" :value="pais.id">@{{ pais.nombre }}</option>
                    </app-select>
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <app-select
                        label="Departamento"
                        default_text="Seleccione Departamento"
                        required
                        v-model="forms.xp.departamento_id"
                        :errors="errors.xp.departamento_id"
                        @input="onChangeDepartamento()"
                        >
                        <option v-for="(departamento) in datos.departamentos" :value="departamento.id">@{{ departamento.nombre }}</option>
                    </app-select>
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <app-select
                        label="Municipio"
                        default_text="Seleccione un Municipio"
                        required
                        v-model="forms.xp.municipio_id"
                        :errors="errors.xp.municipio_id"
                        @input="errors.xp.municipio_id = undefined"
                        >
                        <option v-for="(municipio) in datos.municipios" :value="municipio.id">@{{ municipio.nombre }}</option>
                    </app-select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <app-input
                        label ="Correo Electronico"
                        required
                        placeholder="Correo Electronico"
                        v-model="forms.xp.correo"
                        :errors="errors.xp.correo"
                        @input="errors.xp.correo = undefined"
                    />
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <app-input
                        label ="Teléfono"
                        required
                        placeholder="Teléfono"
                        v-model="forms.xp.telefono"
                        :errors="errors.xp.telefono"
                        @input="errors.xp.telefono = undefined"
                    />
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <app-input
                        label ="Dirección"
                        required
                        placeholder="Dirección"
                        v-model="forms.xp.direccion"
                        :errors="errors.xp.direccion"
                        @input="errors.xp.direccion = undefined"
                    />
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="form-group">
                    <app-select
                        label="Actividad Ecónomica"
                        required
                        input_class="bselect"
                        v-model="forms.xp.actividad_economica_id"
                        :errors="errors.xp.actividad_economica_id"
                        @input="errors.xp.actividad_economica_id = undefined"
                    >
                        <option v-for="actividad in datos.actividades_economicas" :value="actividad.id">
                            @{{ actividad.nombre }}
                        </option>
                    </app-select>
                </div>
            </div>
        </div>

        <hr/>

        <div class="row">
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <app-input
                        label ="Cargo"
                        required
                        placeholder="Cargo"
                        v-model="forms.xp.cargo"
                        :errors="errors.xp.cargo"
                        @input="errors.xp.cargo = undefined"
                    />
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <app-select
                        label="Niveles del cargo"
                        required
                        default_text="Seleccione un Cargo"
                        v-model="forms.xp.nivel_cargo_id"
                        :errors="errors.xp.nivel_cargo_id"
                        @input="errors.xp.nivel_cargo_id = undefined"
                    >
                        <option v-for="(nivel) in datos.niveles_cargo" :value="nivel.id">@{{ nivel.nombre }}</option>
                    </app-select>
                </div>
            </div>
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <app-select
                        label="Tipo de Vinculacion"
                        required
                        v-model="forms.xp.tipo_vinculacion_id"
                        :errors="errors.xp.tipo_vinculacion_id"
                        @input="errors.xp.tipo_vinculacion_id = undefined"
                        >
                        <option v-for="(tipo) in datos.tipos_vinculacion" :value="tipo.id">@{{ tipo.nombre}}</option>
                    </app-select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <app-select
                        label="Area de Desempeño"
                        required
                        v-model="forms.xp.area_desempeno_id"
                        :errors="errors.xp.area_desempeno_id"
                        @input="errors.xp.area_desempeno_id = undefined"
                    >
                        <option v-for="area in datos.areas_des" :value="area.id">@{{ area.nombre }}</option>
                    </app-select>
                </div>
            </div>

            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <app-select
                        label="Rango Salarial"
                        required
                        v-model="forms.xp.salario_id"
                        :errors="errors.xp.salario_id"
                        @input="errors.xp.salario_id = undefined"
                        >
                        <option v-for="(salario) in datos.rangos_salariales" :value="salario.id">@{{ salario.rango }}</option>
                    </app-select>
                </div>
            </div>

            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <app-select
                        label="Contrato Activo"
                        required
                        v-model="forms.xp.contrato_activo"
                        :errors="errors.xp.contrato_activo"
                        @input="errors.xp.contrato_activo = undefined"
                        >
                        <option :value="true" >Si</option>
                        <option :value="false" >No</option>
                    </app-select>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <app-select
                        label="Duración"
                        required
                        v-model="forms.xp.duracion_id"
                        :errors="errors.xp.duracion_id"
                        @input="errors.xp.duracion_id = undefined"
                        >
                        <option v-for="(duracion) in datos.duraciones" :value="duracion.id">@{{ duracion.nombre }}</option>
                    </app-select>
                </div>
            </div>

            <div class="col-md-4 col-sm-12">
                <div class="form-group">
                    <app-input
                        label="Fecha de Ingreso"
                        type="date"
                        required
                        v-model="forms.xp.fecha_ingreso"
                        :errors="errors.xp.fecha_ingreso"
                        @input="errors.xp.fecha_ingreso = undefined"
                    />
                </div>
            </div>

            <div class="col-md-4 col-sm-12" v-if="!forms.xp.contrato_activo">
                <div class="form-group">
                    <app-input
                        label="Fecha de Retiro"
                        placeholher="Fecha"
                        type="date"
                        required
                        v-model="forms.xp.fecha_retiro"
                        :errors="errors.xp.fecha_retiro"
                        @input="errors.xp.fecha_retiro = undefined"
                    />
                </div>
            </div>
        </div>

        <div class="form-group">
            <app-input
                label ="Funciones y/o méritos"
                required
                type="textarea"
                placeholder="Funciones y/o méritos"
                v-model="forms.xp.funciones"
                :errors="errors.xp.funciones"
                @input="errors.xp.funciones = undefined"
            />
        </div>

    </modal>
</div>
@endcomponent


@push('scripts')
<!--<script type="module" src="{{ asset('js/location.js') }}"></script>-->
<script type="module" src="{{ asset('js/ficha/DatosLaborales.js') }}"></script>
@endpush
