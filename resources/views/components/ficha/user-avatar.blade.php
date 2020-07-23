@component('component', ['id' => 'user-avatar-component'])
<div class="text-center">
    <img src="{{ asset('img/sin_perfil.png') }}" alt="" class="img-fluid" v-bind:style="imgstyle">
    <br>
    <div class="mt-4" v-if="actions">
        <button class="btn btn-outline-primary btn-sm btn-circle" title="Cargar Foto" type="button">
            <i class="fas fa-upload"></i>
        </button>
        <button class="btn btn-success btn-sm btn-circle ml-3" title="Aprobar Foto" type="button">
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
<script>
    Vue.component('user-avatar', {
            template: '#user-avatar-component',
            props: {
                imgstyle: Object,
                actions: {
                    type: Boolean,
                    default: true
                }
            }
        });
</script>
@endpush
