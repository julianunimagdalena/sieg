@component('component', ['id' => 'list-group-component'])
<ul class="list-group" :class="{'list-group-flush': flush}">
    <slot/>
</ul>
@endcomponent
<!-- d-flex justify-content-between align-items-center -->
@component('component', ['id' => 'list-group-item-component'])
<li class="list-group-item" :class="{'': actions, 'list-group-item-light': light, 'list-group-item-sm': sm, ...c_estado}" >
    <span class="app-text-black-1 text-truncate" v-bind:class="{'font-weight-bold': bold}"><slot/></span>
    <span v-if="actions || !!$slots.actions" class="float-right">
        <slot name="actions"></slot>
        <i
            v-if="this.$listeners.onedit"
            @click="$emit('onedit')"
            class="fas fa-pencil-alt icon-escritura mr-1 action-btn" />
        <i
            v-if="this.$listeners.ondelete"
            @click="$emit('ondelete')"
            class="fas fa-times-circle text-danger action-btn"></i>
    </span>
    <slot name="aditionals"></slot>
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
        estado: String,
        sm: Boolean
    };

    const list_group_item_data = () => ({
        c_estado: {}
    });


    Vue.component('list-group-item', {
        template: '#list-group-item-component',
        props: list_group_item_props,
        data: list_group_item_data,
        methods: {
            initEstado()
            {
                if(this.estado)
                {
                    this.c_estado = {'border-estado-radius': true ,...getEstados(this.estado, 'border-left-secondary', 'border-left-warning', 'border-left-success', 'border-left-danger')};
                }
            }
        },
        watch: {
            estado()
            {
                this.initEstado();
            }
        },
        mounted()
        {
            this.initEstado();
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
