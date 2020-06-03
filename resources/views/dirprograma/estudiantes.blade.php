@extends('layouts.principal')
@section('title', 'Estudiantes')

@push('components')
    @include('components.app.Titulo')
    @include('components.app.sidebar')
    @include('components.app.badge')
    @include('components.app.select')
    @include('components.app.list-group')
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
        <div class="row mt-3">
            <div class="col-md-3 form-group">
                <label>Programa</label>
                <select v-model="filter.programa_id" class="form-control" @change="initDataTable()">
                    <option v-for="(programa) in datos.programas" :value="programa.id">@{{ programa.nombre }}</option>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label>Tipo de Grado</label>
                <select v-model="filter.tipo_grado_id" class="form-control" @change="onChangeTipoGrado()">
                    <option :value="undefined" disabled>Seleccione una opción</option>
                    <option v-for="(t_grado) in datos.tipos_grado" :value="t_grado.id">@{{ t_grado.nombre }}</option>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label>Fecha de Grado</label>
                <select v-model="filter.fecha_grado_id" class="form-control" @change="initDataTable()">
                    <option :value="undefined" disabled>Seleccione una opción</option>
                    <option v-for="(fecha) in datos.fechas_grado" :value="fecha.id">@{{ fecha.nombre }}</option>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label>Estado</label>
                <select v-model="filter.estado" class="form-control" @change="initDataTable()">
                    <option :value="undefined" disabled>Seleccione una opción</option>
                    <option value="aprobado">Aprobado</option>
                    <option value="no_aprobado">No Aprobado</option>
                    <option value="pendiente">Pendiente</option>
                </select>
            </div>
        </div>

        <div class=" mb-3">
            <button class="btn btn-primary btn-icon-split" @click="initFilter()">
                <span class="icon text-white-50">
                    <i class="fas fa-times"></i>
                </span>
                <span class="text">Limpiar Filtro</span>
            </button>
        </div>

        <table class="table dtable table-sm table-responsive-sm AppTable-Separated AppTable-List " id="tabla-estudiante" style="width:100%">
            <thead >
                <tr>
                    <th>Foto</th>
                    <th>Código</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Identificación</th>
                    <th>Celular</th>
                    <th>Estado Programa</th>
                    <th>Estado Secretaría</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody class="app-text-black-1">

            </tbody>

        </table>
    </div>
    <sidebar title="ESTUDIANTE" :show="show_est" @onhide="show_est = false">
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
                                <badge :estado="item.estado">@{{ item.estado }}</badge>
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
                            <div>@{{ item.nombre }}</div>
                            <div>
                                <badge :estado="item.estado">@{{ item.estado }}</badge>
                            </div>
                        </list-group-item-flex>
                    </list-group>
                </div>
            </div>
        </div>
    </sidebar>
    <sidebar title="DIRECCIÓN" :show="show_dir" @onhide="show_dir = false">
        <div class="pl-2 pr-2">
            <div class="d-flex flex-row align-items-center justify-content-between">
                <div class="font-weight-bold">
                    Documentos
                </div>
                <i class="text-primary fas fa-plus-circle mr-1 action-btn"></i>
            </div>
            <div>
                <list-group flush>
                    <list-group-item light :bold="false" >
                        Documento de Identidad
                        <template v-slot:actions >
                            <badge type="success" class="action-btn">Ver</badge>
                        </template>
                    </list-group-item>
                    <list-group-item light :bold="false" >
                        Resultado de Pruebas
                    </list-group-item>
                    <list-group-item light :bold="false" >
                        Paz y Salvo Egresados
                    </list-group-item>
                    <list-group-item light :bold="false" >
                        Ficha del Egresado
                    </list-group-item>
                    <list-group-item light :bold="false" >
                        Titulo de Grado
                    </list-group-item>
                </list-group>
            </div>
        </div>
        <template v-slot:footer class="p-3">
            <button type="button" class="btn btn-success btn-circle" title="Aprobar Estudiante">
                <i class="fas fa-check"></i>
            </button>
            <button type="button" class="ml-1 btn btn-outline-danger btn-circle float-right" title="Rechazar Estudiante">
                <i class="fas fa-times"></i>
            </button>
        </template>
    </sidebar>
@endsection


