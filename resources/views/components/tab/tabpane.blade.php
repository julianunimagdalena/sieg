@component('component', ['id' => 'tab-pane-component'])
    <div class="tab-pane" :id="id" v-bind:class="{'show active': active}" :aria-labelledby="id + '-tab' " role="tabpanel">
        <slot/>
    </div>
@endcomponent

@push('scripts')
    <script type="module">
        Vue.component('tab-pane', {
            template: '#tab-pane-component',
            props: {
                id: String,
                active: Boolean,
            }
        })
    </script>
@endpush
