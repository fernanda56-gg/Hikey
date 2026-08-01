<template>
    <div class="text-neutral">
        <h1 class="uppercase font-bold md:text-xl text-base">proyecto</h1>
        <span class="md:text-lg text-sm">{{ projects.name }}</span>
        <h1 class="uppercase font-bold md:text-xl text-base mt-2">descripción</h1>
        <p class="md:text-lg text-sm text-justify break-all">{{ projects.description }}</p>

        <!--Links-->
        <div class="flex flex-row mt-4 space-y-2">
            <div class="flex flex-col space-x-2 md:mr-8 mr-4">
                <h1 class="uppercase font-bold md:text-lg text-base">link de proyecto</h1>
                <a :href="projects.link" class="flex items-center space-x-1 text-accent font-bold" target="_blank" rel="noopener noreferrer">
                    <span class="md:text-lg text-sm">Link</span>
                    <PhLink :size="20" weight="bold"/>
                </a>
            </div>

            <div class="flex flex-col">
                <h1 class="uppercase font-bold md:text-lg text-base">link de img</h1>
                <a :href="projects.link" class="flex items-center space-x-1 text-accent font-bold" target="_blank" rel="noopener noreferrer">
                    <span class="md:text-lg text-sm">Link</span>
                    <PhLink :size="20" weight="bold"/>
                </a>
            </div>
        </div>

        <!-- Área -->
        <div class="flex flex-col">
            <h1 class="uppercase font-bold md:text-lg text-base">Área del proyecto</h1>
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
                <h1 class="uppercase font-bold md:text-lg text-sm">fecha de inicio</h1>
                <input class="focus:outline-none" type="date" v-model="form.start_date" @change="updateDate" @input="form.start_date = $event.target.value || null" :disabled="form.end_date">
            </div>

            <div class="flex flex-col">
                <h1 class="uppercase font-bold md:text-lg text-sm">fecha de finalización</h1>
                <input class="focus:outline-none" type="date" v-model="form.end_date" @change="updateDate" @input="form.end_date = $event.target.value || null" :disabled="!form.start_date">
            </div>
        </div>

        <!--Estatus de proyecto y Empresa propietaria de proyecto (ADMIN)-->
        <div class="flex flex-row gap-4 items-start">
            <div class="flex flex-col">
                <h1 class="uppercase font-bold md:text-lg text-base mt-4">estatus de proyecto</h1>
                <div class="flex items-center mt-2 space-x-2 text-neutral font-bold md:text-lg text-sm">
                    <span :class="[
                        projects.status === 'Pendiente' ? 'status bg-[#d90429] status-lg' :
                        projects.status === 'En progreso' ? 'status bg-[#ffc300] status-lg' :
                        'status bg-[#70e000] status-lg'
                    ]"></span>
                    <span>{{ projects.status }}</span>
                </div>
            </div>

            <div v-if="hasRole('admin')" class="flex flex-col">
                <h1 class="uppercase font-bold md:text-lg text-base mt-4">Empresa propietaria</h1>
                <span class="mt-2 font-stretch-expanded md:text-lg text-sm">
                    {{ projects.company.name }}
                </span>
            </div>
        </div>
    </div>
</template>
<script setup>
import { useForm } from '@inertiajs/vue3';
import { PhLink, PhCode, PhChartLineUp, PhPerson, PhCoins, PhGearSix, PhLightbulbFilament, } from '@phosphor-icons/vue';
import { route } from 'ziggy-js';
import { usePermission } from '../../composables/usePermission';

//Comprobar permisos de usuario
const {hasRole} = usePermission();

const props = defineProps(
        {'projects': Object,
            'can': Object,
        }
    )

const form = useForm({
    start_date: props.projects.start_date,
    end_date: props.projects.end_date,
})

const updateDate = () => form.put(route('projects.update-date', props.projects.id))

const areaIcons = {
        'Desarrollo': PhCode,
        'Marketing': PhChartLineUp,
        'Recursos Humanos': PhPerson,
        'Finanzas': PhCoins,
        'Producción': PhGearSix,
        'Otros': PhLightbulbFilament,
    }
</script>
