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
                    <!-- <li>{{ project.name }}</li> -->
                </ul>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { PhHouseLine } from '@phosphor-icons/vue';
</script>
