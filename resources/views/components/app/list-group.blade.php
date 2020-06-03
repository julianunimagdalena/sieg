@component('component', ['id' => 'list-group-component'])
<ul class="list-group" :class="{'list-group-flush': flush}">
    <slot/>
</ul>
@endcomponent
<!-- d-flex justify-content-between align-items-center -->
@component('component', ['id' => 'list-group-item-component'])
<li class="list-group-item" :class="{'': actions, 'list-group-item-light': light, 'list-group-item-sm': sm}" >
    <span class="app-text-black-1 text-truncate" v-bind:class="{'font-weight-bold': bold}"><slot/></span>
    <span v-if="actions || !!$slots.actions" class="float-right">
        <slot name="actions"></slot>
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
@component('component', ['id' => 'list-group-item-flex-component'])
<li class="list-group-item app-text-black-1 d-flex justify-content-between align-items-center"
:class="{'': actions, 'list-group-item-light': light, 'list-group-item-flex-sm': sm, 'list-group-item-flex-md': md}" >
    <slot></slot>
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
    });

    const list_group_item_props = {
        actions: Boolean,
        light: Boolean,
        bold: {
            type: Boolean,
            default: true
        },
        sm: Boolean
    };

    Vue.component('list-group-item', {
        template: '#list-group-item-component',
        props: list_group_item_props,
        methods: {

        }
    });

    Vue.component('list-group-item-flex', {
        template: '#list-group-item-flex-component',
        props: {
            ...list_group_item_props,
            md: Boolean
        }
    });
</script>
@endpush
