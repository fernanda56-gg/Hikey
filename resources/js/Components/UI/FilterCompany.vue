<template>
    <!-- Contenedor principal -->
    <div class="mb-8 mt-4 flex flex-wrap gap-2 px-4">
        <form @submit.prevent="filter">
            <!-- Contenedor de inputs de filtro -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- nombre de empresa -->
                <div class="flex flex-nowrap items-center">
                    <input v-model="filterForm.name" class="w-48 bg-base-200 input input-sm md:input-md focus:outline-none" type="text" placeholder="Empresa X">
                </div>
                <!-- ciudad -->
                <div class="flex flex-nowrap items-center">
                    <input v-model="filterForm.city" class="w-48 bg-base-200 input input-sm md:input-md focus:outline-none" type="text" placeholder="Ciudad">
                </div>
                <!-- país -->
                <div class="flex flex-nowrap items-center">
                    <input v-model="filterForm.country" class="w-48 bg-base-200 input input-sm md:input-md focus:outline-none" type="text" placeholder="País">
                </div>

                <!-- Botón filtro -->
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
})

const filterForm = useForm({
    name: props.filters.name ?? null,
    city: props.filters.city ?? null,
    country: props.filters.country ?? null,
})

const filter = () => {
    filterForm.get(route('companies.index'), {
        preserveState: true,
        preserveScroll:true,
    })
}

const clearFilter = () => {
    filterForm.name = null
    filterForm.city = null
    filterForm.country = null
    filter()
}
</script>
