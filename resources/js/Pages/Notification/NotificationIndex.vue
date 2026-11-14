<template>
    <AppLayout>
        <!-- Contenedor global -->
        <div class="text-neutral container mx-auto p-4">
            <!-- Titulo de pagina -->
            <h1 class="text-neutral uppercase font-bold md:text-5xl text-2xl md:m-10 m-4 p-2">notificaciones</h1>

            <!--Botón para eliminar notificaciones-->
            <div class="flex items-center justify-end w-full">
                <div class="btn btn-sm bg-error text-white border-0 hover:bg-red-700 hover:duration-200 duration-200">
                    <Link :href="route('notifications.delete')" method="delete" as="button" class="flex items-center space-x-1 cursor-pointer">
                        <PhTrash class="md:size-6 size-4" weight="duotone" />
                        <span class="font-black">Eliminar notificaciones</span>
                    </Link>
                </div>
            </div>

            <!-- Contendedor para lista de notificaciones -->
            <div class="md:p-4 mt-8">
                <ul v-if="notifications.data.length" class="list bg-base-100 rounded-box space-y-3">
                    <li v-for="notification in notifications.data" :key="notification.id" class="list-row bg-base-200">
                        <div class="flex items-center">

                            <!-- * PROYECTOS -->
                            <!-- Notificación de creación de proyecto -->
                            <div v-if="notification.type === 'App\\Notifications\\ProjectCreated'" class="font-semibold">
                                Se ha generado un nuevo proyecto
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                para más información entra al siguiente
                                <Link :href="route('projects.show', {project: notification.data.project_id})" class="hover:text-blue-500 duration-200 hover:duration-200 inline-flex items-center gap-1">
                                    proyecto
                                    <PhCards class="md:size-5 size-4" />
                                </Link>
                            </div>

                            <!-- Notificación de modificación en el proyecto -->
                            <div v-if="notification.type === 'App\\Notifications\\ProjectEdited'" class="font-semibold">
                                Se ha modificado información de
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                para más información entra al siguiente
                                <Link :href="route('projects.show', {project: notification.data.project_id})" class="hover:text-blue-500 duration-200 hover:duration-200 inline-flex items-center gap-1">
                                    proyecto
                                    <PhCards class="md:size-5 size-4" />
                                </Link>
                            </div>

                            <!-- Notificación para modificación de fechas -->
                            <div v-if="notification.type === 'App\\Notifications\\ProjectChangeDates'" class="font-semibold">
                                Se ha modificado las fechas de
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                entra para ver las modificaciones
                                <Link :href="route('projects.show', { project: notification.data.project_id })" class="hover:text-blue-500 duration-200 hover:duration-200 inline-flex items-center gap-1">
                                    {{ notification.data.project_name }}
                                    <PhCards class="md:size-5 size-4" />
                                </Link>
                            </div>

                            <!-- Notificación de cambio de estatus de proyecto -->
                            <div v-if="notification.type === 'App\\Notifications\\UpdateProjectStatus'" class="font-semibold">
                                Se ha completado el proyecto
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                entra para ver los detalles
                                <Link :href="route('projects.show', { project: notification.data.project_id })" class="hover:text-blue-500 duration-200 hover:duration-200 inline-flex items-center gap-1">
                                    {{ notification.data.project_name }}
                                    <PhCards class="md:size-5 size-4" />
                                </Link>
                            </div>

                            <!-- Notificación de eliminación de proyecto -->
                            <div v-if="notification.type === 'App\\Notifications\\ProjectDeleted'" class="font-semibold">
                                Se ha eliminado
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                            </div>

                            <!-- * CLIENTES -->
                            <!-- Notificación de creación de cliente -->
                            <div v-if="notification.type === 'App\\Notifications\\ClientCreated'" class="font-semibold">
                                Se ha agregado al cliente
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                para
                                <Link :href="route('projects.show', {project: notification.data.project_id})" class="hover:text-blue-500 duration-200 hover:duration-200 inline-flex items-center gap-1">
                                    {{ notification.data.project_name }}
                                    <PhCards class="md:size-5 size-4" />
                                </Link>
                            </div>

                            <!-- Notificación de asignación de cliente -->
                            <div v-if="notification.type === 'App\\Notifications\\ClientAssigned'" class="font-semibold">
                                Se ha asignado al cliente
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                para
                                <Link :href="route('projects.show', {project: notification.data.project_id})" class="hover:text-blue-500 duration-200 hover:duration-200 inline-flex items-center gap-1">
                                    {{ notification.data.project_name }}
                                    <PhCards class="md:size-5 size-4" />
                                </Link>
                            </div>

                            <!-- Notificación de edición de cliente -->
                            <div v-if="notification.type === 'App\\Notifications\\ClientEdited'" class="font-semibold">
                                Se ha editado información de contacto del cliente
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                            </div>

                            <!-- Notificación de eliminación de cliente -->
                            <div v-if="notification.type === 'App\\Notifications\\ClientDeleted'" class="font-semibold">
                                Se ha eliminado al cliente
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                            </div>

                            <!-- * EMPRESA  -->
                            <!-- Notificación de unirse a la empresa-->
                            <div v-if="notification.type === 'App\\Notifications\\CompanyJoin'" class="font-semibold">
                                Felicidades te has unido a
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                para conocer más entra al siguiente
                                <Link :href="route('companies.show', {company: notification.data.company_id})" class="hover:text-blue-500 duration-200 hover:duration-200 inline-flex items-center gap-1">
                                    enlace
                                    <PhCards class="md:size-5 size-4" />
                                </Link>
                            </div>

                            <!-- Notificación de miembro removido de la empresa -->
                            <div v-if="notification.type === 'App\\Notifications\\CompanyLeave'" class="font-semibold">
                                Has sido removido de
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                            </div>

                            <!-- Notificación de creación de nueva empresa -->
                            <div v-if="notification.type === 'App\\Notifications\\CompanyCreated'" class="font-semibold">
                                Se ha generado exitosamente la empresa
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                para ver la empresa entra al siguiente
                                <Link :href="route('companies.show', {company: notification.data.company_id})" class="hover:text-blue-500 duration-200 hover:duration-200 inline-flex items-center gap-1">
                                    enlace
                                    <PhCards class="md:size-5 size-4" />
                                </Link>
                            </div>

                            <!-- Notificación de modificación de datos de la empresa -->
                            <div v-if="notification.type === 'App\\Notifications\\CompanyEdited'" class="font-semibold">
                                Se ha actualizado los datos de la empresa
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                            </div>

                            <!-- Notificación de eliminación de empresa -->
                            <div v-if="notification.type === 'App\\Notifications\\CompanyDeleted'" class="font-semibold">
                                Tu empresa
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                ha sido eliminada, puedes crear una nueva empresa o unirte a una
                            </div>

                            <!-- * USUARIOS -->
                            <!-- Notificación de creación de usuario (ADMIN)-->
                            <div v-if="notification.type === 'App\\Notifications\\UserCreated'" class="font-semibold">
                                Se ha generado un nuevo usuario
                                <span class="uppercase font-black underline">{{ notification.data.name }} {{ notification.data.last_name }}</span>
                            </div>

                            <div v-if="notification.type === 'App\\Notifications\\WelcomeUser'" class="font-semibold">
                                Bienvenido a Hikey,
                                <span class="uppercase font-black underline">{{ notification.data.name }} {{ notification.data.last_name }}</span>
                            </div>

                            <!-- Notificación de modificación de usuario -->
                            <div v-if="notification.type === 'App\\Notifications\\UserEdited'" class="font-semibold">
                                Se ha modificado la información del usuario
                                <span class="uppercase font-black underline">{{ notification.data.name }} {{ notification.data.last_name }}</span>
                            </div>

                            <!-- Notificación de eliminación de usuario -->
                            <div v-if="notification.type === 'App\\Notifications\\UserDeleted'" class="font-semibold">
                                Se ha eliminado al usuario
                                <span class="uppercase font-black underline">{{ notification.data.name }} {{ notification.data.last_name }}</span>
                            </div>

                            <!-- Notificación de cambio de contraseña -->
                            <div v-if="notification.type === 'App\\Notifications\\UpdatePassword'" class="font-semibold">
                                Has actualizado tu contraseña
                            </div>

                            <!-- * EQUIPOS -->
                            <!-- Notificación de miembro añadido a equipo para proyecto -->
                            <div v-if="notification.type === 'App\\Notifications\\TeamProjectMembers'" class="font-semibold">
                                Has sido añadido al equipo del proyecto
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                para más informacion sobre el proyecto entra al siguiente
                                <Link :href="route('projects.show', {project: notification.data.project_id})" class="hover:text-blue-500 duration-200 hover:duration-200 inline-flex items-center gap-1">
                                    enlace
                                    <PhCards class="md:size-5 size-4" />
                                </Link>
                            </div>

                            <!-- Notificación de asignación de lider -->
                            <div v-if="notification.type === 'App\\Notifications\\TeamProjectLeader'" class="font-semibold">
                                Has sido asignado com el lider de equipo para el proyecto
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                            </div>

                            <!-- Notificación de miembro removido de equipo -->
                            <div v-if="notification.type === 'App\\Notifications\\TeamProjectRemoveMember'" class="font-semibold">
                                Has sido removido del equipo para el proyecto
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                            </div>

                            <!-- Notificación de rol de LIDER de equipo revocado -->
                            <div v-if="notification.type === 'App\\Notifications\\TeamProjectRemoveLeader'" class="font-semibold">
                                Tu rol como lider del proyecto
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                ha sido revocado, continuaras trabajando en el proyecto otro integrante tomará tu posición
                            </div>

                            <!-- * ACTIVIDADES -->
                            <div v-if="notification.type === 'App\\Notifications\\NewActivity'" class="font-semibold">
                                Se ha generado una nueva actividad para el proyecto
                                <span class="uppercase font-black underline">{{ notification.data.project_name_activity }}</span>
                                , ve al tablón de actividades para conocer los detalles.
                            </div>

                            <div v-if="notification.type === 'App\\Notifications\\EditActivity'" class="font-semibold">
                                Se ha actualizado la información de la actividad
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                , ve al tablón de actividades para conocer los detalles.
                            </div>

                            <div v-if="notification.type === 'App\\Notifications\\DeleteActivity'" class="font-semibold">
                                Se ha eliminado la actividad
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                , ve al tablón de actividades para conocer los detalles.
                            </div>

                            <div v-if="notification.type === 'App\\Notifications\\AssignActivity'" class="font-semibold">
                                Se te ha asignado como responsable de la actividad
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                , ve al tablón de actividades para conocer los detalles.
                            </div>

                            <div v-if="notification.type === 'App\\Notifications\\ReviewActivity'" class="font-semibold">
                                {{notification.data.sender_name}} {{ notification.data.sender_last_name }}, ha mandado a revisión la actividad
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                , ve al tablón de actividades para conocer los detalles.
                            </div>

                            <div v-if="notification.type === 'App\\Notifications\\ReturnActivity'" class="font-semibold">
                                {{notification.data.sender_name}} {{ notification.data.sender_last_name }}, ha regresado tu actividad
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                , ve al tablón de actividades para conocer los detalles.
                            </div>

                            <div v-if="notification.type === 'App\\Notifications\\CompleteActivity'" class="font-semibold">
                                {{notification.data.sender_name}} {{ notification.data.sender_last_name }}, ha marcado como completada tu actividad
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                , espera a que tu líder de equipo te asigne tu siguiente actividad.
                            </div>

                            <div v-if="notification.type === 'App\\Notifications\\DueDateActivity'" class="font-semibold">
                                La actividad <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                vence mañana
                                , ve al tablón de actividades para conocer los detalles.
                            </div>

                            <div v-if="notification.type === 'App\\Notifications\\NewAction'" class="font-semibold">
                                Se ha añadido una nueva acción a realizar para la actividad
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                , ve al tablón de actividades para conocer los detalles.
                            </div>

                            <div v-if="notification.type === 'App\\Notifications\\UpdateAction'" class="font-semibold">
                                Se ha actualizado la información de las acciones para la actividad
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                , ve al tablón de actividades para conocer los detalles.
                            </div>

                            <div v-if="notification.type === 'App\\Notifications\\ActionCompleted'" class="font-semibold">
                                Has completado la acción
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                de la actividad <span class="uppercase font-black underline">{{ notification.data.activity_name }}</span>
                                , sigue así y continua con las demás acciones de la actividad.
                            </div>

                            <div v-if="notification.type === 'App\\Notifications\\NewComment'" class="font-semibold">
                                {{ notification.data.author_name }} {{ notification.data.author_last_name }} comentó en la actividad
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                , ve al tablón de actividades para conocer los detalles.
                            </div>

                            <div v-if="notification.type === 'App\\Notifications\\UpdatedComment'" class="font-semibold">
                                {{ notification.data.author_name }} {{ notification.data.author_last_name }} ha editado su comentario
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                , ve al tablón de actividades para conocer los detalles.
                            </div>

                            <div v-if="notification.type === 'App\\Notifications\\DeletedComment'" class="font-semibold">
                                El comentario 
                                <span class="uppercase font-black underline">{{ notification.data.name }}</span>
                                , ha sido eliminado de la actividad.
                            </div>
                        </div>

                        <!-- ? Botón para marcar leída la notificación -->
                        <div class="flex items-center justify-end gap-2">
                            <Link :href="route('notification.seen', {notification: notification.id})" as="button" method="put" v-if="!notification.read_at" class="flex items-center gap-2 btn btn-sm md:btn-md border-0 bg-primary hover:bg-primary-content text-black hover:duration-200 duration-200">
                                <PhEnvelopeOpen class="md:size-5 size-4" weight="duotone"/>
                                <span class="hidden md:inline">Marcar como leído </span>
                            </Link>
                        </div>
                    </li>
                </ul>

                <!-- Contenedor de paginado -->
                <div v-if="notifications.data.length" class="w-full flex justify-center py-4">
                    <PaginationComponent :links="notifications.links" />
                </div>

                <!-- Contenedor en caso de que el usuario aun no tenga notificaciones -->
                <div v-else class="p-4">
                    <div class="flex items-center w-full justify-start border-2 border-base-300 bg-base-200 rounded-lg p-4">
                        Sin notificaciones.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { PhEnvelopeOpen, PhCards, PhTrash } from '@phosphor-icons/vue';
import PaginationComponent from '../../Components/UI/PaginationComponent.vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

defineProps({
    'notifications': Object,
})
</script>
