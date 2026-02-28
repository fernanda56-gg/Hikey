<template>
    <!-- Contenedor global -->
    <div class="mb-8 mt-4 flex flex-wrap gap-2 px-4">
        <form @submit.prevent="filter">
            <!-- Contenedor de inputs de filtro -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- nombre de proyecto -->
                <div class="flex flex-nowrap items-center">
                    <input v-model="filterForm.name" class="w-48 bg-base-200 input input-sm md:input-md focus:outline-none" type="text" placeholder="Proyecto X">
                </div>

                <!-- área de proyecto -->
                <div class="flex flex-nowrap items-center">
                    <select v-model="filterForm.area" class="select select-sm md:select-md focus:outline-none bg-base-200">
                        <option class="w-32" :value="null" disabled selected>Área</option>
                        <option v-for="area in areas" :key="area.id" :value="area.id">{{ area.name }}</option>
                    </select>
                </div>

                <!-- estatus de proyecto -->
                <div class="flex flex-nowrap items-center">
                    <select v-model="filterForm.status" class="select select-sm md:select-md focus:outline-none bg-base-200">
                        <option class="w-28" :value="null" disabled selected>Estatus</option>
                        <option>Pendiente</option>
                        <option>En progreso</option>
                        <option>Completado</option>
                    </select>
                </div>

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
    'areas': Object,
})

const filterForm = useForm({
    name: props.filters.name ?? null,
    area: props.filters.area ?? null,
    status:props.filters.status ?? null,
})

const filter = () => {filterForm.get(route('projects.index'),{
    preserveState: true,
    preserveScroll:true,
})}

const clearFilter = () => {
    filterForm.name = null
    filterForm.area = null
    filterForm.status = null
    filter()
}

</script>
