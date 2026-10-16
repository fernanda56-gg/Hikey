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

                <!-- ! EN CASO DE QUE NO HAYA NINGUNA ACTIVIDAD -->
                <div class="flex items-center justify-start gap-2 h-full bg-base-200 rounded-lg shadow-md p-4" v-if="disponibles.length === 0">
                    <PhXCircle class="size-5" />
                    <p class="text-neutral text-start text-sm font-semibold">No hay actividades disponibles</p>
                </div>

                <!-- ! CUANDO HAY ACTIVIDADES REGISTRADAS -->
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

                <!-- ! EN CASO DE QUE NO HAYA NINGUNA ACTIVIDAD -->
                <div class="flex items-center justify-start gap-2 h-full bg-base-200 rounded-lg shadow-md p-4" v-if="enProgreso.length === 0">
                    <PhXCircle class="size-5" />
                    <p class="text-neutral text-start text-sm font-semibold">No hay actividades en progreso</p>
                </div>

                <!-- ! CUANDO HAY ACTIVIDADES REGISTRADAS -->
                <div v-for="activity in enProgreso" :key="activity.id" class="card w-75 bg-base-200 card-sm shadow-md">
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

                <!-- ! EN CASO DE QUE NO HAYA NINGUNA ACTIVIDAD -->
                <div class="flex items-center justify-start gap-2 h-full bg-base-200 rounded-lg shadow-md p-4" v-if="enRevision.length === 0">
                    <PhXCircle class="size-5" />
                    <p class="text-neutral text-start text-sm font-semibold">No hay actividades en revisión</p>
                </div>

                <!-- ! CUANDO HAY ACTIVIDADES REGISTRADAS -->
                <div v-for="activity in enRevision" :key="activity.id" class="card w-75 bg-base-200 card-sm shadow-md">
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

                <!-- ! EN CASO DE QUE NO HAYA NINGUNA ACTIVIDAD -->
                <div class="flex items-center justify-start gap-2 h-full bg-base-200 rounded-lg shadow-md p-4" v-if="completadas.length === 0">
                    <PhXCircle class="size-5" />
                    <p class="text-neutral text-start text-sm font-semibold">No hay actividades completadas</p>
                </div>

                <!-- ! CUANDO HAY ACTIVIDADES REGISTRADAS -->
                <div v-for="activity in completadas" :key="activity.id" class="card w-75 bg-base-200 card-sm shadow-md">
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

            <!-- ! MODAL DE INFORMACIÓN DE ACTIVIDAD -->
            <dialog id="my_modal_1" ref="dialogRef" class="modal" @close="onModalClose">
                <div v-if="selectedActivity" class="modal-box w-11/12 max-w-3xl">
                    <form method="dialog">
                        <button @click="closeActivity" type="button" class="btn btn-sm btn-square btn-ghost absolute right-2 top-2">
                            <PhX class="size-4 text-neutral" weight="bold" />
                        </button>
                    </form>
                    <h3 class="text-md font-semibold">Descripción de actividad</h3>

                    <!-- ! Contenedor de info de actividad y acciones -->
                    <div class="flex flex-col lg:flex-row md:p-4 text-neutral">

                        <!-- ? INFO DE ACTIVIDAD -->
                        <div class="card h-auto min-w-3/5 grow place-items-start p-4">
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

                            <!-- ! CONTENEDOR DE USUARIO ASIGNADO -->
                            <div class="flex items-center justify-start mt-3 gap-2">
                                <span class="text-sm text-neutral font-black">Responsable:</span>
                                <span class="text-xs text-slate-400/50 font-semibold">Asignar a responsable...</span>
                            </div>

                            <!-- ! CONTENEDOR DE STATUS DE PROYECTO -->
                            <div class="flex items-center justify-start mt-3 gap-2">
                                <span class="text-sm text-neutral font-black">Estatus:</span>
                                <span :class="[
                                    selectedActivity.status === 'Disponible' ? 'badge badge-sm badge-soft badge-info text-xs font-semibold' :
                                    selectedActivity.status === 'En progreso' ? 'badge badge-sm badge-soft badge-warning text-xs font-semibold' :
                                    selectedActivity.status === 'Revision' ? 'badge badge-sm badge-soft badge-accent text-xs font-semibold' :
                                    'badge badge-sm badge-soft badge-success text-xs font-semibold'
                                ]">{{ selectedActivity.status }}</span>
                            </div>

                            <!-- ! CONTENDEDOR DE PRIORIDAD -->
                            <div class="flex items-center justify-start mt-3 gap-2">
                                <span class="text-sm text-neutral font-black">Prioridad:</span>
                                <span :class="[
                                    selectedActivity.priority === 'Baja' ? 'badge badge-sm badge-soft badge-info text-xs font-semibold' :
                                    selectedActivity.priority === 'Media' ? 'badge badge-sm badge-soft badge-warning text-xs font-semibold' :
                                    selectedActivity.priority === 'Alta' ? 'badge badge-sm badge-soft badge-error text-xs font-semibold' :
                                    'badge badge-sm badge-soft badge-error text-xs font-semibold'
                                ]">{{ selectedActivity.priority }}</span>
                            </div>

                            <!-- ! CONTENEDOR DE FECHA LIMITE -->
                            <div class="flex items-center justify-start mt-3 gap-2">
                                <span class="text-sm text-neutral font-black">Fecha limite:</span>
                                <span class="text-xs text-neutral font-semibold">{{ selectedActivity.due_date }}</span>
                            </div>

                            <!-- ! CONTENEDOR DE FECHA DE INICIO -->
                            <div class="flex items-center justify-start mt-3 gap-2">
                                <span class="text-sm text-neutral font-black">Fecha de inicio:</span>
                                <span class="text-xs text-neutral font-semibold">{{ selectedActivity.start_date }}</span>
                            </div>

                            <!-- ! CONTENEDOR DE FECHA FINALIZACIÓN -->
                            <div class="flex items-center justify-start mt-3 gap-2">
                                <span class="text-sm text-neutral font-black">Fecha de finalización:</span>
                                <span class="text-xs text-neutral font-semibold">{{ selectedActivity.completed_at }}</span>
                            </div>

                            <!-- ! CONTENEDOR PARA LINK DE RECURSOS DE ACTIVIDAD -->
                            <div class="flex items-center justify-start mt-3 gap-2">
                                <span class="text-sm text-neutral font-black">Link:</span>
                                <a :href="selectedActivity.link" target="_blank" rel="noopener noreferrer">
                                    <PhPaperclip class="size-4 text-slate-400/60 hover:text-neutral duration-200 " weight="bold" />
                                </a>
                            </div>

                            <div class="divider"></div>

                            <!-- * BTN DE ELIMINADOS -->
                            <div @click="deleteActivity" class="flex items-center justify-start gap-2">
                                <PhTrash class="size-4 text-error" weight="fill" />
                                <span class=" link link-hover text-sm link-error text-error font-black duration-200">Eliminar actividad</span>
                            </div>

                            <!-- * BTN DE ACTUALIZAR ESTATUS DE ACTIVIDAD -->
                            <button v-if="selectedActivity.changeStatus_p && selectedActivity.status === 'Disponible'"
                                @click="router.patch(route('activities.update-status', selectedActivity.id), { status: 'En progreso' })"
                                class="flex items-center justify-start gap-2 mt-3 link link-hover hover:text-info duration-200 text-sm font-black">
                                <PhPlay class="size-4" weight="bold" />
                                Iniciar actividad</button>

                            <button v-if="selectedActivity.changeStatus_p && selectedActivity.status === 'En progreso'"
                                @click="router.patch(route('activities.update-status', selectedActivity.id), { status: 'Revision' })"
                                class="flex items-center justify-start gap-2 mt-3 link link-hover hover:text-info duration-200 text-sm font-black">
                                <PhTestTube class="size-4" weight="bold" />
                                Mandar a revisión</button>

                            <div v-if="selectedActivity.review_p && selectedActivity.status === 'Revision'" class="flex flex-col gap-2 mt-3">
                                <button @click="router.patch(route('activities.return', selectedActivity.id))"
                                    class="flex items-center justify-start gap-2 link link-hover hover:text-info duration-200 text-sm font-black">
                                    <PhArrowsClockwise class="size-4" weight="bold" />
                                    Regresar actividad
                                </button>
                                <button @click="router.patch(route('activities.mark-complete', selectedActivity.id))"
                                    class="flex items-center justify-start gap-2 link link-hover hover:text-info duration-200 text-sm font-black">
                                    <PhChecks class="size-4" weight="bold" />
                                    Marcar completado
                                </button>
                            </div>

                            <div v-if="hasRole('admin') && selectedActivity.status === 'Revision'"
                                class="flex items-center justify-start gap-2 mt-3 text-sm text-slate-400/50 font-black cursor-not-allowed">
                                <PhTestTube class="size-4" weight="bold" />
                                Actividad en revisión</div>

                            <div v-if="selectedActivity.status === 'Completado'"
                                class="flex items-center justify-start gap-2 mt-3 text-sm text-slate-400/50 font-black cursor-not-allowed">
                                <PhChecks class="size-4" weight="bold" />
                                Actividad completada</div>
                        </div>
                    </div>
                </div>
                <div v-else class="modal-box w-auto">
                    <p class="text-neutral items-center">Información de actividad no disponible</p>
                </div>
            </dialog>
        </div>
    </div>


</template>

<script setup>
import { PhCircle, PhXCircle, PhFlagBannerFold, PhX, PhCalendarDots, PhPencilSimple, PhTrash, PhWarningDiamond, PhPlay, PhTestTube, PhArrowsClockwise, PhChecks, PhPaperclip } from '@phosphor-icons/vue';
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import { usePermission } from '../../composables/usePermission';

//Comprobar permisos de usuario
const {hasRole} = usePermission();

const props = defineProps ({
    project: Object,
    activities: Array
})

// ! Obtienen la actividad dependiendo del estado
const disponibles = computed(() => props.activities.filter(a => a.status === 'Disponible'))
const enProgreso = computed(() => props.activities.filter(a => a.status === 'En progreso'))
const enRevision = computed(() => props.activities.filter(a => a.status === 'Revision'))
const completadas = computed(() => props.activities.filter(a => a.status === 'Completado'))

// * Calcula cuantos días faltan para que la actividad se vence a partir de la fecha actual
function calculateDays(dueDate) {
    if (! dueDate) return 0

    // ! Fecha actual a partir de las 00:00:00 del día que se registra la actividades
    let start = new Date()
    start.setHours(0,0,0,0)

    // ! Se obtiene el dia, mes y año de la fecha del due date
    let [year, month, day] = dueDate.split('-').map(Number)
    let end = new Date(year, month -1, day)
    end.setHours(0,0,0,0)

    // ! Se obtiene la diferencia de días
    let timeDifference = end - start
    let daysDifference = timeDifference / (1000 * 3600 * 24);

    // ? Math.round para evitar decimales por los cambios de horario
    return Math.round(daysDifference);
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
    if (days <= 2) return PhWarningDiamond; // Proximo a vencer
    return PhCalendarDots; // A tiempo
}

// ! Modal de información y edición de información de actividad
const dialogRef = ref(null)
const selectedActivityId = ref(null)
const isEditing = ref(false)

const selectedActivity = computed (() => props.activities.find(act => act.id === selectedActivityId.value) ?? null)

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

function closeActivity () {
    document.getElementById("my_modal_1").close()
}

function onModalClose () {
    isEditing.value = false
    editForm.reset()
    selectedActivityId.value = null
}

function saveActivity() {
    if (!selectedActivityId.value) return

    editForm.put(route('activities.update', selectedActivityId.value), {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false
        }
    })
}

function deleteActivity() {
    document.getElementById("my_modal_1").close()

    router.delete(route('activities.destroy', selectedActivityId.value), {
        onSuccess: () => {
            selectedActivityId.value = null
            isEditing.value = false
        }
    })
}

</script>
