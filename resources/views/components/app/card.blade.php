@component('component', ['id' => 'card-component'])
<div :class="['card', shadow ? 'shadow' : '', color ? 'border-left-' + color : '']">
    <div class="card-header" v-if="title">
        <h6 class="m-0 font-weight-bold text-primary">@{{ title }}</h6>
    </div>
    <div class="card-body">
        <slot />
    </div>
    <div class="card-footer" v-if="!!$slots.footer">
        <slot name="footer" />
    </div>
</div>
@endcomponent

@push('scripts')
<script type="module">
    Vue.component('card', {
            template: '#card-component',
            props: {
                title: String,
                shadow: {
                    type: Boolean,
                    default: true
                },
                color: String
            },
            methods: {

            }
        })
    </script>
@endpush
