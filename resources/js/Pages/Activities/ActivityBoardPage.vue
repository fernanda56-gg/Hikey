<template>
    <AppLayout>
        <!-- Contenedor global -->
        <div class="text-neutral container mx-auto p-4">
            <!-- Titulo de la pagina -->
            <h1 class="text-neutral flex justify-between items-start md:text-4xl text-2xl font-black uppercase py-4 underline decoration-primary-content">tablero de actividades</h1>

            <!-- Link de breadcrumbs -->
            <div class="breadcrumbs px-4 py-1.5 text-xs md:text-sm">
                <ul>
                    <li><Link :href="route('inicio')"><PhHouseLine class="md:size-6 size-5 cursor-pointer hover:text-success duration-200 hover:duration-200" weight="duotone" /></Link></li>
                    <li><Link :href="route('projects.index')" class="hover:text-success duration-200 hover:duration-200 font-semibold">Proyectos</Link></li>
                    <li>Tablero de actividades</li>
                </ul>
            </div>

            <!-- Apartado de btn de soft delete, filtro y crear actividad -->
            <div class="flex items-center justify-end w-full gap-3">
                <div class="btn btn-sm text-white bg-error md:font-bold border-0 hover:bg-red-600 hover:duration-200 duration-200">
                    <Link :href="route('projects.create')" class="flex items-center space-x-1">
                        <PhTrash class="size-4" weight="bold" />
                    </Link>
                </div>

                <div class="btn btn-sm text-white bg-black border-0 hover:bg-slate-700 hover:duration-200 duration-200">
                    <Link :href="route('projects.create')" class="flex items-center space-x-1">
                        <PhFunnel class="size-4" weight="fill" />
                        <span>Filtro</span>
                    </Link>
                </div>

                <div class="btn btn-sm text-black bg-primary md:font-bold border-0 hover:bg-primary-content hover:duration-200 duration-200">
                    <Link :href="route('projects.create')" class="flex items-center space-x-1">
                        <PhPlus class="size-4" weight="bold" />
                        <span>Nueva actividad</span>
                    </Link>
                </div>
            </div>

            <!-- Tablón de actividades -->
            <ActivityTable />
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { PhHouseLine, PhPlus, PhTrash, PhFunnel } from '@phosphor-icons/vue';
import ActivityTable from '../../Components/UI/ActivityTable.vue';
</script>
