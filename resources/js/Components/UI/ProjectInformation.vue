<template>
    <div class="text-neutral">
        <h1 class="uppercase font-bold md:text-xl text-base">proyecto</h1>
        <span class="text-lg">{{ projects.name }}</span>
        <h1 class="uppercase font-bold md:text-xl text-base mt-2">descripción</h1>
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

        <!-- Área -->
        <div class="flex flex-col">
            <h1 class="uppercase font-bold text-lg">Área del proyecto</h1>
            <span :class="[
                    projects.area.name === 'Desarrollo' ? 'text-[#0496ff]':
                    projects.area.name === 'Marketing' ? 'text-[#f2b705]':
                    projects.area.name === 'Recursos Humanos' ? 'text-[#ff65b3]':
                    projects.area.name === 'Finanzas' ? 'text-[#52b788]':
                    projects.area.name === 'Producción' ? 'text-[#f26419]':
                    'text-[#9a8f97]'
                ]" class="flex items-center gap-2 text-sm font-black mt-1.5">
                    <component :is="areaIcons[projects.area.name]" class="size-6" />
                    {{ projects.area.name }}
                </span>
        </div>

        <!--Fechas-->
        <div class="flex flex-row mt-4 space-y-2">
            <div class="flex flex-col space-x-2 md:mr-8 mr-4">
                <h1 class="uppercase font-bold text-lg">fecha de inicio</h1>
                <span class="md:text-lg text-base">{{ formatDate(projects.start_date) }}</span>
            </div>

            <div class="flex flex-col">
                <h1 class="uppercase font-bold text-lg">fecha de finalización</h1>
                <span class="md:text-lg text-base">{{ formatDate(projects.end_date) }}</span>
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
import { PhLink, PhCode, PhChartLineUp, PhPerson, PhCoins, PhGearSix, PhLightbulbFilament, } from '@phosphor-icons/vue';
import dayjs from 'dayjs';
import 'dayjs/locale/es';
defineProps(
        {'projects': Object,}
    );

// Cambia el formato de la fecha
const formatDate = (date) => {
    return dayjs(date, 'YYYY-MM-DD').locale('es').format('D [de] MMM [de] YYYY')
}

const areaIcons = {
        'Desarrollo': PhCode,
        'Marketing': PhChartLineUp,
        'Recursos Humanos': PhPerson,
        'Finanzas': PhCoins,
        'Producción': PhGearSix,
        'Otros': PhLightbulbFilament,
    }
</script>
