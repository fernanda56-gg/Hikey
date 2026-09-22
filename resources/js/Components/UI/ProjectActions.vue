<template>
    <div>
        <div class="flex flex-row md:flex-col items-center md:items-start justify-start space-x-6 md:space-x-0 md:space-y-4 m-4">
            <Link v-if="can?.update" :href="route('projects.edit', {project: project.id})" class="flex items-center gap-2 font-bold link link-hover hover:text-[#f8961e] hover:duration-200 mr-4">
                <PhPencilSimple class="md:size-6 size-5" />
                Editar
            </Link>

            <Link v-if="can?.delete" :href="route('projects.destroy', {project: project.id})" method="delete" as="button" class="flex items-center gap-2 font-bold link link-hover hover:text-error hover:duration-200">
                <PhTrash class="md:size-6 size-5" />
                Eliminar
            </Link>

            <!-- Botón para actualizar estatus de proyecto -->
            <button
                v-if="can?.update && project.status === 'Pendiente'"
                @click="changeStatus('En progreso')"
                class="flex items-center gap-2 font-bold link link-hover hover:text-info hover:duration-200"
            >
                <PhChartDonut class="md:size-6 size-5" />
                Iniciar proyecto
            </button>

            <button
                v-if="can?.update && project.status === 'En progreso'"
                @click="changeStatus('Completado')"
                class="flex items-center gap-2 font-bold link link-hover hover:text-info hover:duration-200"
            >
                <PhCheckFat class="md:size-6 size-5" />
                Marcar como completado
            </button>

            <button
                v-if="can?.update && project.status === 'Completado'"
                @click="changeStatus('Pendiente')"
                class="flex items-center gap-2 font-bold link link-hover hover:text-info hover:duration-200"
            >
                <PhDotsThreeOutline class="md:size-6 size-5" />
                Reabrir proyecto
            </button>
        </div>
    </div>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3';
    import { route } from 'ziggy-js';
    import { router } from '@inertiajs/vue3';
    import { PhPencilSimple, PhTrash, PhChartDonut, PhDotsThreeOutline, PhCheckFat } from '@phosphor-icons/vue';

const props =  defineProps({
    project: Object,
    can: Object
})

// ? Ruta para función de actualizar estatus
function changeStatus(newStatus){
    router.put(route('projects.update-status', props.project.id), {
        status: newStatus,
    })
}
</script>
