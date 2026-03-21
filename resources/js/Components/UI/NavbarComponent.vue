<template>
    <!--Contenedor global-->
    <nav class="text-neutral">
        <div class="container max-w-full mx-auto md:flex items-center gap-6 border-b border-gray-300">
            <!--Logo-->
            <div class="flex items-center justify-between md:w-auto w-full">
                <Link :href="route('inicio')" class="flex items-center space-x-2">
                    <img src="/images/logo.png" class="w-[30px] m-2">
                    <span class="font-sans font-bold uppercase text-xl flex-1 py-5">hikey</span>
                </Link>



                <!--Icono de menu desplegable-->
                <div class="md:hidden flex items-center px-3">
                    <button @click="toggleMenu" class="menu-button">
                        <svg xmlns="http://www.w3.org/2000/svg" class="fill-neutral cursor-pointer" width="32" height="32" fill="none" viewBox="0 0 256 256">
                            <path d="M228,128a12,12,0,0,1-12,12H40a12,12,0,0,1,0-24H216A12,12,0,0,1,228,128ZM40,76H216a12,12,0,0,0,0-24H40a12,12,0,0,0,0,24ZM216,180H40a12,12,0,0,0,0,24H216a12,12,0,0,0,0-24Z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!--Links-->
            <div :class="[
                'md:flex md:flex-row flex-col items-center justify-start md:space-x-1 pb-3 md:pb-0 navigation-menu',
                menuOpen ? 'flex' : 'hidden'
            ]">
                <div class="md:hover:bg-primary-content rounded-lg hover:duration-200 duration-200 md:dark:hover:text-black">
                    <Link :href="route('inicio')" class="px-1.5 lg:px-3 py-2 block lg:text-base text-sm">Inicio</Link>
                </div>


                <!--Links para usuario admin-->
                <!--Menu dropdown de vista de usuarios-->
                <div v-if="hasRole('admin')" class="dropdown md:dropdown-hover relative">
                    <div tabindex="0" role="button" class="flex items-center px-1.5 lg:px-3 py-2 md:hover:bg-primary-content rounded-lg duration-200 md:dark:hover:text-black cursor-pointer">
                        <span class="mr-1 lg:text-base text-sm">Usuarios</span>
                        <PhCaretDown weight="bold" class="md:size-4 size-4"/>
                    </div>
                    <ul tabindex="-1" class="dropdown-content menu bg-base-200 rounded-box z-1 w-40 p-2 shadow-sm">
                        <li><Link :href="route('manage-account.index')" class="block">Lista de usuarios</Link></li>
                        <li v-if="can('create projects')"><Link :href="route('manage-account.create')" class="block">Nuevo usuarios</Link></li>
                    </ul>
                </div>

                <!--Menu para vista proyectos-->
                <div class="dropdown md:dropdown-hover relative">
                    <div tabindex="0" role="button" class="flex items-center px-1.5 lg:px-3 py-2 md:hover:bg-primary-content rounded-lg duration-200 md:dark:hover:text-black cursor-pointer">
                        <span class="mr-1 lg:text-base text-sm">Proyectos</span>
                        <PhCaretDown weight="bold" class="md:size-4 size-4"/>
                    </div>
                    <ul tabindex="-1" class="dropdown-content menu bg-base-200 rounded-box z-1 w-40 p-2 shadow-sm">
                        <li><Link :href="route('projects.index')" class="block">Lista de proyectos</Link></li>
                        <li v-if="can('create projects')"><Link :href="route('projects.create')" class="block">Nuevo proyectos</Link></li>
                    </ul>
                </div>

                <!-- Empresa -->
                <div class="md:hover:bg-primary-content rounded-lg hover:duration-200 duration-200 md:dark:hover:text-black">
                    <Link :href="route('companies.redirect')" class="px-1.5 lg:px-3 py-2 block lg:text-base text-sm">Empresa</Link>
                </div>

                <!-- Clientes -->
                <div v-if="hasAnyRole(['admin', 'manager'])" class="md:hover:bg-primary-content rounded-lg hover:duration-200 duration-200 md:dark:hover:text-black">
                    <Link :href="route('clients.index')" class="px-1.5 lg:px-3 py-2 block lg:text-base text-sm">Clientes</Link>
                </div>
            </div>

                <!-- Botones de usuario -->
                <div class="hidden md:flex items-center justify-between ml-auto gap-0.5">
                    <!--Botón de temo claro y oscuro-->
                    <button class="btn btn-square border-0 bg-transparent hover:bg-base-200 hover:duration-300 duration-200 shadow-none" @click="toggleTheme">
                        <span v-if="theme === 'daylight'">
                            <PhMoonStars :size="24" color="#003566"/>
                        </span>
                        <span v-else>
                            <PhSunHorizon :size="24" color="#FFC300"/>
                        </span>
                    </button>

                    <!-- Botón de notificaciones -->
                    <button v-if="user" class="btn btn-square border-0 bg-transparent hover:bg-base-200 hover:duration-300 duration-200 shadow-none">
                        <Link :href="route('notifications.index')" class="flex items-center gap-4">
                            <div class="relative pr-2 py-2">
                                <PhBell class="text-neutral md:size-6 size-5" weight="duotone"/>
                                <div v-if="notificationCount" class="absolute right-0 top-0 w-4 h-4 bg-[#f94144] text-white font-black rounded-full text-xs text-center">
                                    {{ notificationCount }}
                                </div>
                            </div>
                        </Link>
                    </button>

                <!--Botón de usuario-->
                    <div class="flex items-center justify-between">
                        <button class="btn btn-circle border-0 bg-transparent shadow-none">
                            <PhUserCircle weight="duotone" class="text-neutral size-6"/>
                        </button>
                        <span v-if="isAuthenticated" class="cursor-default whitespace-nowrap">{{ user.name }}</span>
                    </div>

                <!--Menu desplegable de usuario-->
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="flex items-center px-3 py-2">
                            <PhCaretDown class="text-neutral cursor-pointer size-5"/>
                        </div>
                        <ul tabindex="-1" class="dropdown-content menu bg-base-100 rounded-box z-1 w-42 p-2 shadow-sm">
                            <li>
                                <Link class="flex items-center">
                                    <PhGear class="size-5 text-base-content" :weight="bold"/>
                                    <span>Ajustes</span>
                                </Link>
                            </li>
                            <li>
                                <Link class="flex items-center" :href="route('logout')" method="delete" as="button">
                                    <PhSignOut class="size-5 text-error" :weight="bold"/>
                                    <span>Cerrar sesión</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
        </div>
    </nav>

    <slot></slot>
</template>

<script setup>
import { Link} from '@inertiajs/vue3';
import { ref, watchEffect} from 'vue';
import { route } from 'ziggy-js';
import { PhMoonStars, PhSunHorizon, PhUserCircle, PhCaretDown, PhGear, PhSignOut, PhBell } from '@phosphor-icons/vue';
import { usePermission } from '../../composables/usePermission';
import { useNotification } from '../../composables/UseNotification';

//Comprobar permisos de usuario
const {hasRole, isAuthenticated, user, can, hasAnyRole} = usePermission();
const {notificationCount } = useNotification()


//Botón de menu
const menuOpen = ref(false)
function toggleMenu() {
    menuOpen.value = !menuOpen.value
}

//Botón de tema claro y oscuro
const theme = ref(localStorage.getItem('theme') || 'daylight')

watchEffect(() => {
    document.documentElement.setAttribute('data-theme', theme.value)
    localStorage.setItem('theme', theme.value)
})

function toggleTheme() {
    theme.value = theme.value === 'daylight' ? 'moonlight' : 'daylight'
}
</script>
