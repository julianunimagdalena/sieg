@component('component', ['id' => 'sidebar-proceso-grado-component'])
<sidebar title="ESTUDIANTE" :show="show" @onhide="$emit('hide')" primary id="sidebar-estudiante">
    <div class="app-text-black-1" v-if="estudiante">
        <div class="text-center">
            <img :src="estudiante.info.foto" alt="" class="img-fluid data-list-img">
            <div class="font-weight-bold mt-1">@{{ estudiante.info.nombre }}</div>
            <div class="mt-2">@{{ estudiante.info.codigo}}</div>
            <div class="">@{{ estudiante.info.programa}}</div>
        </div>
        <hr/>
        <div class="pl-2 pr-2">
            <div class="d-flex flex-row align-items-center justify-content-between">
                <div class="font-weight-bold">
                    Proceso
                </div>
            </div>
            <div class="mt-1">
                <list-group flush>
                    <list-group-item-flex light md v-for="(item) in estudiante.proceso" :key="item.proceso" >
                        <div class="">
                            @{{ item.proceso }}
                        </div>
                        <div class="ml-2">
                            <icono-estado :estado="item.estado"></icono-estado>

                        </div>
                    </list-group-item-flex>
                </list-group>
            </div>
        </div>
        <hr/>
        <div class="pl-2 pr-2">
            <div class="font-weight-bold">
                Paz y Salvos
            </div>
            <div>
                <list-group flush>
                    <list-group-item-flex light sm v-for="(item) in estudiante.paz_salvos" :key="item.nombre" :bold="false">
                        <div class="text-initial">@{{ item.nombre }}</div>
                        <div>
                            <icono-estado :estado="item.estado"></icono-estado>
                        </div>
                    </list-group-item-flex>
                </list-group>
            </div>
        </div>
    </div>
</sidebar>
@endcomponent

@push('scripts')
<script type="module" src="{{ asset('js/components/sidebars/proceso_grado.js') }}"></script>
@endpush
