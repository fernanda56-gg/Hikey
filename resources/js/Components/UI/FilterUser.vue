<template>
    <!-- Contenedor global -->
    <div class="mb-4 mt-4 flex flex-wrap gap-2 px-4">
        <form @submit.prevent="filter">
            <!-- Contenedor de inputs de filtro -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- nombre de usuario -->
                <div class="flex flex-nowrap items-center">
                    <input v-model="filterForm.name" class="w-48 bg-base-200 input input-sm md:input-md focus:outline-none" type="text" placeholder="Nombre de usuario">
                </div>

                <!-- rol de usuario -->
                <div class="flex flex-nowrap items-center">
                    <select v-model="filterForm.role" class="select select-sm md:select-md focus:outline-none bg-base-200">
                        <option class="w-28" :value="null" disabled selected>Rol</option>
                        <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                    </select>
                </div>

                <!-- Botón de filtro -->
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
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { PhFunnel, PhFunnelX } from '@phosphor-icons/vue';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({
    'filters': Object,
    'roles': Object,
})

const filterForm = useForm({
    name: props.filters.name ?? null,
    role: props.filters.role ?? null,
})

const filter = () => {
    filterForm.get(route('manage-account.index'), {
        preserveState: true,
        preserveScroll:true,
    })
}

const clearFilter = () => {
    filterForm.name = null
    filterForm.role = null
    filter()
}
</script>
