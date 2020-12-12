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
<div class="row">
    <div class="col-md-8 col-sm-12">
        <titulo>Configuración de Programas</titulo>
    </div>
    <div class="col-md-4 col-sm-12">
        <button class="btn btn-sm btn-primary float-right btn-icon-split ml-2" @click="openModal('#modalAddPrograma')">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">CREAR</span>
        </button>
    </div>
</div>
<div class="mt-3">
    <div class="row">
        <div class="col-md-6 col-sm-12 form-group">
            <app-select label="Programa" v-model="programa.id" input_class="bselect" @input="onChangePrograma()"
                default_text="Seleccione un Programa">
                <option v-for="pr in datos.programas" :value="pr.id">@{{ pr.nombre }}</option>
            </app-select>
        </div>
        <template v-if="programa.id">
            <div class="col-md-2 col-sm-12 form-group mt-2">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="sw-carga-ecaes"
                        v-model="programa.carga_ecaes" @change="onChangeCarga('carga-ecaes')">
                    <label class="custom-control-label" for="sw-carga-ecaes">Cargar Ecaes</label>
                </div>
                <div class="custom-control custom-switch mt-3">
                    <input type="checkbox" class="custom-control-input" id="sw-carga-titulo-grado"
                        v-model="programa.carga_titulo_grado" @change="onChangeCarga('carga-titulo-grado')">
                    <label class="custom-control-label" for="sw-carga-titulo-grado">Cargar Titulo de Grado</label>
                </div>
            </div>
            <div class="col-md-4 col-sm-12 form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="sw-diligencia-encuesta"
                        v-model="programa.diligencia_encuesta" @change="onChangeCarga('diligencia-encuesta')">
                    <label class="custom-control-label" for="sw-diligencia-encuesta">¿Diligencia Encuesta?</label>
                </div>
            </div>
        </template>
    </div>
</div>
<hr />
<div class="row" v-if="programa.id">
    <div class="col-md-6 col-sm-12 mb-4 mb-md-0">
        <card-action title="Paz y Salvos" fluid>
            <list-group flush>
                <list-group-item actions light v-for="paz in programa.paz_salvos" :key="paz.id">
                    @{{ paz.nombre }}
                </list-group-item>
            </list-group>
        </card-action>
    </div>
    <div class="col-md-6 col-sm-12">
        <card-action title="Documentos" @onAdd="openModal('#modalDocumentos')" fluid>
            <list-group flush>
                <list-group-item actions light v-for="doc in programa.documentos" :can_delete="doc.can_delete"
                    :key="doc.id" @onDelete="onDeleteDocumento(doc)">
                    @{{ doc.nombre }}
                </list-group-item>
            </list-group>
        </card-action>
    </div>
</div>


<modal id="modalPazSalvos" :onsubmit="onClickAddPazSalvo" title="Añadir Paz y Salvo" @onhide="form.paz = {}">
    <div class="form-group">
        <label>Paz y Salvo</label>
        <select v-model="form.paz.paz_salvo_id" class="form-control" @input="errors.paz.paz_salvo_id = undefined">
            <option selected disabled :value="undefined">Seleccione una Opción</option>
            <option :value="null">Otro</option>
            <option v-for="pz in datos.paz_salvos" :value="pz.id">@{{ pz.nombre }}</option>
        </select>
    </div>
    <div class="form-group" v-if="form.paz.paz_salvo_id === null">
        <app-input label="Nombre Paz y Salvo" placeholder="Nombre" v-model="form.paz.paz_salvo_nombre"
            :errors="errors.paz.paz_salvo_nombre" @input="errors.paz.paz_salvo_nombre = undefined" />
    </div>
</modal>

<modal id="modalDocumentos" :onsubmit="onclickAddDocumento" title="Añadir Documento" @onhide="form.documento = {}">
    <div class="form-group">
        <label>Documento</label>
        <select v-model="form.documento.documento_id" class="form-control"
            @input="errors.documento.documento_id = undefined">
            <option selected disabled :value="undefined">Seleccione una Opción</option>
            <option :value="null">Otro</option>
            <option v-for="doc in datos.documentos" :value="doc.id">@{{ doc.nombre }}</option>
        </select>
    </div>
    <div v-if="form.documento.documento_id === null">
        <div class="form-group">
            <app-input label="Nombre Documento" placeholder="Nombre" v-model="form.documento.documento_nombre"
                :errors="errors.documento.documento_nombre" @input="errors.documento.documento_nombre = undefined" />
        </div>
        <div class="form-group">
            <app-input label="Abreviatura Documento" placeholder="Abreviatura" v-model="form.documento.documento_abrv"
                :errors="errors.documento.documento_abrv" @input="errors.documento.documento_abrv = undefined" />
        </div>
    </div>
</modal>


<modal id="modalAddPrograma" :onsubmit="onclickAddPrograma" title="Añadir Programa" large>
    <div class="row">
        <div class="col-md-6 col-sm-12 form-group">
            <app-input label="Nombre" placeholder="Código" v-model="form.programa.nombre"
                :errors="errors.programa.nombre" @input="errors.programa.nombre = undefined" />
        </div>
        <div class="col-md-6 col-sm-12 form-group">
            <app-input label="Código" placeholder="Código" v-model="form.programa.codigo"
                :errors="errors.programa.codigo" @input="errors.programa.codigo = undefined" />
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-sm-12 form-group">
            <app-select label="Facultad" v-model="form.programa.facultad_id" :errors="errors.programa.facultad_id"
                @input="errors.programa.facultad_id = undefined">

                <option v-for="facultad in datos.facultades" :value="facultad.id">@{{ facultad.nombre }}</option>
            </app-select>
        </div>
        <div class="col-md-6 col-sm-12 form-group">
            <app-select label="Modalidad" v-model="form.programa.modalidad_id" :errors="errors.programa.modalidad_id"
                @input="errors.programa.modalidad_id = undefined">
                <option v-for="modalidad in datos.modalidades" :value="modalidad.id">@{{ modalidad.nombre }}</option>
            </app-select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-sm-12 form-group">
            <app-select label="Nivel de Estudio" v-model="form.programa.nivel_estudio_id"
                :errors="errors.programa.nivel_estudio_id" @input="errors.programa.nivel_estudio_id = undefined">
                <option v-for="nivel in datos.niveles_estudio" :value="nivel.id">@{{ nivel.nombre }}</option>
            </app-select>
        </div>
        <div class="col-md-6 col-sm-12 form-group">
            <app-select label="Jornada" v-model="form.programa.jornada_id" :errors="errors.programa.jornada_id"
                @input="errors.programa.jornada_id = undefined">
                <option v-for="jornada in datos.jornadas" :value="jornada.id">@{{ jornada.nombre }}</option>
            </app-select>
        </div>
    </div>
</modal>
@endsection


@push('scripts')
<script type="module" src="{{asset('js/administrador/programas.js')}}"></script>
@endpush
