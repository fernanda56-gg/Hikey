<template>
    <AppLayout>
        <!-- Contenedor global -->
        <div class="text-neutral container mx-auto p-4">
            <!-- Titulo de la pagina -->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-5xl text-2xl md:m-10 m-4 p-2">clientes eliminados</h1>
            </div>

            <!-- Link breadcrumbs -->
            <div class="breadcrumbs px-4 py-1.5 text-xs md:text-sm mt-4">
                <ul>
                    <li><Link :href="route('inicio')"><PhHouseLine class="md:size-6 size-5 cursor-pointer hover:text-success duration-200 hover:duration-200" weight="duotone" /></Link></li>
                    <li><Link :href="route('clients.index')" class="hover:text-success duration-200 hover:duration-200 font-semibold">Clientes</Link></li>
                    <li>Clientes eliminados</li>
                </ul>
            </div>

            <!-- Contenedor de lista de clientes -->
            <div class="md:p-4 mt-8">
                <ul v-if="clients.data.length" class="list bg-base-100 rounded-box space-y-3">
                    <li v-for="client in clients.data" :key="client.id" class="list-row bg-base-200">
                        <div class="space-y-1">
                            <!-- Nombre del cliente -->
                            <div class="font-black capitalize text-lg">
                                {{ client.name }}
                            </div>

                            <!-- Correo de cliente -->
                            <div class="flex items-center space-x-2 text-neutral text-sm capitalize font-semibold opacity-80">
                                <span>
                                    {{ client.email }}
                                </span>
                            </div>
                        </div>

                        <!-- Botón para proyecto recuperar -->
                        <div class="flex items-center justify-end gap-2">
                            <Link :href="route('clients.recover', { client: client.id })" class="flex items-center gap-2 btn btn-sm md:btn-md border-0 bg-primary hover:bg-primary-content text-black hover:duration-200 duration-200">
                                <span class="hidden md:inline">Recuperar </span>
                                <PhArrowBendUpRight class="md:size-5 size-4" weight="duotone"/>
                            </Link>

                            <!-- Botón para eliminar proyecto definitivamente -->
                            <Link :href="route('clients.destroy', {client: client.id})" method="delete" as="button" class="flex items-center gap-2 btn btn-sm md:btn-md border-0 bg-error hover:bg-red-700 text-white hover:duration-200 duration-200">
                                <span class="hidden md:inline">Eliminar</span>
                                <PhTrash class="md:size-5 size-4" weight="duotone" />
                            </Link>
                        </div>
                    </li>

                    <!-- Contenedor de paginado -->
                    <div class="w-full flex justify-center">
                        <PaginationComponent :links="clients.links" />
                    </div>
                </ul>

                <!-- Contenedor en caso de que aun no haya proyectos eliminados -->

                <div v-else class="flex items-center text-neutral border-2 border-base-300 bg-base-200 rounded-lg p-4">
                    <span>Aún no hay clientes eliminados.</span>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import PaginationComponent from '../../Components/UI/PaginationComponent.vue';
import { route } from 'ziggy-js';
import { Link } from '@inertiajs/vue3';
import { PhHouseLine, PhArrowBendUpRight, PhTrash} from '@phosphor-icons/vue';

defineProps({
    'clients': Object,
})
</script>
