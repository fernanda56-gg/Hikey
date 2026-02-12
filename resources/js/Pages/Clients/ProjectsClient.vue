<template>
    <AppLayout>
        <!-- Contenedor global -->
        <div class="text-neutral container mx-auto p-4">
            <!-- Titulo de la pagina -->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-6xl text-2xl md:m-10 m-4 p-2">proyectos</h1>
            </div>

            <!-- Contenedor para lista de proyectos -->
            <div class="md:p-4">
                <ul v-if="projects.length" class="list bg-base-100 rounded-box space-y-3">
                    <li class="p-4 pb-2 text-lg opacity-60 tracking-wide capitalize text-neutral">Proyectos {{ client.name }}</li>

                    <li v-for="project in projects" :key="project.id" class="list-row bg-base-200">
                        <div class="space-y-1">
                            <!-- Nombre de proyecto -->
                            <div class="font-black capitalize text-lg">
                                {{ project.name }}
                            </div>

                            <!-- Área de proyecto -->
                            <div class="flex items-center">
                                <span
                                    :class="[
                                        project.area.name === 'Desarrollo' ? 'text-[#0496ff]' :
                                        project.area.name === 'Marketing' ? 'text-[#f2b705]' :
                                        project.area.name === 'Recursos Humanos' ? 'text-[#ff65b3]' :
                                        project.area.name === 'Finanzas' ? 'text-[#52b788]' :
                                        project.area.name === 'Producción' ? 'text-[#f26419]' :
                                        'text-[#9a8f97]'
                                    ]"
                                    class="flex items-center gap-2 text-sm capitalize font-semibold opacity-80"
                                >
                                    <component :is="areaIcons[project.area.name]" class="size-6" />
                                    {{ project.area.name }}
                                </span>
                            </div>

                            <!-- Estatus de proyecto -->
                            <div class="flex items-center space-x-2 text-neutral text-sm capitalize font-semibold opacity-80">
                                <span :class="[
                                    project.status === 'Pendiente' ? 'status bg-[#d90429] status-lg' :
                                    project.status === 'En progreso' ? 'status bg-[#ffc300] status-lg' :
                                    'status bg-[#70e000] status-lg'
                                ]">
                                </span>
                                <span>
                                    {{ project.status }}
                                </span>
                            </div>
                        </div>

                        <!-- Botón para ir al proyecto -->
                        <div class="flex items-center justify-end gap-2">
                            <Link :href="route('clients.projects.detach', {project: project.id, client: client.id})" method="delete" as="button" class="hidden sm:flex btn border-0 bg-primary-content/80 text-black">
                                Desvincular
                                <PhLinkBreak class="md:size-5 size-4" />
                            </Link>

                            <Link :href="route('projects.show', {project: project.id})" class="hidden sm:flex btn border-0 bg-primary-content/80 text-black">
                                Proyecto
                                <PhArrowRight class="md:size-5 size-4" />
                            </Link>

                            <!-- Links para vista de celular -->
                            <Link :href="route('clients.projects.detach', {project: project.id})" method="delete" as="button" class="md:hidden btn border-0 bg-primary-content/80 text-black">
                                <PhLinkBreak class="md:size-5 size-4" weight="bold"/>
                            </Link>

                            <Link :href="route('projects.show', {project: project.id})" class="md:hidden btn border-0 bg-primary-content/80 text-black">
                                <PhArrowRight class="md:size-5 size-4" weight="bold"/>
                            </Link>
                        </div>

                        <!-- Botón para ir al proyecto -->
                        <div class="flex items-center justify-end gap-2">

                        </div>
                    </li>
                </ul>

                <!-- Contenedor en caso de que el cliente no tenga un proyecto vinculado -->

                    <div v-else class="flex items-center text-neutral border-2 border-base-300 bg-base-200 rounded-lg p-4">
                        <span>Aún no hay proyectos asignados a este cliente.</span>
                    </div>

            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { PhCode, PhChartLineUp, PhPerson, PhCoins, PhGearSix, PhLightbulbFilament, PhArrowRight, PhLinkBreak } from '@phosphor-icons/vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

defineProps({
    client: Object,
    projects: Object,
})

const areaIcons = {
        'Desarrollo': PhCode,
        'Marketing': PhChartLineUp,
        'Recursos Humanos': PhPerson,
        'Finanzas': PhCoins,
        'Producción': PhGearSix,
        'Otros': PhLightbulbFilament,
    }
</script>
