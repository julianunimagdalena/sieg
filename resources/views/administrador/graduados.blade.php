@extends('layouts.principal')
@section('title', 'Graduados')

@push('components')
@include('components.app.Titulo')
@include('components.app.card-action')
@include('components.app.icon-btn')
@include('components.app.input')
@include('components.app.select')
@include('components.app.modal')
@endpush

@section('content')
<titulo>Graduados</titulo>

<div>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <card-action title="ACCIONES">
                <div class="row">
                    <div class="col-md-4 col-sm-12 form-group">
                        <app-select label="Facultad" v-model="filter.facultad_id" @input="onChangeFacultad()">
                            <option v-for="facultad in datos.facultades" :value="facultad.id">
                                @{{ facultad.nombre }}
                            </option>
                        </app-select>
                    </div>

                    <div class="col-md-4 col-sm-12 form-group">
                        <app-select label="Programa" v-model="filter.programa_id" id="select-programas"
                            input_class="bselect" @input="initDataTable()">
                            <option v-for="programa in datos.programas" :value="programa.id">
                                @{{ programa.nombre }}
                            </option>
                        </app-select>
                    </div>

                    <div class="col-md-4 col-sm-12 form-group">
                        <app-select label="Modalidad" v-model="filter.modalidad_id" @input="initDataTable()">
                            <option v-for="modalidad in datos.modalidades" :value="modalidad.id">@{{ modalidad.nombre }}
                            </option>
                        </app-select>
                    </div>

                    <div class="col-md-4 col-sm-12 form-group">
                        <app-select label="Tipo de Grado" v-model="filter.tipo_grado_id" default_text="Todos"
                            :disabled="false" @input="onChangeTipoGrado()">
                            <option v-for="tipo in datos.tipos_grado" :value="tipo.id">@{{ tipo.nombre }}</option>
                        </app-select>
                    </div>


                    <div class="col-md-4 col-sm-12 form-group">
                        <app-select label="Fecha Grado" v-model="filter.fecha_grado_id" input_class="bselect"
                            id="select-fecha-grado" @input="initDataTable()">
                            <option v-for="fecha in datos.fechas_grado" :value="fecha.id">
                                @{{ fecha.nombre}}
                            </option>
                        </app-select>
                    </div>

                    <div class="col-md-4 col-sm-12 form-group">
                        <app-select label="Genero" v-model="filter.genero_id" @input="initDataTable()">
                            <option v-for="genero in datos.generos" :value="genero.id">@{{ genero.nombre }}</option>
                        </app-select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12 form-group">
                        <app-input label="Fecha Inicial" v-model="filter.fecha_inicial" type="date"
                            @input="initDataTable()" />
                    </div>

                    <div class="col-md-6 col-sm-12 form-group">
                        <app-input label="Fecha Inicial" v-model="filter.fecha_final" type="date"
                            @input="initDataTable()" />
                    </div>
                </div>

                <div class="text-center">
                    <button class="btn btn-sm btn-danger btn-icon-split" @click="initFilter()">
                        <span class="icon text-white-50">
                            <i class="fas fa-times"></i>
                        </span>
                        <span class="text">Limpiar Filtro</span>
                    </button>
                    <button class="btn btn-sm btn-primary btn-icon-split ml-3" data-toggle="modal"
                        data-target="#modalRegistrarGraduados">
                        <span class="icon text-white-50">
                            <i class="fas fa-user-graduate"></i>
                        </span>
                        <span class="text">Registrar Graduados</span>
                    </button>
                    <a href="{{ Request::root() }}/administrador/registrar-graduado">
                        <icon-button color="secondary" sm text="Registrar Graduado" c_class="ml-3">
                            <i class="fas fa-user"></i>
                        </icon-button>
                    </a>
                </div>
            </card-action>
        </div>
    </div>
    <div class="mt-4">
        <table class="dtable table table-sm table-responsive-sm" id="tabla-graduados">
            <thead>
                <tr>
                    <th>Identificación</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Facultad</th>
                    <th>Programa</th>
                    <th>Fecha Grado</th>
                    <th>Acciones</th>
                </tr>
            </thead>


            <tbody class="app-text-black-1" style="font-size: 14px">

            </tbody>
        </table>
    </div>
</div>

<modal title="Registrar Graduados" id="modalRegistrarGraduados" :onsubmit="onSubmitRegistrarGraduados">
    <div class="form-group">
        <app-input label="Fecha Inicial" type="date" v-model="form.graduados.fecha_inicial"
            :errors="errors.graduados.fecha_inicial" @input="errors.graduados.fecha_inicial = undefined" />
    </div>
    <div class="form-group">
        <app-input label="Fecha Final" type="date" v-model="form.graduados.fecha_final"
            :errors="errors.graduados.fecha_final" @input="errors.graduados.fecha_final = undefined" />
    </div>
</modal>
@endsection


@push('scripts')
<script type="module" src="{{asset('js/administrador/graduados.js')}}"></script>
@endpush
