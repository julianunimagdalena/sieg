@extends('layouts.principal')
@section('title', 'Estudiantes')

@push('components')
    @include('components.app.Titulo')
    @include('components.app.sidebar')
    @include('components.app.badge')
    @include('components.app.card-action')
    @include('components.app.icono-estado')
    @include('components.app.input')
    @include('components.app.icons-information')
    @include('components.app.modal')
    @include('components.app.select')
    @include('components.modals.verdocumento')
    @include('components.sidebars.documentos')
    @include('components.app.list-group')
    @include('components.inputs.InputFile')
    @include('components.filter.estudiante')
    @include('components.modals.CargaDocumentoModal')
@endpush


@push('csscomponent')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" />
<link rel="stylesheet" href="{{ asset('css/apptable.css')}}" />
@endpush

@push('scripts')
    <!--<script type="module" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="module" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>-->
    <script type="module" src="{{ asset('js/dirprograma/estudiantes.js') }}"></script>
@endpush


@section('content')
    <titulo>Consultar Aspirantes a Grado</titulo>
    <div>
        <div class="mb-3">
            <filter-estudiante v-model="filter" @initdtable="initDataTable()"></filter-estudiante>
        </div>

        <div class="mb-3">
            <!--<icons-information>
                <span class="ml-4 font-weight-bold">
                    <span>
                        Posibles Acciones:
                    </span>
                    <span class="text-primary action-btn ml-3 mr-3 text-left">
                        <i class="fas fa-location-arrow"></i> Ver
                    </span>
                    <span class="text-secondary action-btn mr-3 text-center">
                        <i class="fas fa-cog"></i> Generar
                    </span>
                    <span class="text-secondary action-btn mr-3">
                        <i class="fas fa-upload"></i> Cargar
                    </span>
                    <span class="text-success action-btn mr-3 text-right">
                        <i class="fas fa-check"></i> Aprobar
                    </span>
                    <span class="text-danger action-btn ">
                        <i class="fas fa-times"></i> Rechazar
                    </span>
                </span>
            </icons-information>-->
        </div>

        <table class="table dtable table-sm table-responsive-sm AppTable-Separated AppTable-List " id="tabla-estudiante" style="width:100%">
            <thead style="font-size: 13px;">
                <tr>
                    <th>Foto</th>
                    <th>Código</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Fecha de Grado</th>
                    <th>Estado</th>
                    <th>Estado Programa</th>
                    <th>Estado Secretaría</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody class="app-text-black-1">

            </tbody>

        </table>
    </div>
    <sidebar title="ESTUDIANTE" :show="show_est" @onhide="show_est = false" primary id="sidebar-estudiante">
        <div class="app-text-black-1" v-if="estudiante">
            <div class="text-center">
                <img :src="estudiante.info.foto" alt="" class="img-fluid data-list-img">
                <div class="font-weight-bold mt-1">@{{ estudiante.info.nombre }}</div>
                <div class="mt-2">@{{ estudiante.info.codigo}}</div>
                <div class="">@{{ estudiante.info.programa}}</div>
            </div>
            <hr/>
            <div class="pl-2 pr-2">
                <div class="d-flex flex-row align-items-center justify-content-between">
                    <div class="font-weight-bold">
                        Proceso
                    </div>
                </div>
                <div class="mt-1">
                    <list-group flush>
                        <list-group-item-flex light md v-for="(item) in estudiante.proceso" :key="item.proceso" >
                            <div class="">
                                @{{ item.proceso }}
                            </div>
                            <div class="ml-2">
                                <icono-estado :estado="item.estado"></icono-estado>

                            </div>
                        </list-group-item-flex>
                    </list-group>
                </div>
            </div>
            <hr/>
            <div class="pl-2 pr-2">
                <div class="font-weight-bold">
                    Paz y Salvos
                </div>
                <div>
                    <list-group flush>
                        <list-group-item-flex light sm v-for="(item) in estudiante.paz_salvos" :key="item.nombre" :bold="false">
                            <div class="text-initial">@{{ item.nombre }}</div>
                            <div>
                                <icono-estado :estado="item.estado"></icono-estado>
                            </div>
                        </list-group-item-flex>
                    </list-group>
                </div>
            </div>
        </div>
    </sidebar>
    <sidebar-documentos :show="show_dir" @hide="show_dir = false" :estudiante_data="estudiante">

    </sidebar-documentos>
    <!--<sidebar title="DIRECCIÓN" :show="show_dir" @onhide="show_dir = false" primary id="sidebar-direccion">
        <div class="mb-3 font-weight-bold pl-2 pr-2">
            <span>
                Posibles Acciones:
            </span>
            <span class="text-primary action-btn ml-3 mr-3 text-left">
                <i class="fas fa-location-arrow"></i> Ver
            </span>
            <span class="text-secondary action-btn mr-3 text-center">
                <i class="fas fa-cog"></i> Generar
            </span>
            <span class="text-secondary action-btn mr-3">
                <i class="fas fa-upload"></i> Cargar
            </span>
            <div class="mt-1">
                <span class="text-success action-btn mr-3 text-right">
                    <i class="fas fa-check"></i> Aprobar
                </span>
                <span class="text-danger action-btn ml-2">
                    <i class="fas fa-times"></i> Rechazar
                </span>
            </div>
        </div>
        <hr/>
        <div class="pl-2 pr-2">
            <div class="d-flex flex-row align-items-center justify-content-between">
                <div class="font-weight-bold">
                    Documentos
                </div>
                <i class="text-primary fas fa-plus-circle mr-1 action-btn"></i>
            </div>
            <div>
                <list-group>
                    <list-group-item light :bold="false" v-for="(documento) in datos.documentos" :estado="documento.estado">
                        @{{ documento.nombre }}

                        <template v-slot:actions >
                            <span v-if="documentCanSomething(documento)">
                                <span class="text-primary action-btn mr-1"
                                 @click="verDocumento(documento.id)"
                                  v-if="documento.can_show" title="Ver Documento">
                                    <i class="fas fa-location-arrow"></i>
                                </span>
                                <span class="text-secondary action-btn mr-1" v-if="documento.can_generar"
                                    title="Generar Documento"
                                    @click="generar(documento)">
                                    <i class="fas fa-cog"></i>
                                </span>
                                <span
                                    v-if="documento.can_aprobar"
                                    @click="estadoDocumento('aprobar', documento.id)"
                                    class="text-success action-btn mr-1"
                                    title="Aprobar Documento">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span
                                    v-if="documento.can_rechazar"
                                    @click="forms.documento = documento"
                                    data-toggle="modal"
                                    data-target="#modalRechazarDocumento"
                                    class="text-danger action-btn mr-1"
                                    title="Rechazar Documento">
                                    <i class="fas fa-times"></i>
                                </span>
                                <span
                                v-if="documento.can_cargar"
                                @click="forms.documento = documento"
                                data-toggle="modal"
                                data-target="#cargaDocumentoModal"
                                class="text-secondary action-btn mr-1"
                                title="Cargar Documento">
                                    <i class="fas fa-upload"></i>
                                </span>
                            </span>
                            <badge v-else class="action-btn" :estado="documento.estado">@{{ documento.estado}}</badge>
                        </template>
                    </list-group-item>
                </list-group>
            </div>
            <hr/>
            <div>
                <div class="font-weight-bold">
                    Información Adicional del Estudiante
                </div>
                <div class="mt-2">
                    <form @submit.prevent="onSubmitInfoExtra()" v-if="estudiante && estudiante.extra">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 form-group">
                                <app-input
                                    label="Código Pruebas Saber Pro"
                                    placeholder="código"
                                    v-model="estudiante.extra.codigo_ecaes"
                                    required
                                    :errors="errors.estudiante.extra.codigo_ecaes"
                                    @input="errors.estudiante.extra.codigo_ecaes = undefined"
                                />
                            </div>
                            <div class="col-md-12 col-sm-12 form-group">
                                <app-input
                                    label="Resultado Pruebas Saber Pro"
                                    placeholder="Resultado"
                                    v-model="estudiante.extra.resultado_ecaes"
                                    required
                                    :errors="errors.estudiante.extra.resultado_ecaes"
                                    @input="errors.estudiante.extra.resultado_ecaes = undefined"
                                />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 form-group">
                                <app-input
                                    label="Título de Memoria de Grado"
                                    placeholder="Título"
                                    v-model="estudiante.extra.titulo_memoria_grado"
                                    required
                                    :errors="errors.estudiante.extra.titulo_memoria_grado"
                                    @input="errors.estudiante.extra.titulo_memoria_grado = undefined"
                                />
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12 col-sm-12 form-group">
                                <button class="btn btn-primary" type="submit">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <template v-slot:footer class="p-3">
            <button type="button" class="btn btn-success btn-circle"
                title="Aprobar Estudiante" @click="aprobarEstudiante()" :disabled="!datos.can_aprobar">
                <i class="fas fa-check"></i>
            </button>
            <button type="button" class="ml-1 btn btn-outline-danger btn-circle float-right"
                data-toggle="modal" data-target="#modalNoAprobarEstudiante"
                title="Rechazar Estudiante" >
                <i class="fas fa-times"></i>
            </button>
        </template>
    </sidebar>
    <modal id="modalNoAprobarEstudiante" title="Motivo de Rechazo" :onsubmit="rechazarEstudiante" >
        <app-input
            v-if="estudiante"
            label="Motivo"
            type="textarea"
            v-model="estudiante.motivo"
            :errors="errors.estudiante.motivo"
            @input="errors.estudiante.motivo = undefined"
        />
    </modal>
    <modal
        id="modalRechazarDocumento"
        title="Rechazar Documento Estudiante"
        @submit="estadoDocumento( 'rechazar' , forms.documento.id, forms.documento.motivo)"
        large
        buttontext="Enviar"
    >
        <div class="form-group">
            <app-input
                label="Motivo"
                required
                placeholder="Motivo"
                v-model="forms.documento.motivo"
                type="textarea"
            />
        </div>
    </modal>-->
    <modal
        id="modalInformacionEstudiante"
        title="Información Estudiante"
        :onsubmit="actualizarEstudiante"
        large
        buttontext="Actualizar">

        <div class="form-group form-row">
            <div class="col-md-6">
                <app-input
                    label="Nombre"
                    v-model="datos.estudiante.nombres"
                    disabled
                />
            </div>
            <div class="col-md-6">
                <app-input
                    label="Apellido"
                    v-model="datos.estudiante.apellidos"
                    disabled
                />
            </div>
        </div>
        <div class="form-group form-row">
            <div class="col-md-4">
                <app-input
                    label="Municipio de Expedición"
                    v-model="datos.estudiante.municipio_expedicion"
                    disabled
                />
            </div>
            <div class="col-md-3">
                <app-input
                    label="Fecha de Expedición"
                    disabled
                    v-model="datos.estudiante.fecha_expedicion"
                />
            </div>
            <div class="col-md-2">
                <app-input
                    label="T Documento"
                    v-model="datos.estudiante.tipo_documento"
                    disabled
                />
            </div>
            <div class="col-md-3">
                <app-input
                    label="Documento"
                    v-model="datos.estudiante.documento"
                    disabled
                />
            </div>
        </div>
        <div class="form-group form-row">
            <div class="col-md-6">
                <app-input
                    label="Lugar de Nacimiento"
                    v-model="datos.estudiante.lugar_nacimiento"
                    disabled
                />
            </div>
            <div class="col-md-6">
                <app-input
                    label="Fecha Nacimiento"
                    v-model="datos.estudiante.fecha_nacimiento"
                    disabled
                />
            </div>
        </div>
        <div class="form-group form-row">
            <div class="col-md-6">
                <app-input
                    label="Correo"
                    v-model="datos.estudiante.correo"
                    disabled
                />
            </div>
            <div class="col-md-6">
                <app-input
                    label="Celular"
                    v-model="datos.estudiante.celular"
                    disabled
                />
            </div>
        </div>
        <div class="form-group form-row">
            <div class="col-md-6">
                <app-input
                    label="Programa"
                    v-model="datos.estudiante.programa"
                    disabled
                />
            </div>
            <div class="col-md-6">
                <app-input
                    label="Código"
                    v-model="datos.estudiante.codigo"
                    disabled
                />
            </div>
        </div>
        <div class="form-group form-row">
            <div class="col-md-6">
                <app-input
                    label="Titulo Grado"
                    v-model="datos.estudiante.titulo_grado"
                    disabled
                />
            </div>
            <div class="col-md-6">
                <app-input
                    label="Modalidad Grado"
                    v-model="datos.estudiante.modalidad_grado"
                    disabled
                />
            </div>
        </div>

        <div class="form-group form-row">
            <div class="col-md-6">
                <app-input
                    label="Descripción Opción Grado"
                    v-model="datos.estudiante.descripcion_opcion_grado"
                    disabled
                />
            </div>
            <div class="col-md-6">
                <app-input
                    label="Nota Grado"
                    v-model="datos.estudiante.nota_grado"
                    disabled
                />
            </div>
        </div>

    </modal>
@endsection


