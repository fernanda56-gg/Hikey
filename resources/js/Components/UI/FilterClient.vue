<template>
    <!-- Contenedor principal -->
    <div class="mb-4 mt-4 flex flex-wrap gap-2 px-4">
        <form @submit.prevent="filter">
            <!-- Contenedor de inputs de filtro -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- nombre de cliente -->
                <div class="flex flex-nowrap items-center">
                    <input v-model="filterForm.name" class="w-48 bg-base-200 input input-sm md:input-md focus:outline-none" type="text" placeholder="Nombre de cliente">
                </div>

                <!-- nombre de proyecto -->
                <div class="flex flex-nowrap items-center">
                    <input v-model="filterForm.projectName" class="w-48 bg-base-200 input input-sm md:input-md focus:outline-none" type="text" placeholder="Proyecto X">
                </div>

                <!-- nombre de empresa -->
                <div v-if="hasAnyRole(['admin'])" class="flex flex-nowrap items-center">
                    <input v-model="filterForm.companyName" class="w-48 bg-base-200 input input-sm md:input-md focus:outline-none" type="text" placeholder="Empresa">
                </div>

                <!-- Botón de filtros -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-sm md:btn-md bg-primary text-black border-0 hover:bg-primary-content hover:duration-200 duration-200">
                        <span class="flex items-center space-x-1">
                            <PhFunnel class="md:size-5 size-4" />
                            <span class="font-black">Filtrar</span>
                        </span>
                    </button>
                    <button type="reset" @click="clearFilter" class="btn btn-sm md:btn-md bg-black text-white border-0 hover:bg-slate-700 hover:duration-200 duration-200">
                        <span class="flex items-center space-x-1">
                            <PhFunnelX class="md:size-5 size-4" />
                            <span class="font-black">Limpiar</span>
                        </span>
                    </button>

                    <!-- Botón para soft delete -->
                    <Link v-if="hasAnyRole(['admin', 'manager'])" :href="route('clients.trash')" class="btn btn-sm md:btn-md bg-error text-white border-0 hover:bg-red-700 hover:duration-200 duration-200">
                        <span class="flex items-center md:space-x-1">
                            <PhTrashSimple class="md:size-5 size-4" weight="bold" />
                            <span class="font-black hidden md:flex">Eliminados</span>
                        </span>
                    </Link>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { PhFunnel, PhFunnelX, PhTrashSimple } from '@phosphor-icons/vue';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Link } from '@inertiajs/vue3';
import { usePermission } from '../../composables/usePermission';

//Comprobar permisos de usuario
const {hasAnyRole} = usePermission();

const props = defineProps({
    'filters': Object,
})

const filterForm = useForm({
    name: props.filters.name ?? null,
    projectName: props.filters.projectName ?? null,
    companyName: props.filters.companyName ?? null,
})

const filter = () => {
    filterForm.get(route('clients.index'), {
        preserveState: true,
        preserveScroll:true,
    })
}

const clearFilter = () => {
    filterForm.name = null
    filterForm.projectName = null
    filterForm.companyName = null
    filter()
}
</script>
