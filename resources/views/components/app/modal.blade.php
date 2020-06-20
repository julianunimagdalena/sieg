@component('component', ['id' => 'modal'])
<div class="modal fade" :id="id" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" :id="'dialog-'+id" v-bind:class="{'modal-lg': large}" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary" v-if="title">
                <h5 class="modal-title" style="color: #fff; text-transform: uppercase">@{{title}}</h5>
                <button type="button" class="close" style="color: #fff;" data-dismiss="modal" aria-label="Close" v-if="iconclose">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form @submit.prevent="submit" :id="'form-'+id">
                <div class="modal-body" :id="'body-'+id">
                    <slot />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" v-if="onsubmit || this.$listeners.submit"
                        :disabled="buttonDisabled">@{{buttontext}}</button>
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
            iconclose: {
                type:Boolean,
                default: true
            },
            onsubmit: Function,
            buttontext: {
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
                if (this.onsubmit) fn = this.onsubmit;
                this.$emit('submit');
                return fn;
            }
        },
        mounted: function ()
        {
            $('#'+this.id).on('show.bs.modal', () => this.$emit('show'));
            $('#'+this.id).on('hidden.bs.modal', function () {
                //this.$emit('onHide');
                this.$emit('onhide');
            }.bind(this));
        }
    });
</script>
@endpush
