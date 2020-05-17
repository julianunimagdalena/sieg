Vue.component('modal', {
    template: "#modal",
    props: {
        id: String,
        title: {
            type: String,
            default: ""
        },
        onSubmit: Function,
        buttonText: {
            type: String,
            default: "Guardar"
        },
        buttonDisabled: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        submit() {
            let fn = new Function();
            if (this.onSubmit) fn = this.onSubmit;

            return fn;
        }
    }
});
