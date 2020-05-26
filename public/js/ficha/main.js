import http from '../http.js';


new Vue({
    el: '#app',
    data: () => ({
        progress: 0,
        active:{
            datos_basicos: true,
            datos_academicos: false,
            hoja_de_vida: false,
            datos_laborales: false,
        }
    }),
    methods: {
        setActiveTab(tab){
            sessionStorage.setItem('active', tab);
            for(let key in this.active)
            {
                this.active[key] = key === tab;
            }
        },
        updateProgreso()
        {
            http.get('egresado/progreso-ficha').then(
                ({ data }) =>
                {
                    this.progress = data;
                }
            );
        }
    },
    mounted: function()
    {
        let tabActive = sessionStorage.getItem('active');

        if(!tabActive)
        {
            sessionStorage.setItem('active', 'datos_basicos');
        }else
            this.setActiveTab(tabActive);



        this.updateProgreso();


        $(document).on('submit', ( ) =>
        {
            setTimeout(this.updateProgreso, 150);
        });


    },
    beforeUpdate: function ()
    {

    }
})
