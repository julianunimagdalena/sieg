<template>
    <div>
        <h3 class="titulo text-primary">Solicitudes de grado pendientes</h3>
        <p>A continuación los estudiantes que hicieron una solicitud de grado a su programa:</p>
        <Table class="table table-sm table-hover">
            <thead class="bg-primary">
                <tr>
                    <th>Fecha</th>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Fecha de grado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in solicitudes" :key="item.id">
                    <td>{{item.fecha}}</td>
                    <td>{{item.codigo_estudiante}}</td>
                    <td>{{item.nombre_estudiante}}</td>
                    <td>{{item.fecha_grado.nombre}}</td>
                    <td>
                        <a href="#" @click.prevent="activarEstudiante(item)">
                            <i class="fas fa-plus text-success" title="Activar estudiante"></i>
                        </a>
                        <i class="fas fa-times text-danger" title="Rechazar solicitud"></i>
                    </td>
                </tr>
            </tbody>
        </Table>
    </div>
</template>

<script>
//@ts-ignore
import Table from "./tables/Table.vue";
import swal from "sweetalert";

import axios from "../tools/http";

export default {
    data: () => ({
        solicitudes: []
    }),
    components: { Table },
    created() {
        this.fetchSolicitudes();
    },
    methods: {
        async fetchSolicitudes() {
            const { data } = await axios.get("solicitud-grado/pendientes");
            this.solicitudes = data;
        },
        activarEstudiante(solicitud) {
            swal(
                "¡Atención!",
                "¿Desea activar a " + solicitud.nombre_estudiante + "?",
                "warning",
                //@ts-ignore
                {
                    buttons: {
                        cancel: "Cancelar",
                        activar: {
                            text: "Activar",
                            closeModal: false
                        }
                    },
                    closeOnClickOutside: false,
                    closeOnEsc: false
                }
            ).then(async value => {
                if (value) {
                    const data = { solicitud_id: solicitud.id };
                    await axios.post("dirprograma/activar-estudiante", data);

                    swal("Exito", "Estudiante aprobado con éxito", "success");
                    this.fetchSolicitudes();
                }
            });
        }
    }
};
</script>
