@component('component', ['id' => 'datos-academicos-component'])
<div class="container-fluid mt-3">
    <!--<h6 class="text-uppercase font-weight-bold text-primary mb-3">
        Información académica en la que se aspira a grado en la Universidad del Magdalena
    </h6>
    <table class="table table-responsive-md text-center">
        <thead class="bg-primary">
            <th class="text-white p-1">Código</th>
            <th class="text-white p-1">Facultad</th>
            <th class="text-white p-1">Programa</th>
            <th class="text-white p-1">Modalidad</th>
        </thead>
        <tbody>
            <td class="font-weight-bold p-1">CODIGO</td>
            <td class="font-weight-bold p-1">FACULTAD</td>
            <td class="font-weight-bold p-1">PROGRAMA</td>
            <td class="font-weight-bold p-1">MODALIDAD</td>
        </tbody>
    </table>
    <br/>
    <h6 class="text-uppercase font-weight-bold text-primary mb-3">Información académica desarrollada en la Universidad del Magdalena</h6>
    <table class="table table-responsive-md text-center">
        <thead class="bg-primary">
            <th class="text-white p-1">Código</th>
            <th class="text-white p-1">Facultad</th>
            <th class="text-white p-1">Programa</th>
            <th class="text-white p-1">Modalidad</th>
        </thead>
        <tbody>
            <td class="p-1">CODIGO</td>
            <td class="p-1">FACULTAD</td>
            <td class="p-1">PROGRAMA</td>
            <td class="p-1">MODALIDAD</td>
        </tbody>
    </table>
    <hr/>-->
    <h6 class="text-uppercase font-weight-bold text-primary mb-4">
        Información académica desarrollada en otras instituciones
    </h6>
    <button class="btn btn-primary btn-icon-split" data-toggle="modal" data-target="#modalAddInfoAcademica">
        <span class="icon text-white-50">
            <i class="fas fa-plus"></i>
        </span>
        <span class="text">Añadir</span>
    </button>
    <table class="table table-sm table-responsive-md text-center AppTable">
        <thead class="bg-primary">
            <th class="text-white p-1">Nombre</th>
            <th class="text-white p-1">Institucion</th>
            <th class="text-white p-1">Meses</th>
            <th class="text-white p-1">Graduado</th>
            <th class="text-white p-1">Acciones</th>
        </thead>
        <tbody class="app-text-black-0">
            <tr class="TableRow">
                <td>Nombre de Estudio</td>
                <td>Institución del Estudio</td>
                <td>123456</td>
                <td>
                    <span class="badge badge-success">SI</span>
                </td>
                <td>
                    <i class="far fa-edit text-warning action-btn"></i>
                    <i class="far fa-trash-alt text-danger action-btn ml-3"></i>
                </td>
            </tr>
        </tbody>
    </table>
    <modal id="modalAddInfoAcademica" title="Agregar Estudio externo" buttonText="Añadir" large>
        <div class="row">
            <div class="col-sm-5">
                <div class="form-group">
                    <label>Nombre del estudio</label>
                    <input type="text" class="form-control" placeholder="Nombre" ng-model="forms.estudio.nombre">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Institución</label>
                    <input type="text" class="form-control" placeholder="Institución" ng-model="forms.estudio.institucion">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Semestres cursados</label>
                    <input type="number" class="form-control" placeholder="Meses" ng-model="forms.estudio.duracion">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <label>¿Se graduó de este estudio?</label>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="estudio-graduado1" class="custom-control-input" ng-value="1" ng-model="forms.estudio.graduado">
                    <label class="custom-control-label" for="estudio-graduado1">Si</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="estudio-graduado2" class="custom-control-input" ng-value="0" ng-model="forms.estudio.graduado">
                    <label class="custom-control-label" for="estudio-graduado2">No</label>
                </div>
            </div>
            <div ng-if="forms.estudio.graduado==1" class="col-sm-4">
                <div class="form-group">
                    <app-input
                    v-model="input.anio_culminacion"
                    type="number"
                    label="Año de Culminación"
                    required
                    @input="errors.anio_culminacion = undefined"
                    placeholder="Año"
                    v-bind:errors="errors.anio_culminacion"/>
                </div>
            </div>
            <div ng-if="forms.estudio.graduado==1" class="col-sm-4">
                <div class="form-group">
                    <label>Mes de culminación</label>
                    <select class="form-control" ng-model="forms.estudio.mes_culminacion" ng-options="item.id as item.nombre for item in datos.meses">
                        <option value="" selected hidden>Seleccione una opción</option>
                    </select>
                </div>
            </div>
        </div>
    </modal>
</div>
@endcomponent

@push('csscomponent')
<link rel="stylesheet" href="{{ asset('css/apptable.css')}}" />
@endpush

@push('scripts')
<script type="module" src="{{ asset('js/ficha/DatosAcademicos.js') }}"></script>
@endpush
