<template>
    <AppLayout>
        <!-- Contenedor global -->
        <div v-if="client.length" class="text-neutral container mx-auto p-4">
            <!-- Titulo de la pagina -->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-6xl text-2xl md:m-10 m-4 p-2">asignar cliente</h1>
            </div>

            <!-- Link breadcrumbs -->
            <div class="breadcrumbs px-4 py-1.5 text-xs md:text-sm">
                <ul>
                    <li><Link :href="route('inicio')"><PhHouseLine class="md:size-6 size-5 cursor-pointer hover:text-success duration-200 hover:duration-200" weight="duotone" /></Link></li>
                    <li><Link :href="route('projects.show', {project: project.id})" class="hover:text-success duration-200 hover:duration-200 font-semibold">Empresa</Link></li>
                    <li>Asignación de cliente</li>
                </ul>
            </div>

            <!-- Contenedor de lista de clientes -->

            <div class="md:p-4">
                <ul v-if="client.length" class="list bg-base-100 rounded-box space-y-3">
                    <li v-for="clientSelect in client" :key="clientSelect.id" class="list-row bg-base-200">
                        <div class="flex items-center font-black capitalize text-lg">
                            {{ clientSelect.name }}
                        </div>

                        <div class="flex items-center justify-end">
                            <button @click="attach(clientSelect.id)" type="button" class="btn border-0 bg-primary-content/80 text-black">Asignar <PhStar class="size-4" weight="bold"/></button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Contenedor en caso de que aun no haya clientes -->
        <div v-else class="p-4">
            <div class="flex items-center w-full justify-start border-2 border-base-300 bg-base-200 rounded-lg p-4">
                Aún no hay clientes registrados.
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { route } from 'ziggy-js';
import { Link } from '@inertiajs/vue3';
import { PhStar, PhHouseLine } from '@phosphor-icons/vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    client: Object,
    project: Object,
})

const form = useForm({
    client_id: null,
})

const attach = (clientId) =>{
    form.client_id = clientId;
    form.post(route('clients.projects.attach', props.project.id))
}

</script>
