@component('component', ['id' => 'card-action-component'])
<div class="card border-left-primary shadow h-100 py-2">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background-color: white !important;">
        <h6 class="m-0 font-weight-bold text-primary">@{{ title }}</h6>

        <slot name="actions" />
        <div v-if="!$slots.actions">
            <i
            v-if="this.$listeners.onAdd"
            class="text-primary fas fa-plus-circle mr-1 action-btn"
            title="Añadir" @click="$emit('onAdd')"></i>
        </div>
    </div>
    <div  :class="{'card-body': !fluid}" class="d-flex flex-column justify-content-between">
        <slot />
    </div>
</div>
@endcomponent

@push('scripts')
<script type="module">
    Vue.component('card-action', {
            template: '#card-action-component',
            props: {
                title: String,
                fluid: {
                    type: Boolean,
                    default: false
                }
            },
            methods: {

            },
            mounted: function(){
            }
        })
    </script>
@endpush
