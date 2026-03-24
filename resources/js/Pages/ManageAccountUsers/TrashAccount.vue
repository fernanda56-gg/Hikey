<template>
    <AppLayout>
        <!-- Contenedor global -->
        <div class="text-neutral container mx-auto p-4">
            <!--Titulo de pagina-->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-5xl text-2xl md:m-10 m-4 p-2">usuarios eliminados</h1>
            </div>

            <!-- Link breadcrumbs -->
            <div class="breadcrumbs px-4 py-1.5 text-xs md:text-sm mt-4">
                <ul>
                    <li><Link :href="route('inicio')"><PhHouseLine class="md:size-6 size-5 cursor-pointer hover:text-success duration-200 hover:duration-200" weight="duotone" /></Link></li>
                    <li><Link :href="route('manage-account.index')" class="hover:text-success duration-200 hover:duration-200 font-semibold">Usuarios</Link></li>
                    <li>Usuarios eliminados</li>
                </ul>
            </div>

            <!-- Contenedor para lista de usuarios -->
            <div class="md:p-4 mt-8">
                <ul v-if="users.data.length" class="list bg-base-100 rounded-box space-y-3">
                    <li v-for="user in users.data" :key="user.id" class="list-row bg-base-200">
                        <div class="space-y-1">
                            <!-- Nombre de usuario -->
                            <div class="font-black capitalize text-lg">
                                {{ user.name }} {{ user.last_name }}
                            </div>

                            <!-- correo de usuario -->
                            <div class="flex items-center space-x-2 text-neutral text-sm capitalize font-semibold opacity-80">
                                <span>
                                    {{ user.email }}
                                </span>
                            </div>

                            <!-- rol de usuario -->
                            <div v-for="role in user.roles" :key="role.id" class="flex items-center gap-1 text-neutral text-sm capitalize font-semibold opacity-80">
                                <PhIdentificationBadge class="md:size-6 size-4" weight="duotone" :class="[
                            role.name === 'admin' ? 'text-success' :
                            role.name === 'manager' ? 'text-error' :
                            role.name === 'team-leader' ? 'text-warning' :
                            'text-info'
                        ]"/>
                                <span class="font-black">
                                    {{ role.name === 'admin' ? 'Admin' :
                            role.name === 'manager' ? 'Manager' :
                            role.name === 'team-leader' ? 'Líder de equipo' :
                            'Miembro' }}
                                </span>
                            </div>
                        </div>

                        <!-- Botón para proyecto recuperar -->
                        <div class="flex items-center justify-end gap-2">
                            <Link :href="route('manage-account.recover', {user: user.id})" class="flex items-center gap-2 btn btn-sm md:btn-md border-0 bg-primary hover:bg-primary-content text-black hover:duration-200 duration-200">
                                <span class="hidden md:inline">Recuperar </span>
                                <PhArrowBendUpRight class="md:size-5 size-4" weight="duotone"/>
                            </Link>

                            <!-- Botón para eliminar proyecto definitivamente -->
                            <Link :href="route('manage-account.destroy', {user: user.id})" method="delete" as="button" class="flex items-center gap-2 btn btn-sm md:btn-md border-0 bg-error hover:bg-red-700 text-white hover:duration-200 duration-200">
                                <span class="hidden md:inline">Eliminar</span>
                                <PhTrash class="md:size-5 size-4" weight="duotone" />
                            </Link>
                        </div>
                    </li>

                    <!-- Contenedor de paginado -->
                    <div class="w-full flex justify-center">
                        <PaginationComponent :links="users.links" />
                    </div>
                </ul>

                <!-- Contenedor en caso de que aun no haya proyectos eliminados -->

                <div v-else class="flex items-center text-neutral border-2 border-base-300 bg-base-200 rounded-lg p-4">
                    <span>Aún no hay usuarios eliminados.</span>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import PaginationComponent from '../../Components/UI/PaginationComponent.vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { PhHouseLine, PhIdentificationBadge, PhArrowBendUpRight, PhTrash } from '@phosphor-icons/vue';

defineProps({
    'users': Object,
})
</script>
