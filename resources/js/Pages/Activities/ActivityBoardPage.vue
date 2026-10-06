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
                    <li><Link :href="route('projects.show', {project: project.id})" class="hover:text-success duration-200 hover:duration-200 font-semibold">{{ project.name }}</Link></li>
                    <li>Tablero de actividades</li>
                </ul>
            </div>

            <!-- ! Apartado de btn de soft delete, filtro y crear actividad -->
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

                <!-- ? Btn para abrir modal de nueva actividad -->
                <div class="btn btn-sm text-black bg-primary md:font-bold border-0 hover:bg-primary-content hover:duration-200 duration-200">
                    <button class="flex items-center space-x-1 cursor-pointer" @click="openModal">
                        <PhPlus class="size-4" weight="bold" />
                        <span>Nueva actividad</span>
                    </button>
                </div>
            </div>

            <!-- ! Modal para añadir actividades -->
            <dialog ref="dialogRef" class="modal">
                <div class="modal-box w-11/12">
                    <h3 class="text-lg font-bold">Nueva actividad</h3>

                    <!-- ? Form de actividades -->
                    <form class="flex items-center justify-between w-full" @submit.prevent="store">
                        <fieldset class="fieldset w-full p-4">
                            <label class="label text-neutral font-semibold">Nombre</label>
                            <input type="text" class="input input-sm w-full outline-none" placeholder="Título de la actividad" />

                            <label class="label text-neutral font-semibold">Descripción</label>
                            <textarea class="textarea textarea-sm w-full outline-none" placeholder="Descripción de la actividad"></textarea>

                            <label class="label text-neutral font-semibold">Link</label>
                            <input type="text" class="input input-sm w-full outline-none" placeholder="Link de recursos" />

                            <div class="flex items-center justify-between w-full mt-0.5 gap-3">
                                <div class="w-1/2">
                                    <label class="label text-neutral font-semibold">Prioridad</label>
                                    <input type="text" class="input input-sm w-full outline-none" placeholder="Título de la actividad" />
                                </div>
                                <div class="w-1/2">
                                    <label class="label text-neutral font-semibold">Fecha de entrega</label>
                                    <input type="text" class="input input-sm w-full outline-none" placeholder="Título de la actividad" />
                                </div>
                            </div>
                        </fieldset>
                    </form>


                    <!-- ? Acciones del modal -->
                    <div class="modal-action">
                        <form method="dialog">
                            <!-- if there is a button in form, it will close the modal -->
                            <button class="btn btn-sm btn-error text-white">Cancelar</button>
                        </form>
                    </div>
                </div>
            </dialog>

            <!-- ! Tablón de actividades -->
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
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps ({
    project: Object,
    activity: Object
})

const dialogRef = ref(null)

const form = useForm ({
    name: null,
    description: null,
    priority: 'Baja',
    link: null,
    due_date: null
})

function openModal() {
    dialogRef.value?.showModal()
}

function store() {
    form.post(route('activities.store', props.project.id), {
        onSuccess: () => {
            dialogRef.value?.close()
            form.reset()
        },
    })
}
</script>
