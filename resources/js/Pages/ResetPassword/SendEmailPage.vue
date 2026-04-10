<template>
    <!--Contenedor global-->
    <div class="flex flex-col md:flex-row bg-base-100 min-h-screen w-full">
        <!--Contenedor de imagen-->
        <div class="hidden md:flex md:w-1/2 h-screen items-center justify-center relative overflow-hidden">
            <img src="images/team-2.jpeg" alt="team work" class="w-full h-full object-cover ">
        </div>

        <!--Contenedor de enviar correo de restablecer-->
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
                <h1 class="md:text-3xl text-2xl font-bold mb-4 text-neutral">Correo de restablecimiento de contraseña</h1>
            </div>

            <!--Form de login-->
            <form @submit.prevent="sendEmail">
                <!--Correo-->
                <div class="mb-6 m-4">
                    <div class="flex items-center gap-2 bg-transparent border-b border-b-gray-700 py-2">
                        <PhAt weight="regular" class="text-neutral size-6"/>
                        <input id="email" v-model="form.email" type="text" placeholder="Ingresa tu correo electrónico" class="w-full bg-transparent focus:outline-none placeholder-shown:text-neutral/50 md:text-base text-sm text-neutral"/>
                    </div>
                    <!--Contenedor de error-->
                    <div class="flex items-center justify-start text-error text-xs m-2" v-if="form.errors.email">
                        <PhWarningCircle class="mx-1 size-4" weight="bold" />
                        {{ form.errors.email }}
                    </div>
                </div>

                <!--Botón de iniciar sesión-->
                <div class="py-2 flex items-center justify-center">
                    <button type="submit" class="bg-primary w-full p-4 font-bold uppercase rounded-lg m-4 cursor-pointer text-black hover:bg-primary-content hover:duration-200 duration-200">enviar correo</button>
                </div>
            </form>
        </div>
    </div>
</template>
<script setup>
import { PhAt, PhWarningCircle } from '@phosphor-icons/vue';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const form = useForm(
    {
        email: null,
    })

const sendEmail = () => form.post(route('password.email'))
</script>

