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
                        <div class="flex items-center">
                        <span class="bg-slate-200/50 rounded-lg p-1 text-xs">#{{ activity.id }}</span>
                        <span :class="getBadgeClass(activity.due_date)" >
                            <component :is="getIcon(activity.due_date)" class="size-4" weight="bold" />
                            {{ getDaysText(activity.due_date) }}
                        </span>
                        </div>
                        <h2 class="card-title">{{ activity.name }}</h2>
                        <p> {{ activity.description }}</p>


                        <div class="justify-end card-actions">
                            <div class="flex items-start justify-start gap-1 mr-auto my-auto">
                                <PhFlagBannerFold :class="[
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
                            <!-- BTN para mostrar modal de información de actividad -->
                            <button class="btn btn-sm btn-primary text-black" @click="openActivity(activity)">Mostrar más</button>
                            <!-- <button class="btn btn-sm btn-primary text-black">Mostrar más</button> -->
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

            <!-- ! MODAL DE INFORMACIÓN DE ACTIVIDAD -->
            <dialog ref="dialogRef" class="modal" @close="selectedActivityId = null">
                <div v-if="selectedActivity" class="modal-box w-11/12 max-w-2xl">
                    <form method="dialog">
                        <button class="btn btn-sm btn-square btn-ghost absolute right-2 top-2">
                            <PhX class="size-4 text-neutral" weight="bold" />
                        </button>
                    </form>
                    <h3 class="text-md font-semibold">Descripción de actividad</h3>

                    <!-- ! Contenedor de info de actividad y acciones -->
                    <div class="flex flex-col lg:flex-row md:p-4 text-neutral">

                        <!-- ? INFO DE ACTIVIDAD -->
                        <div class="card h-auto min-w-2/3 grow place-items-start p-4">
                            <form class="flex flex-col w-full" @submit.prevent="saveActivity">

                                <!-- * Btn para habilitar edición -->
                                <div class="flex items-center justify-end">
                                    <button
                                        v-if="selectedActivity?.update_p && !isEditing"
                                        @click="isEditing = true"
                                        class="btn btn-sm btn-square bg-slate-200/40 text-neutral">
                                        <PhPencilSimple class="size-4" weight="fill" />
                                    </button>
                                </div>

                                <!-- * Inputs de info de proyecto -->
                                <fieldset class="fieldset w-full">

                                    <!-- * NOMBRE DE ACTIVIDAD -->
                                    <label class="label text-neutral font-semibold">Nombre</label>
                                    <input v-model="editForm.name" type="text" class="input input-sm w-full outline-none" :disabled="!isEditing"/>

                                    <!-- Contenedor de error -->
                                    <div v-if="editForm.errors.name" class="flex items-center justify-start text-error text-xs">
                                        <PhWarningCircle class="mx-1 size-4" weight="bold" />
                                        {{ editForm.errors.name }}
                                    </div>

                                    <!-- * DESCRIPCIÓN DE LA ACTIVIDAD -->
                                    <label class="label text-neutral font-semibold">Descripción</label>
                                    <textarea v-model="editForm.description" class="textarea textarea-sm w-full outline-none" :disabled="!isEditing" />

                                    <!-- Contenedor de error -->
                                    <div v-if="editForm.errors.description" class="flex items-center justify-start text-error text-xs">
                                        <PhWarningCircle class="mx-1 size-4" weight="bold" />
                                        {{ editForm.errors.description }}
                                    </div>

                                    <!-- * LINK -->
                                    <label class="label text-neutral font-semibold">Link</label>
                                    <input v-model="editForm.link" type="text" class="input input-sm w-full outline-none" :disabled="!isEditing" />

                                    <!-- Contenedor de error -->
                                    <div v-if="editForm.errors.link" class="flex items-center justify-start text-error text-xs">
                                        <PhWarningCircle class="mx-1 size-4" weight="bold" />
                                        {{ editForm.errors.link }}
                                    </div>

                                    <!-- * PRIORIDAD Y FECHA DE ENTREGA DE ACTIVIDAD -->
                                    <div class="flex items-center justify-between w-full mt-0.5 gap-3">
                                        <div class="w-1/2">
                                            <label class="label text-neutral font-semibold mb-1">Prioridad</label>
                                            <select v-model="editForm.priority" :disabled="!isEditing" class="select select-sm outline-none">
                                                <option disabled selected>Prioridad de actividad</option>
                                                <option>Baja</option>
                                                <option>Media</option>
                                                <option>Alta</option>
                                                <option>Urgente</option>
                                            </select>
                                            <!-- Contenedor de error -->
                                            <div v-if="editForm.errors.priority" class="flex items-center justify-start text-error text-xs">
                                                <PhWarningCircle class="mx-1 size-4" weight="bold" />
                                                {{ editForm.errors.priority }}
                                            </div>
                                        </div>


                                        <div class="w-1/2">
                                            <label class="label text-neutral font-semibold mb-1">Fecha de entrega</label>
                                            <input v-model="editForm.due_date" type="date" :disabled="!isEditing" class="input input-sm w-full outline-none" />
                                        </div>
                                    </div>
                                </fieldset>

                                <!-- ? Acciones del form de edición de info -->
                                <div v-if="isEditing" class="modal-action">
                                    <button class="btn btn-sm btn-primary text-black" type="submit" :disabled="editForm.processing">Añadir</button>
                                    <button class="btn btn-sm btn-error text-white" type="button" @click="isEditing = false">Cancelar</button>
                                </div>
                            </form>
                        </div>

                        <div class="divider divider-horizontal"></div>

                        <!-- ? ACCIONES -->
                        <div class="card bg-transparent rounded-box lg:h-32 h-20 w-full grow place-items-center lg:place-items-start">
                            content
                        </div>
                    </div>
                </div>
                <div v-else>
                    <p class="text-neutral items-center">Información de actividad no disponible</p>
                </div>
            </dialog>
        </div>
    </div>


</template>

<script setup>
import { PhCircle, PhXCircle, PhFlagBannerFold, PhX, PhWarning, PhCalendarDots, PhPencilSimple } from '@phosphor-icons/vue';
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

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

// * Muestra cuando días faltan para que se venzca el plazo de la actividad con texto, badge e icono
function getDaysText(dueDate) {
    let days = calculateDays(dueDate);
    if (days < 0) return `Venció hace ${Math.abs(days)} días`;
    if (days === 0) return 'Vence hoy';
    if (days === 1) return 'Vence mañana';
    return `Faltan ${days} días`;
}

function getBadgeClass(dueDate) {
    let days = calculateDays(dueDate);
    if (days < 0) return 'flex items-stretch justify-between gap-1 badge badge-sm font-semibold badge-error items-end ml-auto text-white'; // Ya venció
    if (days <= 2) return 'flex items-stretch justify-between gap-1 badge badge-sm font-semibold badge-warning items-end ml-auto text-black'; // Proximo a vencer
    return 'flex items-stretch justify-between gap-1 badge badge-sm font-semibold items-end ml-auto text-black bg-slate-300/50'; // A tiempo
}

function getIcon(dueDate) {
    let days = calculateDays(dueDate);
    if (days < 0) return PhX; // Ya venció
    if (days <= 2) return PhWarning; // Proximo a vencer
    return PhCalendarDots; // A tiempo
}

// ! Modal de información y edición de información de actividad
const dialogRef = ref(null)
const selectedActivityId = ref(null)
const isEditing = ref(false)

const selectedActivity = computed (() => props.activities.find(act => act.id === selectedActivityId.value) ?? null)

/* function openActivityModal (activity) {
    selectedActivityId.value = activity.id
    dialogRef.value?.showModal()
} */

const editForm = useForm({
    name: '',
    description: '',
    priority: '',
    link: '',
    due_date: '',
})

function openActivity(activity) {
    selectedActivityId.value = activity.id
    isEditing.value = false

    editForm.defaults({
        name: activity.name,
        description: activity.description,
        priority: activity.priority,
        link: activity.link,
        due_date: activity.due_date,
    }).reset()
    dialogRef.value?.showModal()
}

function saveActivity() {
    if (!selectedActivityId.value) return

    editForm.put(route('activities.update', selectedActivityId.value), {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false // <--- Volvemos a modo lectura al guardar con éxito
            // dialogRef.value?.close() // Descomenta si quieres que se cierre al guardar
        }
    })
}

</script>
