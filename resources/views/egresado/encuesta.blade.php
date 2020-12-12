@extends('layouts.principal')
@section('title', 'ENCUESTA')

@push('csscomponent')
<style>
    .pregunta {
        text-align: justify;
        text-justify: inter-word;
    }

    /*.custom-control-label::before {
        left: -0.88rem !important;
    }*/
</style>

@endpush

@push('components')
@include('components.app.Titulo')
@include('components.app.card-action')
@endpush

@section('content')
<titulo>ENCUESTA</titulo>
<input type="hidden" id="tipo_encuesta" value="{{ $key }}">
<div class="text-center mt-3" v-if="diligenciada">
    <h4 class="text-muted">La encuesta ya ha sido enviada</h4>
</div>
<div class="container mt-3" v-else>
    <div v-for="(modulo) in modulos" class="mb-3">
        <card-action :title="modulo.titulo">
            <div class="text-muted font-weight-bold">@{{ modulo.descripcion }}</div>
            <hr />
            <div v-for="(pregunta) in modulo.preguntas" :id="'pregunta-'+pregunta.id" class="mb-3">
                <div v-bind:class="{'text-muted': preguntas_d[pregunta.id]}">
                    <span class="font-weight-bold">@{{ pregunta.orden }}</span> -
                    <span class="pregunta">@{{ pregunta.text }} <small class="text-danger ml-1"
                            v-if="pregunta.obligatoria || respuesta_encuesta[pregunta.id].obligatoria">*</small></span>
                </div>
                <div class="mt-3 container" v-if="!preguntas_d[pregunta.id]">
                    <div v-if="pregunta.preguntas.length > 0">
                        <table class="table table-bordered">
                            <thead class="thead-light table-sm text-center">
                                <tr>
                                    <th> - </th>
                                    <th v-for="(respuesta) in pregunta.respuestas">
                                        @{{ respuesta.valor  }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-left">
                                <tr v-for="(subpr) in pregunta.preguntas" :id="'pregunta-'+subpr.id">
                                    <td>@{{ subpr.text }} <small class="text-danger ml-1"
                                            v-if="subpr.obligatoria">*</small></td>
                                    <td v-for="(respuesta, i) in pregunta.respuestas">
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" :id="'radio-'+subpr.id+'-'+i" :name="'radio-'+subpr.id"
                                                :value="respuesta.id" @change="onChangeRespuesta(subpr.id, respuesta)"
                                                class="custom-control-input" :value="respuesta.id">
                                            <label class="custom-control-label text-center"
                                                :for="'radio-'+subpr.id+'-'+i" style="right: -0.8rem;"></label>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else-if="!pregunta.abierta">
                        <div v-if="!pregunta.multiple">
                            <div class="custom-control custom-radio" v-for="(respuesta, i) in pregunta.respuestas">
                                <input type="radio" :id="'radio-resp-'+respuesta.id" :name="'radio-'+pregunta.id"
                                    class="custom-control-input" :value="respuesta.id"
                                    @change="onChangeRespuesta(pregunta.id, respuesta)">
                                <label class="custom-control-label"
                                    :for="'radio-resp-'+respuesta.id">@{{respuesta.valor}}</label>
                                <input
                                    v-if="respuesta.abierta == true && respuesta_encuesta[pregunta.id].respuesta_id == respuesta.id"
                                    class="form-control ml-2" v-model="respuesta_encuesta[pregunta.id].texto" />
                            </div>
                        </div>
                        <div v-else>
                            <div class="custom-control custom-checkbox" v-for="(respuesta, i) in pregunta.respuestas">
                                <input type="checkbox" class="custom-control-input"
                                    v-model="respuesta_encuesta[pregunta.id].multiple" :value="respuesta.valor"
                                    :id="'check-resp-'+respuesta.id" :name="'check-'+pregunta.id"
                                    @change="onChangeMultiple(pregunta.id, respuesta)" />
                                <label class="custom-control-label"
                                    :for="'check-resp-'+respuesta.id">@{{respuesta.valor}}
                                    <span> <small class="text-muted"> - Pase a pregunta
                                            @{{ respuesta_encuesta[respuesta.to_pregunta].orden }}</small></span></label>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <textarea v-model="respuesta_encuesta[pregunta.id].texto" class="form-control"
                            :placeholder="'Respuesta a pregunta '+pregunta.orden">

                        </textarea>
                    </div>
                </div>
                <!--<div> RP: @{{ respuesta_encuesta[pregunta.id] }}</div>-->
                <hr />
            </div>
        </card-action>
    </div>
    <div class="mt-3 mb-3 text-center">
        <button class="btn btn-primary" @click="enviarEncuesta()">
            Enviar Encuesta
        </button>
    </div>

</div>
@endsection

@push('scripts')
<script type="module" src="{{asset('js/egresado/encuesta.js')}}"></script>
@endpush
