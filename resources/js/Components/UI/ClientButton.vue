<template>
    <div class="flex items-center justify-center w-full">
        <h2 class="font-black text-center">Cliente</h2>
    </div>

    <!-- Contenedor de info de clientes (cuando ya se asigno a uno) -->
    <div v-if="projects.clients.length" class=" flex items-center justify-start md:mt-3 mt-1.5 pb-6 md:pb-0 mb-4 p-4">
        <div v-for="client in projects.clients" :key="client.id" class="flex flex-row items-center gap-2">
            <span class="font-semibold text-sm mr-3">{{ client.name }}</span>

            <!-- Btn copiar correo -->
            <button
                    class="btn btn-square btn-sm btn-ghost text-neutral"
                    :data-tip="copyText">
                <PhAt @click="copy(client.email)" class="size-4" weight="bold"/>
            </button>

            <!-- Btn copiar teléfono -->
            <button
                    class="btn btn-square btn-sm btn-ghost text-neutral"
                    :data-tip="copyText">
                <PhPhone @click="copy(client.phone)" class="size-4" weight="bold"/>
            </button>

            <!-- Btn editar -->
            <div class="flex items-center">
                    <Link
                            class="btn btn-square btn-sm btn-soft btn-warning"
                            v-if="client.client_update"
                            type="button"
                            :href="route('clients.edit', {client: client.id})">
                        <PhPencilSimple class="size-4" weight="fill"/>
                    </Link>
                </div>

                <!-- Btn eliminar -->
            <div class="flex flex-row gap-2">
                <div class="flex items-center">
                    <Link
                            class="btn btn-square btn-sm btn-soft btn-error"
                            v-if="client.client_delete"
                            :href="route('clients.destroy', {client: client.id})" method="delete" as="button">
                        <PhTrash class="size-4" weight="fill"/>
                    </Link>
                </div>

                <!-- Btn desvincular cliente -->
                <div class="flex items-center">
                    <Link
                            v-if="client.client_unlink"
                            class="btn btn-square btn-sm btn-soft btn-info"
                            type="button"
                            :href="route('clients.projects.detach', {project: projects.id, client: client.id})" method="delete" as="button">
                        <PhLinkBreak class="size-4" weight="bold"/>
                    </Link>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones para acceder a clientes -->
        <div v-else class="flex flex-row lg:flex-row md:flex-col items-center justify-center w-full gap-2 mt-2 pb-6 md:pb-0">

            <!-- Btn agregar cliente -->
            <div class="btn md:btn-sm btn-md text-black bg-primary md:font-bold border-0 hover:bg-primary-content hover:duration-200 duration-200">
                    <Link :href="route('clients.create', { project: projects.id })" class="flex items-center space-x-1">
                        <PhPlus class="size-4" weight="bold"/>
                        <span class="text-xs">Nuevo cliente</span>
                    </Link>
            </div>

            <!-- Btn asignar cliente (modal) -->
            <button class="btn md:btn-sm btn-md text-black bg-primary md:font-bold border-0 hover:bg-primary-content hover:duration-200 duration-200" @click="openClientsModal">
                <PhStar class="size-4" weight="bold"/>
                <span class="text-xs">Asignar cliente</span>
            </button>
        </div>

        <!-- Modal de asignación de cliente -->
        <dialog ref="dialogRef" class="modal">
            <div class="modal-box">

                <!-- titulo de modal -->
                <h3 class="text-lg font-bold">Asignar cliente a proyecto</h3>

                <!-- Tabla de clientes -->
                <div v-if="clients.length" class="w-fit overflow-x-auto rounded-box border-2 border-base-content/15 bg-base-100 overflow-y-auto max-h-50 scrollbar-thin scroll-smooth mt-2.5">
                    <table class="table md:table-md table-sm w-auto">
                        <!-- head -->
                        <thead class="bg-base-200 text-neutral">
                            <tr>
                                <th class="w-80">Nombre</th>
                                <th class="w-30">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="client in clients" :key="client.id">
                                <td>{{ client.name }}</td>
                                <td>
                                    <Link
                                        @click="attachClient(client.id)"
                                        class="btn btn-sm bg-primary text-black border-0 hover:bg-primary-content hover:duration-200 duration-200">
                                        <span class="font-semibold">Asignar</span>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- En caso de que aún no haya clientes registrados -->
                <span v-else>Aún no hay clientes registrados.</span>

                <div class="modal-action">
                    <form method="dialog">
                        <!-- if there is a button in form, it will close the modal -->
                        <button class="btn btn-error text-white">Cancelar</button>
                    </form>
                </div>
            </div>
        </dialog>
</template>

<script setup>
import { PhStar, PhPlus, PhAt, PhPhone, PhPencilSimple, PhTrash, PhLinkBreak} from '@phosphor-icons/vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useClipboard } from '@vueuse/core';
import { ref, watch } from 'vue';
import axios from 'axios'
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    projects: Object,
})

const copyText = ref('Copiar')
const { copy, copied } = useClipboard({
    timeout: 2000,
})

watch(copied, (value) => {
    copyText.value = value ? 'Copiado' : 'Copiar'
})

const clients = ref([])
const dialogRef = ref(null)

async function openClientsModal() {
    dialogRef.value?.showModal()

    const { data } = await axios.get(route('clients.projects.client-list', props.projects.id))
    clients.value = data.clients
}

const form = useForm({
    client_id: null,
})

const attachClient = (clientId) => {
    form.client_id = clientId
    form.post(route('clients.projects.attach', props.projects.id), {

        onSuccess: () => {
            dialogRef.value?.close()
        },
        onError: () => {
            dialogRef.value?.close()
        }
    })
}
</script>
