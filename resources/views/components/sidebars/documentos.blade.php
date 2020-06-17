@component('component', ['id' => 'sidebar-documentos-component'])
<div>
    <sidebar title="DIRECCIÓN" :show="show" @onhide="$emit('hide')" primary id="sidebar-direccion">
        <div class="pl-2 pr-2">
            <div class="d-flex flex-row align-items-center justify-content-between">
                <div class="font-weight-bold">
                    Documentos
                </div>
            </div>
            <div>
                <list-group>
                    <list-group-item light :bold="false" v-for="(documento) in datos.documentos" :estado="documento.estado" :key="documento.id">
                        @{{ documento.nombre }}

                        <template v-slot:actions >
                            <span v-if="documentCanSomething(documento)">
                                <span class="text-primary action-btn mr-1"
                                    @click="verDocumento(documento)"
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
                                    @click="onClickRechazarDocumento(documento)"
                                    class="text-danger action-btn mr-1"
                                    title="Rechazar Documento">
                                    <i class="fas fa-times"></i>
                                </span>
                                <span
                                v-if="documento.can_cargar"
                                @click="form.documento = documento"
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
                    <form @submit.prevent="onSubmitInfoExtra()">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 form-group">
                                <app-input
                                    label="Código Pruebas Saber Pro"
                                    placeholder="código"
                                    v-model="form.estudiante.extra.codigo_ecaes"
                                    required
                                    :errors="errors.estudiante.extra.codigo_ecaes"
                                    @input="errors.estudiante.extra.codigo_ecaes = undefined"
                                />
                            </div>
                            <div class="col-md-12 col-sm-12 form-group">
                                <app-input
                                    label="Resultado Pruebas Saber Pro"
                                    placeholder="Resultado"
                                    v-model="form.estudiante.extra.resultado_ecaes"
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
                                    v-model="form.estudiante.extra.titulo_memoria_grado"
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
    <modal
        id="modalRechazarDocumento"
        title="Rechazar Documento Estudiante"
        @submit="estadoDocumento( 'rechazar' , form.documento.id, form.documento.motivo)"
        large
        buttontext="Enviar">
        <div class="form-group">
            <app-input
                label="Motivo"
                required
                placeholder="Motivo"
                v-model="form.documento.motivo"
                type="textarea"
            />
        </div>
    </modal>

    <modal id="modalNoAprobarEstudiante" title="Motivo de Rechazo" :onsubmit="rechazarEstudiante" >
        <app-input
            label="Motivo"
            required
            class="form-group"
            type="textarea"
            v-model="form.estudiante.motivo"
            :errors="errors.estudiante.motivo"
            @input="errors.estudiante.motivo = undefined"
        />
    </modal>

    <carga-documento-modal id="cargaDocumentoModal" :documento="form.documento"
        v-on:documento-cargado="onDocumentoCargado">
    </carga-documento-modal>

    <modal-ver-documento
        :documento="form.documento"
        v-if="form.documento"
        @aprobar="estadoDocumento('aprobar', form.documento.id)"
        @rechazar="onClickRechazarDocumento(form.documento)">
    </modal-ver-documento>
</div>
@endcomponent

@push('scripts')
<script type="module" src="{{ asset('js/components/sidebars/documentos.js') }}"></script>
@endpush
