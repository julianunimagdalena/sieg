@component('component', ['id' => 'user-avatar-component'])
<div class="text-center">
    <img :src="foto || '{{ asset('img/sin_perfil.png') }}' " alt="" class="img-fluid" v-bind:style="imgstyle">
    <br>
    <input type="file" id="foto-input" accept="image/*" @change="onChangeFoto($event.target.files)"
        style="display: none;" />
    <div class="mt-4" v-if="actions && !foto_aprobada">
        <button class="btn btn-outline-primary btn-sm btn-circle" @click="onClickUpload()" title="Cargar Foto"
            type="button">
            <i class="fas fa-upload"></i>
        </button>
        <button class="btn btn-success btn-sm btn-circle ml-3" :disabled="!valid" @click="onclickAprobar()"
            title="Aprobar Foto" type="button">
            <i class="fas fa-check"></i>
        </button>
    </div>
    <!--<div class="btn-group-vertical" style="width:100%;" role="group" aria-label="...">
        <button type="button" class="btn btn-light btn-block btn-sm" title="Actualizar foto">
            <i class="fa fas-pencil"></i> Actualizar
        </button>
        <button type="button" class="btn btn-success btn-block btn-sm" title="Aprobar foto">
            <i class="fa fa-plus"></i> &nbsp;Aprobar
        </button>
    </div>-->
</div>
@endcomponent

@push('scripts')
<script type="module" src="{{ asset('js/ficha/UserAvatar.js') }}"></script>
@endpush
