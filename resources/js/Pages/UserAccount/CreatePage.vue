<template>
    <!--Contenedor global-->
    <div class="flex flex-col md:flex-row bg-base-100 min-h-screen w-full">
        <!--Contenedor de imagen-->
        <div class="hidden md:flex md:w-2/5 h-screen items-center justify-center relative overflow-hidden">
            <img src="/images/team-1.jpeg" alt="team work" class="w-full h-full object-cover ">
        </div>

        <!--Contenedor de form de registro de usuarios-->
        <div class="md:w-3/5 md:m-16 w-full">
            <!--Logo-->
            <div class="flex items-center justify-between mb-8 mt-2">
                <div class="flex items-center gap-1">
                    <img src="/images/logo.png" alt="logo" class="md:w-10 w-8 m-2">
                    <span class="font-sans font-bold uppercase flex py-5 md:text-2xl text-xl text-neutral">hikey</span>
                </div>
            </div>

            <!--Mensaje-->
            <div class="mb-8 md:mt-20 mt-12 m-2">
                <h1 class="md:text-3xl text-2xl font-bold mb-2 text-neutral">Crear cuenta</h1>
                <span class="text-neutral mb-4 md:text-base text-sm">Regístrate para comenzar a gestionar tus proyectos y actividades en HIKEY.</span>
            </div>

            <!--Form de registro de usuarios-->
            <form @submit.prevent="register">
                <!--Nombre de usuario-->
                <div class="md:mb-6 m-4">
                    <div class="flex flex-col md:flex-row items-center gap-1.5">
                        <!--Nombre-->
                        <div class="md:w-1/2 w-full">
                            <div class="flex items-center gap-2 bg-transparent border-b border-b-gray-700 py-2">
                                <PhUser weight="regular" class="text-neutral size-6"/>
                                <input id="name" v-model="form.name" type="text" placeholder="Ingresa tu nombre" class="w-full bg-transparent focus:outline-none placeholder-shown:text-neutral/50 md:text-base text-sm text-neutral"/>
                            </div>
                            <!--Contenedor de error-->
                            <div class="flex items-center justify-start text-error text-xs m-2" v-if="form.errors.name">
                                <PhWarningCircle class="mx-1 size-4" weight="bold" />
                                {{form.errors.name}}
                            </div>
                        </div>

                        <!--Apellidos-->
                        <div class="md:w-1/2 w-full mt-2 md:mt-0">
                            <div class="flex items-center gap-2 bg-transparent border-b border-b-gray-700 py-2">
                                <PhUser weight="regular" class="text-neutral size-6 md:hidden"/>
                                <input id="last_name" v-model="form.last_name" type="text" placeholder="Ingresa tus apellidos" class="w-full bg-transparent focus:outline-none placeholder-shown:text-neutral/50 md:text-base text-sm text-neutral"/>
                            </div>
                            <!--Contenedor de error-->
                            <div class="flex items-center justify-start text-error text-xs m-2" v-if="form.errors.last_name">
                                <PhWarningCircle class="mx-1 size-4" weight="bold" />
                                {{ form.errors.last_name }}
                            </div>
                        </div>
                    </div>
                </div>

                <!--Correo-->
                <div class="mb-6 m-4 mt-2 md:mt-0">
                    <div class="flex items-center gap-2 bg-transparent border-b border-b-gray-700 py-2">
                        <PhAt weight="regular" class="text-neutral size-6"/>
                        <input id="email" v-model="form.email" type="text" placeholder="Ingresa tu correo electrónico" class="w-full bg-transparent focus:outline-none placeholder-shown:text-neutral/50 md:text-base text-sm text-neutral"/>
                    </div>
                    <!--Contenedor de error-->
                    <div class="flex items-center justify-start text-error text-xs m-2" v-if="form.errors.email">
                        <PhWarningCircle class="mx-1 size-4" weight="bold" />
                        {{form.errors.email}}
                    </div>
                </div>

                <!--Input de contraseñas-->
                <div class="mb-6 m-4">
                    <div class="flex flex-col md:flex-row items-center gap-1.5">
                        <!--Contraseña-->
                        <div class="md:w-1/2 w-full">
                            <div class="flex items-center gap-2 bg-transparent border-b border-b-gray-700 py-2">
                                <PhLockKey class="text-neutral size-6"/>
                                <input id="password" v-model="form.password" type="password" placeholder="Ingresa tu contraseña" class="w-full bg-transparent focus:outline-none placeholder-shown:text-neutral/50 md:text-base text-sm text-neutral"/>
                            </div>
                    <!--Contenedor de error-->
                            <div class="flex items-center justify-start text-error text-xs m-2" v-if="form.errors.password">
                                <PhWarningCircle class="mx-1 size-4" weight="bold" />
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <!--Confirmar contraseña-->
                        <div class="md:w-1/2 w-full mt-2 md:mt-0">
                            <div class="flex items-center gap-2 bg-transparent border-b border-b-gray-700 py-2">
                                <PhLockKey class="text-neutral size-6"/>
                                <input id="password_confirmation" v-model="form.password_confirmation" type="password" placeholder="Confirma tu contraseña" class="w-full bg-transparent focus:outline-none placeholder-shown:text-neutral/50 md:text-base text-sm text-neutral"/>
                            </div>
                    <!--Contenedor de error-->
                            <div class="flex items-center justify-start text-error text-xs m-2" v-if="form.errors.password">
                                <PhWarningCircle class="mx-1 size-4" weight="bold" />
                                {{ form.errors.password }}
                            </div>
                        </div>
                    </div>
                </div>

                <!--Link a inicio de sesión-->
                <div class="md:mb-6 m-4">
                    <Link class="flex items-center justify-end gap-1 bg-transparent cursor-pointer" :href="route('login')">
                        <span class="text-xs text-neutral border-b hover:text-blue-600 hover:duration-200 duration-200">Ya tienes cuenta, inicia sesión</span>
                        <PhArrowRight class="text-neutral size-4"/>
                    </Link>
                </div>

                <!--Botón de registrar usuario-->
                <div class="mt-16 flex items-center justify-center">
                    <button type="submit" class="bg-primary w-full p-4 font-bold uppercase rounded-lg m-4 cursor-pointer text-black hover:bg-primary-content hover:duration-200 duration-200">crear cuenta</button>
                </div>

            </form>
        </div>
    </div>
</template>
<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { PhUser, PhWarningCircle, PhAt, PhLockKey, PhArrowRight} from '@phosphor-icons/vue';
import { route } from 'ziggy-js';

const form = useForm({
    name: null,
    last_name: null,
    email: null,
    password: null,
    password_confirmation: null,
})

const register = () => form.post(route('register.store'));
</script>
