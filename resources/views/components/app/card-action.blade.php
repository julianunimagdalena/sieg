@component('component', ['id' => 'card-action-component'])
<div class="card shadow h-100 py-2" v-bind:class="{'border-left-primary': border}">
    <div
        class="card-header"
        v-bind:class="{'py-3 d-flex flex-row align-items-center justify-content-between': hflex}"
        style="background-color: white !important;"
        v-if="title">
        <h6 class="m-0 font-weight-bold text-primary">@{{ title }}</h6>

        <slot name="actions" />
        <div v-if="!$slots.actions">
            <i
            v-if="this.$listeners.onadd"
            class="text-primary fas fa-plus-circle mr-1 action-btn"
            title="Añadir" @click="$emit('onadd')"></i>
        </div>
        <slot name="head"></slot>
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
                },
                hflex: {
                    type: Boolean,
                    default: true
                },
                border: {
                    type: Boolean,
                    default: true
                }
            },
            methods: {

            },
            mounted: function(){
            }
        })
    </script>
@endpush
