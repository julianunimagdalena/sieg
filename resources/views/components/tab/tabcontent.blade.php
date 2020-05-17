@component('component', ['id' => 'tab-content-component'])
    <div class="tab-content">
        <slot></slot>
    </div>
@endcomponent

@push('scripts')
    <script type="module">
        Vue.component('tab-content', { template: '#tab-content-component'})
    </script>
@endpush
