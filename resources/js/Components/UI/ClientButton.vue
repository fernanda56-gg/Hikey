<template>
    <div class="flex items-center justify-center w-full">
        <h2 class="font-black text-center">Cliente</h2>
    </div>

    <!-- Contenedor de info de clientes (cuando ya se asigno a uno) -->
    <div v-if="projects.clients.length" class=" flex items-center justify-start md:mt-3 mt-1.5 pb-6 md:pb-0 mb-4 p-4">
        <div v-for="client in projects.clients" :key="client.id" class="flex flex-row items-center gap-2">
            <span class="font-semibold text-sm mr-3">{{ client.name }}</span>

            <button
                    class="btn btn-square btn-sm btn-ghost text-neutral"
                    :data-tip="copyText">
                <PhAt @click="copy(client.email)" class="size-4" weight="bold"/>
            </button>

            <button
                    class="btn btn-square btn-sm btn-ghost text-neutral"
                    :data-tip="copyText">
                <PhPhone @click="copy(client.phone)" class="size-4" weight="bold"/>
            </button>

            <div class="flex items-center">
                    <Link
                            class="btn btn-square btn-sm btn-soft btn-warning"
                            v-if="client.client_update"
                            type="button"
                            :href="route('clients.edit', {client: client.id})">
                        <PhPencilSimple class="size-4" weight="fill"/>
                    </Link>
                </div>

            <div class="flex flex-row gap-2">
                <div class="flex items-center">
                    <Link
                            class="btn btn-square btn-sm btn-soft btn-error"
                            v-if="client.client_delete"
                            :href="route('clients.destroy', {client: client.id})" method="delete" as="button">
                        <PhTrash class="size-4" weight="fill"/>
                    </Link>
                </div>

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
            <div class="btn md:btn-sm btn-md text-black bg-primary md:font-bold border-0 hover:bg-primary-content hover:duration-200 duration-200">
                    <Link :href="route('clients.create', { project: projects.id })" class="flex items-center space-x-1">
                        <PhPlus class="size-4" weight="bold"/>
                        <span class="text-xs">Nuevo cliente</span>
                    </Link>
            </div>

            <div class="btn md:btn-sm btn-md text-black bg-primary md:font-bold border-0 hover:bg-primary-content hover:duration-200 duration-200">
                    <Link :href="route('clients.projects.assign', { project: projects.id })" class="flex items-center space-x-1">
                        <PhStar class="size-4" weight="bold"/>
                        <span class="text-xs">Asignar cliente</span>
                    </Link>
            </div>
        </div>
</template>

<script setup>
import { PhStar, PhPlus, PhAt, PhPhone, PhPencilSimple, PhTrash, PhLinkBreak} from '@phosphor-icons/vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useClipboard } from '@vueuse/core';
import { ref, watch } from 'vue';
import { usePermission } from '../../composables/usePermission';

//Comprobar permisos de usuario
const {hasAnyRole} = usePermission();

defineProps({
    projects: Object,
})

const copyText = ref('Copiar')
const { copy, copied } = useClipboard({
    timeout: 2000,
})

watch(copied, (value) => {
    copyText.value = value ? 'Copiado' : 'Copiar'
})
</script>
