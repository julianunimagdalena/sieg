@push('csscomponent')
<link rel="stylesheet" href="{{ asset('css/data-list-sidebar.css')}}" />
<link rel="stylesheet" href="{{ asset('css/perfect-scrollbar.css')}}" />
@endpush

@component('component', ['id' => 'sidebar-component'])
<div>
    <div class="data-list-sidebar" :class="{'show': c_show}">
        <div class="data-list-sidebar-header pt-3 d-flex justify-content-between app-text-black-1" :class="{'bg-primary text-white': primary}">
            <h4 class="px-3" v-if="title">@{{ title }}</h4>
            <slot name="header"></slot>
            <span @click="close()" class="action-btn">
                <svg  xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-4 fonticon-wrap"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </span>
        </div>
        <div class="scrollbar-container data-list-fields px-2 mt-3 ps ps--active-y container-sidebar" :id="id">
            <slot></slot>
        </div>
        <div class="data-list-sidebar-footer px-4 d-flex justify-content-between align-items-center mt-3">
            <slot name="footer"></slot>
        </div>
    </div>
    <div class="data-list-overlay" :class="{'show': c_show}" @click="close()"></div>
</div>
@endcomponent

@push('scripts')
<script  src="{{ asset('js/dist/perfect-scrollbar.min.js') }}"></script>
<script type="module">
    Vue.component('sidebar', {
        template: '#sidebar-component',
        data: () => ({
            c_show: false
        }),
        props: {
            show: Boolean,
            title: String,
            primary: Boolean,
            id: String
        },
        watch:{
            show()
            {
                this.c_show = this.show;
                if(this.show)
                {
                    document.onkeydown = evt => {
                        evt = evt || window.event;
                        if (evt.keyCode == 27) {
                            this.close();
                        }
                    };
                }else
                    document.onkeydown = null;
            }
        },
        created()
        {
            this.c_show = this.show;
        },
        methods: {
            close()
            {
                this.c_show = false;
                this.$emit('onhide');
            }
        },
        mounted()
        {
            const ps = new PerfectScrollbar('#'+this.id);
        }
    });
</script>
@endpush
