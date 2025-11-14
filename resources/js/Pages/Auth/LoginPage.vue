<template>
    <!--Contenedor global-->
    <div class="flex flex-col md:flex-row bg-base-100 min-h-screen w-full">
        <!--Contenedor de imagen-->
        <div class="hidden md:flex md:w-1/2 h-screen items-center justify-center relative overflow-hidden">
            <img src="images/team-2.jpeg" alt="team work" class="w-full h-full object-cover ">
        </div>

        <!--Contenedor de login form-->
        <div class="md:w-1/2 md:m-16 w-full">
            <!--Logo-->
            <div class="flex items-center justify-between mb-8 mt-2">
                <div class="flex items-center gap-1">
                    <img src="/images/logo.png" class="md:w-10 w-8 m-2">
                    <span class="font-sans font-bold uppercase flex py-5 md:text-2xl text-xl text-neutral">hikey</span>
                </div>
            </div>

            <!--Mensaje-->
            <div class="mb-8 md:mt-20 mt-12 m-2">
                <h1 class="md:text-3xl text-2xl font-bold mb-4 text-neutral">Inicia sesión en tu cuenta</h1>
            </div>

            <!--Form de login-->
            <form @submit.prevent="login">
                <!--Correo-->
                <div class="mb-6 m-4">
                    <div class="flex items-center gap-2 bg-transparent border-b border-b-gray-700 py-2">
                        <PhAt weight="regular" class="text-neutral size-6"/>
                        <input id="email" v-model="form.email" type="text" placeholder="Ingresa tu correo electrónico" class="w-full bg-transparent focus:outline-none placeholder-shown:text-neutral/50 md:text-base text-sm text-neutral"/>
                    </div>
                    <!--Contenedor de error-->
                    <div class="flex items-center justify-start text-error text-xs m-2">
                        <PhWarningCircle class="mx-1 size-4" weight="bold" />
                        error
                    </div>
                </div>

                <!--Contraseña-->
                <div class="mb-6 m-4">
                    <div class="flex items-center gap-2 bg-transparent border-b border-b-gray-700 py-2">
                        <PhLockKey class="text-neutral size-6"/>
                        <input id="password" v-model="form.password" type="password" placeholder="Ingresa tu contraseña" class="w-full bg-transparent focus:outline-none placeholder-shown:text-neutral/50 md:text-base text-sm text-neutral"/>
                    </div>
                </div>

                <!--Link a registro de usuario-->
                <div class="mb-6 m-4">
                    <Link class="flex items-center gap-1 bg-transparent cursor-pointer">
                        <PhArrowLeft class="text-neutral size-4"/>
                        <span class="text-xs text-neutral border-b hover:text-blue-600 hover:duration-200 duration-200">¿Aún no tienes cuenta? Regístrate!</span>
                    </Link>

                </div>

                <!--Botón de iniciar sesión-->
                <div class="mt-16 flex items-center justify-center">
                    <button type="submit" class="bg-primary w-full p-4 font-bold uppercase rounded-lg m-4 cursor-pointer text-black">iniciar sesión</button>
                </div>
            </form>
        </div>
    </div>
</template>
<script setup>
import { PhArrowLeft, PhAt, PhLockKey, PhWarningCircle } from '@phosphor-icons/vue';
import { Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const form = useForm (
    {
        email: null,
        password: null
    }
)

const login = () => form.post(route('login.store'))
</script>
