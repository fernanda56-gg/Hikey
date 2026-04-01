<template>
    <AppLayout>
    <!--Contenedor global-->
        <div class="text-neutral min-h-screen mx-auto flex items-start justify-center">
            <div class="container py-10 px-4 w-120">
                <!--Titulo de página-->
                <div class="flex justify-between items-center">
                    <h1 class="font-bold text-2xl">Verifica tu cuenta de correo electrónico</h1>
                </div>

                <!-- Información -->
                <div class="flex items-center text-justify">
                    <span class="py-2 tracking-wide md:text-base text-sm">Hemos enviado un enlace de verificación a tu correo electrónico. Por favor, revisa tu bandeja de entrada y haz click en el enlace para verificar.</span>
                </div>

                <div class="flex items-center text-justify">
                    <span class="py-2 tracking-wide md:text-base text-sm">Si aún no has recibido el correo, puedes solicitar un nuevo enlace de verificación.</span>
                </div>

                <!-- Botón para reenviar el enlace -->
                <div class="flex items-center mt-6 md:justify-end justify-center mb-3">
                    <Link :href="route('verification.send')" method="post" as="button" class="rounded-lg bg-primary px-5 py-2 cursor-pointer text-black font-semibold duration-200 hover:bg-primary-content hover:duration-200">Reenviar enlace</Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
</script>
