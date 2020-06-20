@push('csscomponent')
<style>
    .icon-habla {
        color: #27ae60;
    }

    .icon-escritura {
        color: #f39c12;
    }

    .icon-lectura {
        color: #2980b9;
    }

    .app-text-md {
        font-size: 0.8rem;
    }

    span.Alto {
        color: #558B2F;
	}

    span.Intermedio.alto {
        color: #7CB342;
    }

    span.Intermedio {
        color: #1565C0;
    }

    span.Basico {
        color: #0097A7;
    }

    span.Bajo {
        color: #EF6C00;
    }
</style>
@endpush


@component('component', ['id' => 'hoja-de-vida-component'])
<div>
    <div class="row">
        <div class="col col-md-8 col-sm-12" id="perfil-profesional-form">
            <card title="Perfil Profesional">
                <form @submit.prevent="onSubmitPerfil">
                    <div class="form-group">
                        <textarea class="form-control" placeholder="Ingrese su perfil profesional"
                             v-model="forms.perfil.perfil" style="min-height: 150px;"></textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary float-right" type="submit">Guardar</button>
                    </div>
                </form>
            </card>
        </div>
        <div class="col col-md-4 col-sm-12" >
            <card-action title="Información de Idiomas" id="info-idiomas-form" @onAdd="openModal('modal-idioma')" fluid>
                <list-group flush>
                    <list-group-item v-for="(idioma) in datos.user_idiomas" actions light v-bind:key="idioma.id"
                        @onEdit="editItem('idioma',idioma)" @onDelete="deleteItem('eliminar-idioma',idioma, initUserIdiomas)">
                        <span class="app-text-md">
                            <span class="mr-3 font-weight-bold">
                                @{{ idioma.resolve.idioma }}
                            </span>
                            <span class="mr-3">
                                <i class="fas fa-comment icon-habla "></i>
                                <span :class="idioma.resolve.nivel_habla" class="ml-1 text-capitalize">@{{idioma.resolve.nivel_habla | shortText}}</span>
                            </span>
                            <span class="mr-3">
                                <i class="fas fa-edit icon-escritura"></i>
                                <span :class="idioma.resolve.nivel_escritura" class="ml-1 text-capitalize">@{{ idioma.resolve.nivel_escritura | shortText }}</span>
                            </span>
                            <span>
                                <i class="fas fa-book-open icon-lectura "></i>
                                <span :class="idioma.resolve.nivel_lectura" class="ml-1 text-capitalize">@{{ idioma.resolve.nivel_lectura | shortText }}</span>
                            </span>
                        </span>
                    </list-group-item>
                </list-group>
            </card-action>
        </div>
        <hr />
    </div>
    <div class="mb-4 mt-4">
        <div class="row">
            <div class="col col-md-3 col-sm-12">
                <card-action title="Distinciones" @onAdd="openModal('modal-distincion')" fluid id="distinciones-form">
                    <list-group flush>
                        <list-group-item v-for="(dist) in datos.distinciones" :key="dist.id"
                            actions
                            light
                            @onEdit="editItem('distincion',dist)"
                            @onDelete="deleteItem('eliminar-distincion',dist, initDistinciones)">
                            @{{ dist.nombre }}
                        </list-group-item>
                    </list-group>
                </card-action>
            </div>
            <div class="col col-md-3 col-sm-12">
                <card-action title="Información de Asociaciones" @onAdd="openModal('modal-asociacion')" fluid id="asociaciones-form">
                    <list-group flush>
                        <list-group-item v-for="(aso) in datos.asociaciones" :key="aso.id"
                            actions
                            light
                            @onEdit="editItem('asociacion',aso)"
                            @onDelete="deleteItem('eliminar-asociacion',aso, initAsociaciones)">
                            @{{ aso.nombre }}
                        </list-group-item>
                    </list-group>
                </card-action>
            </div>
            <div class="col col-md-3 col-sm-12">
                <card-action title="Consejos Profesionales" @onAdd="openModal('modal-consejo')" fluid id="consejos-form">
                    <list-group flush>
                        <list-group-item v-for="(con) in datos.consejos" :key="con.id"
                            actions
                            light
                            @onDelete="deleteItem('eliminar-concejo',con, initConsejos)">
                            @{{ con.nombre }}
                        </list-group-item>
                    </list-group>
                </card-action>
            </div>
            <div class="col col-md-3 col-sm-12">
                <card-action title="Información de discapacidades" @onAdd="openModal('modal-discapacidad')" fluid id="discapacidad-form">
                    <list-group flush>
                        <list-group-item v-for="(disc) in datos.discapacidades" :key="disc.id"
                            actions
                            light
                            @onDelete="deleteItem('eliminar-discapacidad',disc, initDiscapacidades)">
                            @{{ disc.nombre }}
                        </list-group-item>
                    </list-group>
                </card-action>
            </div>

        </div>
    </div>

    <modal title="Añadir Idioma" id="modal-idioma" large :onSubmit="onSubmitLanguaje" @onhide="forms.idioma = {}">
        <div class="row">
            <div class="col col-md-6">
                <div class="form-group">
                    <label>Idioma</label>
                    <select class="form-control" v-model="forms.idioma.idioma_id" @input="errors.idioma.idioma_id = undefined">
                        <option :value="undefined" selected hidden>Seleccione una opción</option>
                        <option v-for="(idioma) in datos.idiomas" :value="idioma.id">@{{idioma.nombre}}</option>
                    </select>
                    <small class="text-danger" v-if="errors.idioma.idioma_id">@{{ errors.idioma.idioma_id[0] }}</small>
                </div>
            </div>
            <div class="col col-md-6">
                <div class="form-group">
                    <label>Nivel de habla</label>
                    <i class="fas fa-comment icon-habla "></i>
                    <select class="form-control" v-model="forms.idioma.nivel_habla_id" @input="errors.idioma.nivel_habla_id = undefined">
                        <option :value="undefined" selected hidden>Seleccione una opción</option>
                        <option v-for="(nivel) in datos.niveles" :value="nivel.id">@{{ nivel.nombre }}</option>
                    </select>
                    <small class="text-danger" v-if="errors.idioma.nivel_habla_id">@{{ errors.idioma.nivel_habla_id[0] }}</small>
                </div>
            </div>
            <div class="col col-sm-6">
                <div class="form-group">
                    <label>Nivel de escritura</label>
                    <i class="fas fa-pencil-alt icon-escritura "></i>
                    <select class="form-control" v-model="forms.idioma.nivel_escritura_id" @input="errors.idioma.nivel_lectura_id = undefined">
                        <option :value="undefined" selected hidden>Seleccione una opción</option>
                        <option v-for="(nivel) in datos.niveles" :value="nivel.id">@{{ nivel.nombre }}</option>
                    </select>
                    <small class="text-danger" v-if="errors.idioma.nivel_escritura_id">@{{ errors.idioma.nivel_lectura_id[0] }}</small>
                </div>
            </div>
            <div class="col col-sm-6">
                <div class="form-group">
                    <label>Nivel de lectura</label>
                    <i class="fas fa-book-open icon-lectura "></i>
                    <select class="form-control" v-model="forms.idioma.nivel_lectura_id" @input="errors.idioma.nivel_escritura_id = undefined">
                        <option :value="undefined" selected hidden>Seleccione una opción</option>
                        <option v-for="(nivel) in datos.niveles" :value="nivel.id">@{{ nivel.nombre }}</option>
                    </select>
                    <small class="text-danger" v-if="errors.idioma.nivel_lectura_id">@{{ errors.idioma.nivel_escritura_id[0] }}</small>
                </div>
            </div>
        </div>
    </modal>

    <modal title="Añadir Distinción" id="modal-distincion" :onSubmit="onSubmitDistincion" @onhide="forms.distincion = {}">
        <div class="row">
            <div class="col col-md-8 offset-md-2">
                <small class="text-secondary"><app-required>Si usted no tiene por favor colocar "ninguna"</app-required></small>
                <div class="form-group">
                    <app-input
                        label="Distinción"
                        v-model="forms.distincion.nombre"
                        :errors="errors.distincion.nombre"
                        @errors="errors.distincion.nombre = undefined" />
                </div>
            </div>
        </div>
    </modal>

    <modal title="Añadir Información de Asociación" id="modal-asociacion" :onSubmit="onSubmitAsociacion" @onhide="forms.asociacion = {}">
        <div class="row">
            <div class="col col-md-8 offset-md-2">
                <small class="text-secondary"><app-required>Si usted no tiene por favor colocar "ninguna"</app-required></small>
                <div class="form-group">
                    <app-input
                        label="Asociación"
                        v-model="forms.asociacion.nombre"
                        :errors="errors.asociacion.nombre"
                        @input="errors.asociacion.nombre = undefined" />
                </div>
            </div>
        </div>
    </modal>

    <modal title="Añadir Consejo Profesional" id="modal-consejo" :onSubmit="onSubmitConsejo" @onhide="forms.consejo = {}">
        <div class="row">
            <div class="col col-md-8 offset-md-2">
                <div class="form-group">
                    <app-select label="Consejo Profesional" v-model="forms.consejo.id" required>
                        <option v-for="(consejo) in datos.t_consejos" :value="consejo.id">@{{ consejo.nombre }}</option>
                    </app-select>
                </div>
            </div>
        </div>
    </modal>

        <modal title="Añadir Dispacidad" id="modal-discapacidad" :onSubmit="onSubmitDiscapacidad" @onhide="forms.discapacidad = {}">
        <div class="row">
            <div class="col col-md-8 offset-md-2">
                <div class="form-group">
                    <app-select label="Discapacidad" v-model="forms.discapacidad.discapacidad_id" required
                        :errors="errors.discapacidad.discapacidad_id"
                        @input="errors.discapacidad.discapacidad_id = undefined">
                        <option v-for="(disc) in datos.t_discapacidades" :value="disc.id">@{{ disc.nombre }}</option>
                    </app-select>
                </div>
            </div>
        </div>
    </modal>



</div>
@endcomponent



@push('scripts')

<script>
    Vue.filter('shortText', function (value) {
        if (!value) return '';
        value = value.split(' ');

        if(value.length === 1)
            return value[0];

        return value[0] + ' ' + value[1][0];
    })
</script>

<script type="module" src="{{ asset('js/ficha/HojaDeVida.js') }}"></script>
@endpush
