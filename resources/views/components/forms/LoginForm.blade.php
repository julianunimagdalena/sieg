@component('component', ['id' => 'login-form'])
<div>
    <div class="alert alert-info">
        <strong>Inicie sesión con su cuenta institucional.</strong>
    </div>
    <form @submit.prevent="handleSubmit">
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Usuario" v-model="input.username"
                @input="errors.username=undefined" />
            <small class="text-danger" v-if="errors.username">@{{errors.username[0]}}</small>
        </div>
        <div class="form-group">
            <input type="password" class="form-control" placeholder="Contraseña" v-model="input.password"
                @input="errors.password=undefined" />
            <small class="text-danger" v-if="errors.password">@{{errors.password[0]}}</small>
        </div>
        <div class="form-group" v-if="roles.length > 0">
            <label>Seleccione un rol:</label>
            <select class="form-control" v-model="input.local_id" @input="errors.rol_id=undefined">
                <option :value="undefined" hidden selected>Seleccione una opción</option>
                <option v-for="item in roles" :key="item.id" :value="item.local_id">@{{item.nombre}}</option>
            </select>
            <small class="text-danger" v-if="errors.rol_id">@{{errors.rol_id[0]}}</small>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-success" :disabled="cargando">
                <i class="fas fa-sign-in-alt"></i>&nbsp;Iniciar sesión
            </button>
        </div>
    </form>
</div>
@endcomponent

@push('scripts')
<script type="module" src="{{asset('js/LoginForm.js')}}"></script>
@endpush
