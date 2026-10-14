<template>
    <AppLayout>
        <!-- Contenedor global -->
        <div class="text-neutral container mx-auto p-4">
            <!--Titulo de pagina-->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-5xl text-2xl md:m-10 m-4 p-2">actividades eliminados</h1>
            </div>

            <!-- Link breadcrumbs -->
            <div class="breadcrumbs px-4 py-1.5 text-xs md:text-sm mt-4">
                <ul>
                    <li><Link :href="route('inicio')"><PhHouseLine class="md:size-6 size-5 cursor-pointer hover:text-success duration-200 hover:duration-200" weight="duotone" /></Link></li>
                    <li><Link :href="route('projects.index')" class="hover:text-success duration-200 hover:duration-200 font-semibold">Proyectos</Link></li>
                    <li><Link :href="route('projects.show', {project: project.id})" class="hover:text-success duration-200 hover:duration-200 font-semibold">{{ project.name }}</Link></li>
                    <li><Link :href="route('activities-board.show', {project: project.id})" class="hover:text-success duration-200 hover:duration-200 font-semibold">Tablón de actividades</Link></li>
                    <li>Actividades eliminados</li>
                </ul>
            </div>

            <!-- Contenedor para lista de actividades -->
            <div class="md:p-4 mt-8">
                <ul v-if="activities.length" class="list bg-base-100 rounded-box space-y-3">
                    <li v-for="activity in activities" :key="activity.id" class="list-row bg-base-200">
                        <div class="space-y-1">

                            <!-- Nombre de la actividad -->
                            <div class="font-black capitalize text-lg">
                                {{ activity.name }}
                            </div>

                            <!-- Descripción de la actividad -->
                            <div class="flex items-center text-xs">
                                <div class="flex items-center md:w-2xl w-lg">
                                    {{ activity.description }}
                                </div>
                            </div>
                        </div>
                        <!-- Acciones para actividades -->

                        <!-- * BTN RECUPERAR -->
                        <div class="flex items-center justify-end gap-2 ml-auto">
                            <Link :href="route('activities.restore', { activity: activity.id })" method="put"
                                class="flex items-center gap-2 btn btn-sm md:btn-md border-0 bg-primary hover:bg-primary-content text-black hover:duration-200 duration-200">
                                <span class="hidden md:inline">Recuperar </span>
                                <PhArrowBendUpRight class="md:size-5 size-4" weight="duotone" />
                            </Link>

                            <!-- * BTN DE ELIMINACIÓN DEFINITIVA -->
                            <Link :href="route('activities.force-delete', { activity: activity.id })" method="delete" as="button"
                                class="flex items-center gap-2 btn btn-sm md:btn-md border-0 bg-error hover:bg-red-700 text-white hover:duration-200 duration-200">
                                <span class="hidden md:inline">Eliminar</span>
                                <PhTrash class="md:size-5 size-4" weight="duotone" />
                            </Link>
                        </div>
                    </li>
                </ul>

                <!-- Contenedor en caso de que aun no haya actividades eliminadas -->

                <div v-else class="flex items-center text-neutral border-2 border-base-300 bg-base-200 rounded-lg p-4">
                    <span>Aún no hay actividades eliminadas.</span>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { route } from 'ziggy-js';
import { Link } from '@inertiajs/vue3';
import { PhHouseLine, PhArrowBendUpRight, PhTrash } from '@phosphor-icons/vue';

defineProps({
    project: Object,
    activities: Object
})
</script>
