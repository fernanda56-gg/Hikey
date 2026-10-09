<template>
    <div class="overflow-x-auto text-neutral">
        <div class="flex min-w-max gap-4 p-4">

            <!-- Columna de DISPONIBLES -->
            <div class="flex w-80 shrink-0 flex-col gap-3 rounded-lg p-3">

                <!-- Titulo de columna -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-1">
                        <PhCircle class="size-4 text-info" weight="fill" />
                        <h2 class="uppercase font-black md:text-lg">Disponibles</h2>
                    </div>

                    <!-- Contador de actividades -->
                    <span class="bg-slate-200/50 rounded-md text-neutral font-bold px-2 py-1 text-xs">
                        {{ disponibles.length }}
                    </span>
                </div>

                <!-- Cards -->
                <div v-for="activity in disponibles" :key="activity.id" class="card w-75 bg-base-200 card-sm shadow-md">
                    <div class="card-body">
                        <h2 class="card-title">{{ activity.name }}</h2>
                        <p>A card component has a figure, a body part, and inside body there are title and actions parts
                        </p>
                        <div class="justify-end card-actions">
                            <button class="btn btn-primary text-black">Buy Now</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna de EN PROGRESO -->
            <div class="flex w-80 shrink-0 flex-col gap-3 rounded-lg p-3">

                <!-- Titulo de columna -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-1">
                        <PhCircle class="size-4 text-warning" weight="fill" />
                        <h2 class="uppercase font-black md:text-lg">En progreso</h2>
                    </div>

                    <!-- Contador de actividades -->
                    <span class="bg-slate-200/50 rounded-md text-neutral font-bold px-2 py-1 text-xs">
                        4
                    </span>
                </div>

                <!-- Cards -->
                <div class="card w-75 bg-base-200 card-sm shadow-md">
                    <div class="card-body">
                        <h2 class="card-title">Small Card</h2>
                        <p>A card component has a figure, a body part, and inside body there are title and actions parts
                        </p>
                        <div class="justify-end card-actions">
                            <button class="btn btn-primary text-black">Buy Now</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna de EN REVISIÓN -->
            <div class="flex w-80 shrink-0 flex-col gap-3 rounded-lg p-3">

                <!-- Titulo de columna -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-1">
                        <PhCircle class="size-4 text-indigo-600" weight="fill" />
                        <h2 class="uppercase font-black md:text-lg">En revisión</h2>
                    </div>

                    <!-- Contador de actividades -->
                    <span class="bg-slate-200/50 rounded-md text-neutral font-bold px-2 py-1 text-xs">
                        4
                    </span>
                </div>

                <!-- Cards -->
                <div class="card w-75 bg-base-200 card-sm shadow-md">
                    <div class="card-body">
                        <h2 class="card-title">Small Card</h2>
                        <p>A card component has a figure, a body part, and inside body there are title and actions parts
                        </p>
                        <div class="justify-end card-actions">
                            <button class="btn btn-primary text-black">Buy Now</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna de COMPLETADAS -->
            <div class="flex w-80 shrink-0 flex-col gap-3 rounded-lg p-3">

                <!-- Titulo de columna -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-1">
                        <PhCircle class="size-4 text-success" weight="fill" />
                        <h2 class="uppercase font-black md:text-lg">Completadas</h2>
                    </div>

                    <!-- Contador de actividades -->
                    <span class="bg-slate-200/50 rounded-md text-neutral font-bold px-2 py-1 text-xs">
                        4
                    </span>
                </div>

                <!-- Cards -->
                <div class="card w-75 bg-base-200 card-sm shadow-md">
                    <div class="card-body">
                        <h2 class="card-title">Small Card</h2>
                        <p>A card component has a figure, a body part, and inside body there are title and actions parts
                        </p>
                        <div class="justify-end card-actions">
                            <button class="btn btn-primary text-black">Buy Now</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


</template>

<script setup>
import { PhCircle } from '@phosphor-icons/vue';
import { computed } from 'vue';

const props = defineProps ({
    project: Object,
    activities: Array
})

// ! Obtienen la actividad dependiendo del estado
const disponibles = computed(() => props.activities.filter(a => a.status === 'Disponible'))
const enProgreso = computed(() => props.activities.filter(a => a.status === 'En progreso'))
const enRevision = computed(() => props.activities.filter(a => a.status === 'Revisión'))
const completadas = computed(() => props.activities.filter(a => a.status === 'Completada'))
</script>
