<template>
    <div class="text-neutral">
        <h1 class="uppercase font-bold text-xl">proyecto</h1>
        <span class="text-lg">{{ projects.name }}</span>
        <h1 class="uppercase font-bold text-xl mt-2">descripción</h1>
        <p class="text-lg">{{ projects.description }}</p>

        <!--Links-->
        <div class="flex flex-row mt-4 space-y-2">
            <div class="flex flex-col space-x-2 md:mr-8 mr-4">
                <h1 class="uppercase font-bold text-lg">link de proyecto</h1>
                <a :href="projects.link" class="flex items-center space-x-1 text-accent font-bold" target="_blank" rel="noopener noreferrer">
                    <span>Link</span>
                    <PhLink :size="20" weight="bold"/>
                </a>
            </div>

            <div class="flex flex-col">
                <h1 class="uppercase font-bold text-lg">link de img</h1>
                <a :href="projects.link" class="flex items-center space-x-1 text-accent font-bold" target="_blank" rel="noopener noreferrer">
                    <span>Link</span>
                    <PhLink :size="20" weight="bold"/>
                </a>
            </div>
        </div>

        <!--Fechas-->
        <div class="flex flex-row mt-4 space-y-2">
            <div class="flex flex-col space-x-2 md:mr-8 mr-4">
                <h1 class="uppercase font-bold text-lg">fecha de inicio</h1>
                <span class="text-lg">{{ formatDate(projects.start_date) }}</span>
            </div>

            <div class="flex flex-col">
                <h1 class="uppercase font-bold text-lg">fecha de finalización</h1>
                <span class="text-lg">{{ formatDate(projects.end_date) }}</span>
            </div>
        </div>

        <!--Estatus de proyecto-->
        <h1 class="uppercase font-bold text-lg mt-4">estatus de proyecto</h1>
        <div class="flex items-center mt-2 space-x-2 text-neutral font-bold text-lg">
            <span :class="[
                projects.status === 'planned' ? 'status bg-[#d90429] status-lg' :
                projects.status === 'in_progress' ? 'status bg-[#ffc300] status-lg' :
                'status bg-[#70e000] status-lg'
            ]">
            </span>
            <span>
                {{ projects.status === 'planned' ? 'Pendiente' :
                projects.status === 'in_progress' ? 'En progreso' :
                'Completado' }}
            </span>
        </div>


    </div>
</template>
<script setup>
import { PhLink } from '@phosphor-icons/vue';
import dayjs from 'dayjs';
import 'dayjs/locale/es';
defineProps(
        {'projects': Object,}
    );

// Cambia el formato de la fecha
const formatDate = (date) => {
    return dayjs(date, 'YYYY-MM-DD').locale('es').format('D [de] MMM [de] YYYY')
}
</script>
