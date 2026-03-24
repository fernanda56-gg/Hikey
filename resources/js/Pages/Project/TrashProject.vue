<template>
    <AppLayout>
        <!-- Contenedor global -->
        <div class="text-neutral container mx-auto p-4">
            <!--Titulo de pagina-->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-5xl text-2xl md:m-10 m-4 p-2">proyectos eliminados</h1>
            </div>

            <!-- Link breadcrumbs -->
            <div class="breadcrumbs px-4 py-1.5 text-xs md:text-sm mt-4">
                <ul>
                    <li><Link :href="route('inicio')"><PhHouseLine class="md:size-6 size-5 cursor-pointer hover:text-success duration-200 hover:duration-200" weight="duotone" /></Link></li>
                    <li><Link :href="route('projects.index')" class="hover:text-success duration-200 hover:duration-200 font-semibold">Proyectos</Link></li>
                    <li>Proyectos eliminados</li>
                </ul>
            </div>

            <!-- Contenedor para lista de proyectos -->
            <div class="md:p-4 mt-8">
                <ul v-if="projects.data.length" class="list bg-base-100 rounded-box space-y-3">
                    <li v-for="project in projects.data" :key="project.id" class="list-row bg-base-200">
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

                        <!-- Botón para proyecto recuperar -->
                        <div class="flex items-center justify-end gap-2">
                            <Link :href="route('projects.recover', { project: project.id })" class="flex items-center gap-2 btn btn-sm md:btn-md border-0 bg-primary hover:bg-primary-content text-black hover:duration-200 duration-200">
                                <span class="hidden md:inline">Recuperar </span>
                                <PhArrowBendUpRight class="md:size-5 size-4" weight="duotone"/>
                            </Link>

                            <!-- Botón para eliminar proyecto definitivamente -->
                            <Link :href="route('projects.destroy', {project: project.id})" method="delete" as="button" class="flex items-center gap-2 btn btn-sm md:btn-md border-0 bg-error hover:bg-red-700 text-white hover:duration-200 duration-200">
                                <span class="hidden md:inline">Eliminar</span>
                                <PhTrash class="md:size-5 size-4" weight="duotone" />
                            </Link>
                        </div>
                    </li>

                    <!-- Contenedor de paginado -->
                    <div class="w-full flex justify-center">
                        <PaginationComponent :links="projects.links" />
                    </div>
                </ul>

                <!-- Contenedor en caso de que aun no haya proyectos eliminados -->

                <div v-else class="flex items-center text-neutral border-2 border-base-300 bg-base-200 rounded-lg p-4">
                    <span>Aún no hay proyectos eliminados.</span>
                </div>

            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import PaginationComponent from '../../Components/UI/PaginationComponent.vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { PhHouseLine, PhCode, PhChartLineUp, PhPerson, PhCoins, PhGearSix, PhLightbulbFilament, PhArrowBendUpRight, PhTrash} from '@phosphor-icons/vue';

defineProps({
    'projects': Object,
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


