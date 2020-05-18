@component('component', ['id' => 'card-component'])
<div class="card" v-bind:class="{'shadow': shadow}">
    <div class="card-header" v-if="title">
    <h6 class="m-0 font-weight-bold text-primary">@{{ title }}</h6>
    </div>
    <div class="card-body">
        <slot/>
    </div>
    <div class="card-footer" v-if="!!$slots.footer">
        <slot name="footer"/>
    </div>
</div>
@endcomponent

@push('scripts')
    <script>
        Vue.component('card', {
            template: '#card-component',
            props: {
                title: String,
                shadow: {
                    type: Boolean,
                    default: true
                }
            },
            methods: {

            }
        })
    </script>
@endpush
