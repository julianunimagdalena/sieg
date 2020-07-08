@extends('layouts.principal')
@section('title', 'Configuración de Programas')


@push('csscomponent')
@endpush


@push('components')
    @include('components.app.Titulo')
    @include('components.app.input')
    @include('components.app.select')
    @include('components.app.card-action')
    @include('components.app.list-group')
    @include('components.app.modal')
@endpush

@section('content')
    <titulo>Configuración de Programas</titulo>
    <div class="mt-3">
        <div class="row">
            <div class="col-md-6 col-sm-12 form-group">
                <app-select
                label="Programa"
                v-model="programa.id"
                input_class="bselect"
                @input="onChangePrograma()"
                default_text="Seleccione un Programa"
                >
                    <option v-for="pr in datos.programas" :value="pr.id">@{{ pr.nombre }}</option>
                </app-select>
            </div>
            <div class="col-md-2 col-sm-12 form-group mt-2" v-if="programa.id">
                <div class="custom-control custom-switch" >
                    <input type="checkbox" class="custom-control-input" id="sw-carga-ecaes" v-model="programa.carga_ecaes"
                    @change="onChangeCarga('carga-ecaes')">
                    <label class="custom-control-label" for="sw-carga-ecaes">Cargar Ecaes</label>
                </div>
                <div class="custom-control custom-switch mt-3">
                    <input type="checkbox" class="custom-control-input" id="sw-carga-titulo-grado" v-model="programa.carga_titulo_grado"
                    @change="onChangeCarga('carga-titulo-grado')">
                    <label class="custom-control-label" for="sw-carga-titulo-grado">Cargar Titulo de Grado</label>
                </div>
            </div>
            <div class="col-md-2 col-sm-12 form-group">
            </div>
        </div>
    </div>
    <hr />
    <div class="row" v-if="programa.id">
        <div class="col-md-6 col-sm-12">
           <card-action title="Paz y Salvos" @onAdd="openModal('#modalPazSalvos')" fluid>
                <list-group flush>
                    <list-group-item
                        actions
                        light
                        v-for="paz in programa.paz_salvos"
                        @onDelete="onDeletePazSalvo(paz)"
                    >
                        @{{ paz.nombre }}
                    </list-group-item>
                </list-group>
           </card-action>
        </div>
        <div class="col-md-6 col-sm-12">
           <card-action title="Documentos" @onAdd="openModal('#modalDocumentos')" fluid>
                <list-group flush>
                    <list-group-item
                        actions
                        light
                        v-for="doc in programa.documentos"
                        @onDelete="onDeleteDocumento(doc)"
                    >
                    @{{ doc.nombre }}
                    </list-group-item>
                </list-group>
           </card-action>
        </div>
    </div>
    <modal id="modalAddPrograma" :onsubmit="onclickAddPrograma" title="Añadir Paz y Salvo" large>
        <div class="row">
            <div class="col-md-6 col-sm-12 form-group">
                <app-input
                    label="Nombre"
                    placeholder="Código"
                    v-model="form.programa.nombre"
                    :errors="errors.programa.nombre"
                    @input="errors.programa.nombre = undefined"
                />
            </div>
            <div class="col-md-6 col-sm-12 form-group">
                <app-input
                    label="Código"
                    placeholder="Código"
                    v-model="form.programa.codigo"
                    :errors="errors.programa.codigo"
                    @input="errors.programa.codigo = undefined"
                />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-12 form-group">
                <app-select
                    label="Facultad"
                    v-model="form.programa.facultad_id"
                    :errors="errors.programa.facultad_id"
                    @input="errors.programa.facultad_id"
                >

                </app-select>
            </div>
            <div class="col-md-6 col-sm-12 form-group">
                <app-select
                    label="Modalidad"
                    v-model="form.programa.modalidad_id"
                    :errors="errors.programa.modalidad_id"
                    @input="errors.programa.modalidad_id"
                >

                </app-select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-12 form-group">
                <app-select
                    label="Nivel de Estudio"
                    v-model="form.programa.nivel_estudio_id"
                    :errors="errors.programa.nivel_estudio_id"
                    @input="errors.programa.nivel_estudio_id"
                >

                </app-select>
            </div>
            <div class="col-md-6 col-sm-12 form-group">
                <app-select
                    label="Jornada"
                    v-model="form.programa.jornada_id"
                    :errors="errors.programa.jornada_id"
                    @input="errors.programa.jornada_id"
                >

                </app-select>
            </div>
        </div>
    </modal>
@endsection


@push('scripts')
<script type="module" src="{{asset('js/administrador/programas.js')}}"></script>
@endpush
