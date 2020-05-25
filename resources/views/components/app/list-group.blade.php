@component('component', ['id' => 'list-group-component'])
<ul class="list-group" :class="{'list-group-flush': flush}">
    <slot/>
</ul>
@endcomponent
<!-- d-flex justify-content-between align-items-center -->
@component('component', ['id' => 'list-group-item-component'])
<li class="list-group-item" :class="{'': actions, 'list-group-item-light': light}" >
    <span class="font-weight-bold app-text-black-1"><slot/></span>
    <span v-if="actions" class="float-right">
        <i
            v-if="this.$listeners.onEdit"
            @click="$emit('onEdit')"
            class="fas fa-pencil-alt icon-escritura mr-1 action-btn" />
        <i
            v-if="this.$listeners.onDelete"
            @click="$emit('onDelete')"
            class="fas fa-times-circle text-danger action-btn"></i>
    </span>
</li>
@endcomponent

@push('scripts')
<script type="module">
    Vue.component('list-group', {
            template: '#list-group-component',
            props: {
                flush: Boolean
            },
            methods: {

            }
    })

    Vue.component('list-group-item', {
            template: '#list-group-item-component',
            props: {
                actions: Boolean,
                light: Boolean
            },
            methods: {

            }
    })
</script>
@endpush
