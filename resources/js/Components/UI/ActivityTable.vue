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

                <!-- EN CASO DE QUE NO HAYA NINGUNA ACTIVIDAD -->
                <div class="flex items-center justify-start gap-2 h-full bg-base-200 rounded-lg shadow-md p-4" v-if="disponibles.length === 0">
                    <PhXCircle class="size-5" />
                    <p class="text-neutral text-start text-sm font-semibold">No hay actividades disponibles</p>
                </div>

                <!-- CUANDO HAY ACTIVIDADES REGISTRADAS -->
                <div v-for="activity in disponibles" :key="activity.id" class="card w-75 bg-base-200 card-sm shadow-md">
                    <div class="card-body">
                        <span :class="getBadgeClass(activity.due_date)" >
                            <component :is="getIcon(activity.due_date)" class="size-4" weight="bold" />
                            {{ getDaysText(activity.due_date) }}
                        </span>
                        <h2 class="card-title">{{ activity.name }}</h2>
                        <p> {{ activity.description }}</p>


                        <div class="justify-end card-actions">
                            <div class="flex items-start justify-start gap-1 mr-auto my-auto">
                                <PhFlagCheckered :class="[
                                    activity.priority === 'Baja' ? 'text-[#0496ff]' :
                                    activity.priority === 'Media' ? 'text-[#ffc700]' :
                                    activity.priority === 'Alta'? 'text-[#ff4800]' :
                                    'text-error'
                                ]" class="size-5" weight="fill" />
                                <span :class="[
                                    activity.priority === 'Baja' ? 'text-[#0496ff]' :
                                    activity.priority === 'Media' ? 'text-[#ffc700]' :
                                    activity.priority === 'Alta'? 'text-[#ff4800]' :
                                    'text-error'
                                ]" class="font-black uppercase">{{ activity.priority }}</span>
                            </div>
                            <button class="btn btn-sm btn-primary text-black">Mostrar más</button>
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
                        {{ enProgreso.length }}
                    </span>
                </div>

                <!-- Cards -->

                <!--  -->
                <div class="flex items-center justify-start gap-2 h-full bg-base-200 rounded-lg shadow-md p-4" v-if="enProgreso.length === 0">
                    <PhXCircle class="size-5" />
                    <p class="text-neutral text-start text-sm font-semibold">No hay actividades en progreso</p>
                </div>
                <div v-for="activity in progreso" :key="activity.id" class="card w-75 bg-base-200 card-sm shadow-md">
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
                        {{ enRevision.length }}
                    </span>
                </div>

                <!-- Cards -->
                <div class="flex items-center justify-start gap-2 h-full bg-base-200 rounded-lg shadow-md p-4" v-if="enRevision.length === 0">
                    <PhXCircle class="size-5" />
                    <p class="text-neutral text-start text-sm font-semibold">No hay actividades en revisión</p>
                </div>
                <div v-for="activity in revision" :key="activity.id" class="card w-75 bg-base-200 card-sm shadow-md">
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
                        {{ completadas.length }}
                    </span>
                </div>

                <!-- Cards -->
                <div class="flex items-center justify-start gap-2 h-full bg-base-200 rounded-lg shadow-md p-4" v-if="completadas.length === 0">
                    <PhXCircle class="size-5" />
                    <p class="text-neutral text-start text-sm font-semibold">No hay actividades completadas</p>
                </div>
                <div v-for="activity in completadas" :key="activity.id" class="card w-75 bg-base-200 card-sm shadow-md">
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
import { PhCircle, PhXCircle, PhFlagCheckered, PhX, PhWarning, PhCalendarDots } from '@phosphor-icons/vue';
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

// * Calcula cuantos días faltan para que la actividad se vence a partir de la fecha actual
function calculateDays(dueDate) {
    let start = new Date(); // ! Fecha y hora actual
    let end = new Date(dueDate); // ! Fecha límite de la actividad

    let timeDifference = end - start;
    let daysDifference = timeDifference / (1000 * 3600 * 24);

    // ? Math.ceil redondea hacia arriba para mostrar días completos restantes
    return Math.ceil(daysDifference);
}

function getDaysText(dueDate) {
    let days = calculateDays(dueDate);
    if (days < 0) return `Venció hace ${Math.abs(days)} días`;
    if (days === 0) return 'Vence hoy';
    if (days === 1) return 'Vence mañana';
    return `Faltan ${days} días`;
}

function getBadgeClass(dueDate) {
    let days = calculateDays(dueDate);
    if (days < 0) return 'flex items-stretch justify-between gap-1 badge badge-sm font-semibold badge-error items-end ml-auto text-black'; // Ya venció
    if (days <= 2) return 'flex items-stretch justify-between gap-1 badge badge-sm font-semibold badge-warning items-end ml-auto text-black'; // Proximo a vencer
    return 'flex items-stretch justify-between gap-1 badge badge-sm font-semibold items-end ml-auto text-black bg-slate-300/50'; // A tiempo
}

function getIcon(dueDate) {
    let days = calculateDays(dueDate);
    if (days < 0) return PhX; // Ya venció
    if (days <= 2) return PhWarning; // Proximo a vencer
    return PhCalendarDots; // A tiempo
}
</script>
