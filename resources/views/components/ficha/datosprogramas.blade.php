@component('component', ['id' => 'datos-programas-component'])
<div>
    <card-action title="PROGRAMAS" @onadd="openModal('#modal-add-programa')" fluid>
        <table class="table table-sm">
            <thead class="thead-light">
                <tr>
                    <th>Facultad</th>
                    <th>Programa</th>
                    <th>Código</th>
                    <th>Modalidad</th>
                    <th>Fecha de Grado</th>
                </tr>
            </thead>
            <tbody class="app-text-black-1">
                <tr v-for="programa in datos.programas_egresado" class="TableRow-2">
                    <td>@{{ programa.resolve.facultad }}</td>
                    <td>@{{ programa.resolve.programa }}</td>
                    <td>@{{ programa.codigo }}</td>
                    <td>@{{ programa.resolve.modalidad }}</td>
                    <td>@{{ programa.fecha_grado }}</td>
                </tr>
            </tbody>
        </table>
    </card-action>

    <modal title="Form Programa" id="modal-add-programa" :onsubmit="onSubmitFormPrograma" large>
        <div class="row">
            <div class="col-md-4 col-sm-12 form-group">
                <app-select label="Facultad" required v-model="form.programa.facultad_id"
                    :errors="errors.programa.facultad_id" @input="onChangeFacultad()">
                    <option v-for="facultad in datos.facultades" :value="facultad.id">@{{ facultad.nombre }}</option>
                </app-select>
            </div>
            <div class="col-md-4 col-sm-12 form-group">
                <app-select label="Programa" required v-model="form.programa.programa_id" id="select-programas"
                    :errors="errors.programa.programa_id" @input="errors.programa.programa_id = undefined">
                    <option v-for="programa in datos.programas" :value="programa.id">@{{ programa.nombre }}</option>
                </app-select>
            </div>
            <div class="col-md-4 col-sm-12 form-group">
                <app-select label="Jornada" required v-model="form.programa.jornada_id"
                    :errors="errors.programa.jornada_id" @input="errors.programa.jornada_id = undefined">
                    <option v-for="jornada in datos.jornadas" :value="jornada.id">@{{ jornada.nombre }}</option>
                </app-select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-12 form-group">
                <app-select label="Modalidad" required v-model="form.programa.modalidad_id"
                    :errors="errors.programa.modalidad_id" @input="errors.programa.modalidad_id = undefined">
                    <option v-for="modalidad in datos.modalidades" :value="modalidad.id">@{{ modalidad.nombre }}
                    </option>
                </app-select>
            </div>
            <div class="col-md-4 col-sm-12 form-group">
                <app-select label="Distinción" v-model="form.programa.distincion_id" required
                    :errors="errors.programa.distincion_id" @input="errors.programa.distincion_id = undefined">
                    <option v-for="distincion in datos.distinciones" :value="distincion.id">@{{ distincion.nombre }}
                    </option>
                </app-select>
            </div>
            <div class="col-md-4 col-sm-12 form-group">
                <app-input label="Código" required v-model="form.programa.codigo" :errors="errors.programa.codigo"
                    placeholder="Código" @input="errors.programa.codigo = undefined" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-12 form-group">
                <app-input label="Acta" required v-model="form.programa.acta" :errors="errors.programa.acta"
                    placeholder="Acta" @input="errors.programa.acta = undefined" />
            </div>
            <div class="col-md-4 col-sm-12 form-group">
                <app-input label="Libro" required v-model="form.programa.libro" :errors="errors.programa.libro"
                    placeholder="Libro" @input="errors.programa.libro = undefined" />
            </div>
            <div class="col-md-4 col-sm-12 form-group">
                <app-input label="Folio" required v-model="form.programa.folio" :errors="errors.programa.folio"
                    placeholder="Folio" @input="errors.programa.folio = undefined" />
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 col-sm-12 form-group">
                <app-input label="Fecha de Grado" required v-model="form.programa.fecha_grado" type="date"
                    :errors="errors.programa.fecha_grado" @input="errors.programa.fecha_grado = undefined" />
            </div>
        </div>
    </modal>
</div>
@endcomponent

@push('scripts')
<script type="module" src="{{ asset('js/ficha/DatosProgramas.js') }}"></script>
@endpush
