<template>
    <AppLayout>
        <!-- Contenedor global -->
        <div class="text-neutral container mx-auto p-4">
            <!-- Titulo de pagina -->
            <h1 class="text-neutral uppercase font-bold md:text-5xl text-2xl md:m-10 m-4 p-2">información de cuenta</h1>

            <!-- Contendedor de img de usuario -->
            <BoxComponent>
                <h1 class="flex items-center pb-3 font-semibold md:text-xl text-base uppercase">Foto de perfil</h1>
                <div class="flex items-center gap-3">
                    <div class="avatar">
                        <div class="md:w-24 w-16 rounded">
                            <img :src="user.profile_photo_url ?? 'https://img.daisyui.com/images/profile/demo/batperson@192.webp'"  />
                        </div>
                    </div>

                    <div>
                        <!-- Input y botón para subir archivo el input es invisible -->
                        <input type="file" name="profile-photo" ref="fileInput" @change="storeProfilePhoto" class="hidden">

                        <button type="button" @click="fileInput.click()" :disabled="form.processing" class="btn btn-info text-white btn-sm md:btn-md border-0 shadow-none">
                            <span class="flex items-center md:space-x-1">
                                <PhUploadSimple class="md:size-5 size-4" weight="bold" />
                                <span class="font-black hidden md:flex">{{ form.processing ? 'Subiendo...' : 'Actualizar' }}</span>
                            </span>
                        </button>
                    </div>
                </div>
            </BoxComponent>

            <!-- Contenedor de info de usuario -->
            <BoxComponent class="mt-6">
                <h1 class="flex items-center pb-3 font-semibold md:text-xl text-base uppercase">información de usuario</h1>
                <form @submit.prevent="update">
                    <!-- nombre y apellido -->
                    <div class="flex flex-row items-start md:space-x-4 space-x-2">
                        <div class="flex flex-col space-y-2 w-1/2">
                            <label class="font-bold text-neutral md:text-base text-sm">Nombre</label>
                            <input v-model="form.name" type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50" />
                            <!--Contenedor de error en input-->
                            <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.name">
                                <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                    error
                            </div>
                        </div>

                        <div class="flex flex-col space-y-2 w-1/2">
                            <label class="font-bold text-neutral md:text-base text-sm">Apellido</label>
                            <input v-model="form.last_name" type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50" />
                            <!--Contenedor de error en input-->
                            <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.last_name">
                                <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                    error
                            </div>
                        </div>
                    </div>

                    <!-- correo -->
                    <div class="flex flex-row items-start md:space-x-4 space-x-2 mt-3">
                        <div class="flex flex-col space-y-2 w-1/2">
                            <label class="font-bold text-neutral md:text-base text-sm">Correo</label>
                            <input v-model="form.email" type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50" />
                            <!--Contenedor de error en input-->
                            <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.email">
                                <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                    error
                            </div>
                        </div>
                    </div>

                    <!--Botón de actualización, y borrado de cuenta-->
                    <div class="flex items-center mt-8 gap-2 w-full">
                        <button type="submit" class=" btn bg-primary-content btn-sm p-2 rounded-lg text-black text-sm font-bold cursor-pointer tracking-wide">
                            Actualizar
                        </button>

                        <button @click="resetValues" class="btn p-2 rounded-lg btn-sm btn-outline hover:bg-neutral-300 text-neural text-sm font-bold cursor-pointer tracking-wide">
                            Cancelar
                        </button>

                        <!-- Botón borrar -->
                        <Link :href="route('my-account.delete-account', {user: props.user.id})" method="delete" as="button" class="btn btn-sm ml-auto btn-error hover:duration-200 duration-200 hover:bg-red-700 p-2 rounded-lg text-white text-sm font-semibold cursor-pointer tracking-wide">
                            Borrar cuenta
                        </Link>
                    </div>
                </form>
            </BoxComponent>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import BoxComponent from '../../Components/UI/BoxComponent.vue';
import { PhUploadSimple } from '@phosphor-icons/vue';
import {Link} from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { PhWarningCircle } from '@phosphor-icons/vue';

const props = defineProps({
    'user': Object,
})

const fileInput = ref(null)

const originalValues = {
    'name': props.user.name,
    'last_name': props.user.last_name,
    'email': props.user.email,
}

const resetValues = () => {
    form.value = { originalValues }
}

const form = useForm({
    'profile-photo': null,
    'name': props.user.name,
    'last_name': props.user.last_name,
    'email': props.user.email,
})

function storeProfilePhoto(event) {
    form['profile-photo'] = event.target.files[0]
    form.post(route('my-account.profile-photo', {user: props.user.id}), {
        forceFormData: true,
    })
}

const update = () => form.put(route('my-account.edit-account', {user: props.user.id}))
</script>


