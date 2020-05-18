@component('component', ['id' => 'modal'])
<div class="modal fade" :id="id" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" v-bind:class="{'modal-lg': large}" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary" v-if="title">
                <h5 class="modal-title" style="color: #fff; text-transform: uppercase">@{{title}}</h5>
            </div>
            <form @submit.prevent="submit">
                <div class="modal-body">
                    <slot />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" v-if="onSubmit"
                        :disabled="buttonDisabled">@{{buttonText}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcomponent

@push('scripts')
<script type="module" src="{{asset('js/modal.js')}}"></script>
@endpush
