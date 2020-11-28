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
<div class="container mt-3">
    <div v-for="(modulo) in modulos" class="mb-3">
        <card-action :title="modulo.titulo">
            <div class="text-muted font-weight-bold">@{{ modulo.descripcion }}</div>
            <hr />
            <div v-for="(pregunta) in modulo.preguntas" class="mb-3">
                <div>
                    <span class="font-weight-bold">@{{ pregunta.orden }}</span> -
                    <span class="pregunta">@{{ pregunta.text }}</span>
                </div>
                <div class="mt-3 container">
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
                                <tr v-for="(subpr) in pregunta.preguntas">
                                    <td>@{{ subpr.text }}</td>
                                    <td v-for="(respuesta, i) in pregunta.respuestas">
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" :id="'radio-'+subpr.id+'-'+i" :name="'radio-'+subpr.id"
                                                :value="respuesta.id"
                                                v-model="respuesta_encuesta[pregunta.id].respuesta_id"
                                                @change="onChangeRespuesta(pregunta.id, respuesta)"
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
                        <div class="custom-control custom-radio" v-for="(respuesta, i) in pregunta.respuestas">
                            <input type="radio" :id="'radio-resp-'+respuesta.id" :name="'radio-'+pregunta.id"
                                class="custom-control-input" :value="respuesta.id"
                                v-model="respuesta_encuesta[pregunta.id].respuesta_id"
                                @change="onChangeRespuesta(pregunta.id, respuesta)">
                            <label class="custom-control-label"
                                :for="'radio-resp-'+respuesta.id">@{{respuesta.valor}}</label>
                            <input
                                v-if="respuesta.abierta == true && respuesta_encuesta[pregunta.id].respuesta_id == respuesta.id"
                                class="form-control ml-2" v-model="respuesta_encuesta[pregunta.id].texto" />
                        </div>
                    </div>
                    <div v-else>
                        <textarea class="form-control" :placeholder="'Respuesta a pregunta '+pregunta.orden">

                        </textarea>
                    </div>
                </div>
                <hr />
            </div>
        </card-action>
    </div>
    <div class="mt-3 text-center">
        <button class="btn btn-primary" @click="enviarEncuesta()">
            Enviar Encuesta
        </button>
    </div>

</div>
@endsection

@push('scripts')
<script type="module" src="{{asset('js/egresado/encuesta.js')}}"></script>
@endpush
