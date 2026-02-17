<template>
    <AppLayout>
        <!--Contenedor global-->
        <div class="text-neutral min-h-screen mx-auto flex items-start justify-center">
            <div class="container py-8 px-4 w-120">
                <!--Titulo de página-->
                <div class="flex justify-between items-center">
                    <h1 class="font-bold text-2xl">Unirse a una empresa</h1>
                </div>

                <div class="flex items-center">
                    <span class="py-1 tracking-wide">Ingresa el código de invitación para unirte.</span>
                </div>

                <form @submit.prevent="check">
                    <!--Input y botón para código de invitación-->
                    <div class="flex items-center mt-4 border-b-2 border-neutral pb-2 w-100 focus-within:border-secondary duration-200">
                        <input v-model="form.code" type="text" class="bg-transparent w-full focus:outline-none placeholder-shown:text-neutral/50" placeholder="Ej. XK7J2M9PZ8">
                        <!--Contenedor de error en input-->
                        <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.code">
                            <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                            {{ form.errors.code }}
                        </div>
                    </div>

                    <div class="flex items-center mt-6 justify-end mb-3">
                        <button type="submit" class="rounded-lg bg-primary px-5 py-2 cursor-pointer text-black font-bold duration-200 hover:bg-primary-content hover:duration-200">Unirse</button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '../../Layouts/AppLayout.vue';
import { PhWarningCircle} from '@phosphor-icons/vue';
import { route } from 'ziggy-js';

const form = useForm({
    code: null,
});

const check = () => form.post(route('companies.checkCode'));


</script>
