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
<script type="module">
    Vue.component('modal', {
        template: "#modal",
        props: {
            id: String,
            title: {
                type: String,
                default: ""
            },
            onSubmit: Function,
            buttonText: {
                type: String,
                default: "Guardar"
            },
            buttonDisabled: {
                type: Boolean,
                default: false
            },
            large: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            submit() {
                let fn = new Function();
                if (this.onSubmit) fn = this.onSubmit;

                return fn;
            }
        },
        mounted: function ()
        {
            $('#'+this.id).on('hidden.bs.modal', function () {
                this.$emit('onHide');
            }.bind(this));
        }
    });
</script>
@endpush
