<template>
    <AppLayout>
        <!-- Contenedor global -->
        <div class="text-neutral container mx-auto p-4">
            <!-- Titulo de pagina -->
            <h1 class="text-neutral uppercase font-bold md:text-5xl text-2xl md:m-10 m-4 p-2">información de cuenta</h1>

            <!-- ! Contendedor de img de usuario -->
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

            <!-- Pestañas de ajustes de cuenta -->
            <div class="tabs tabs-lift mt-6 text-neutral">

                <!-- * info de cuenta de usuario -->

                <label class="tab bg-base-100 has-checked:bg-base-200 border-0 has-checked:border-2 hover:duration-200 duration-200 border-base-300">
                    <input type="radio" name="my_tabs_4" checked="checked"/>
                    <PhUserFocus class="size-6 md:size-5 mx-0.5" />
                    <span class="font-black uppercase text-xs md:text-sm">Información de usuario</span>
                </label>

                <!--  ! CONTENEDOR DE INFO DE USUARIO -->
                <div class="tab-content bg-base-200 border-base-300 p-6 border-2">
                    <form @submit.prevent="update">

                        <!-- nombre y apellido -->
                        <div class="flex flex-row items-start md:space-x-4 space-x-2">
                            <div class="flex flex-col space-y-2 w-1/2">
                                <label class="font-bold text-neutral md:text-base text-sm">Nombre</label>
                                <input v-model="form.name" type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50" />
                                <!--Contenedor de error en input-->
                                <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.name">
                                    <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                        {{ form.errors.name }}
                                </div>
                            </div>

                            <div class="flex flex-col space-y-2 w-1/2">
                                <label class="font-bold text-neutral md:text-base text-sm">Apellido</label>
                                <input v-model="form.last_name" type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50" />
                                <!--Contenedor de error en input-->
                                <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.last_name">
                                    <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                        {{ form.errors.last_name }}
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
                                        {{ form.errors.email }}
                                </div>
                            </div>
                        </div>

                        <!-- ? Botón de actualización y borrar cuenta-->
                        <div class="flex items-center mt-8 gap-2 w-full">
                            <button type="submit" class=" btn bg-primary-content btn-sm p-2 border-0 rounded-lg text-black text-sm font-bold cursor-pointer tracking-wide">
                                Actualizar
                            </button>

                            <button @click="resetValues" class="btn p-2 rounded-lg btn-sm btn-outline border-2 hover:bg-neutral-300 text-neural text-sm font-bold cursor-pointer tracking-wide">
                                Cancelar
                            </button>

                            <button type="button" class="btn btn-sm ml-auto btn-error outline-none border-0 hover:duration-200 duration-200 hover:bg-red-700 p-2 rounded-lg text-white text-sm font-semibold cursor-pointer tracking-wide" onclick="my_modal_1.showModal()" @click="deleteAc.reset()">Borrar cuenta</button>
                        </div>
                    </form>

                    <!-- ! Modal para borrar cuenta -->
                    <div class="flex flex-row items-start md:space-x-4 space-x-2 mt-3">
                        <dialog id="my_modal_1" class="modal">
                            <div class="modal-box">

                                <!-- *contenedor de contraseña -->
                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend text-error">Ingresa tu contraseña para eliminar tu cuenta</legend>
                                    <input v-model="deleteAcc.current_password" type="password" class="input outline-none text-neutral border-neutral" placeholder="Contraseña" />
                                    <div class="label text-red-600" v-if="deleteAcc.errors.current_password">
                                        <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                            {{ deleteAcc.errors.current_password }}
                                    </div>
                                </fieldset>

                                <!-- *botones del modal -->
                                <div class="modal-action">
                                    <form @submit.prevent="deleteAccount">
                                        <!-- botón de cancelar -->
                                        <button class="btn btn-primary text-black">Borrar cuenta</button>
                                    </form>

                                    <form method="dialog">
                                        <!-- botón de cancelar -->
                                        <button class="btn btn-error text-white" @click="deleteAcc.reset()">Cancelar</button>
                                    </form>
                                </div>
                            </div>
                        </dialog>
                    </div>
                </div>

                <!-- *cambiar contraseña -->

                <label class="tab bg-base-100 has-checked:bg-base-200 border-0 has-checked:border-2 hover:duration-200 duration-200 border-base-300">
                    <input type="radio" name="my_tabs_4"/>
                    <PhPassword class="size-6 md:size-5 mx-1" />
                    <span class="font-black uppercase text-xs md:text-sm">Cambiar contraseña</span>
                </label>

                <!-- ! CONTENEDOR DE CAMBIAR CONTRASEÑA -->
                <div class="tab-content bg-base-200 border-base-300 p-6">
                    <form @submit.prevent="updatePassword">

                        <!-- Contraseña actual -->
                        <div class="flex flex-row items-start md:space-x-4 space-x-2">
                            <div class="flex flex-col space-y-2 w-full">
                                <label class="font-bold text-neutral md:text-base text-sm">Contraseña actual</label>
                                <div class="relative w-full">
                                    <input :type="inputType.current_password ? 'text' : 'password'" class="bg-base-100 rounded-lg p-2 pr-10 text-neutral w-full focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50"/>
                                    <!-- botón para ocultar contraseña -->
                                    <button type="button" @click="showPass('current_password')" class="absolute right-2 top-1/2 -translate-y-1/2">
                                        <PhEyeClosed weight="duotone" v-if="!inputType.current_password" class="text-neutral size-5" />
                                        <PhEye weight="duotone" v-else class="text-neutral size-6" />
                                    </button>
                                </div>
                                <!--Contenedor de error en input-->
                                <div class="flex items-center justify-start text-xs text-error" v-if="passForm.errors.current_password">
                                    <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                    {{ passForm.errors.current_password }}
                                </div>
                            </div>
                        </div>

                        <!-- Contraseña nueva -->
                        <div class="flex flex-row items-start md:space-x-4 space-x-2 mt-3">
                            <div class="flex flex-col space-y-2 w-1/2">
                                <label class="font-bold text-neutral md:text-base text-sm">Nueva contraseña</label>
                                <div class="relative w-full">
                                    <input :type="inputType.password ? 'text' : 'password'" class="bg-base-100 rounded-lg p-2 pr-10 text-neutral w-full focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50"/>
                                    <!-- botón para ocultar contraseña -->
                                    <button type="button" @click="showPass('password')" class="absolute right-2 top-1/2 -translate-y-1/2">
                                        <PhEyeClosed weight="duotone" v-if="!inputType.password" class="text-neutral size-5" />
                                        <PhEye weight="duotone" v-else class="text-neutral size-6" />
                                    </button>
                                </div>
                                <!--Contenedor de error en input-->
                                <div class="flex items-center justify-start text-xs text-error" v-if="passForm.errors.password">
                                    <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                        {{ passForm.errors.password }}
                                </div>
                            </div>

                            <div class="flex flex-col space-y-2 w-1/2">
                                <label class="font-bold text-neutral md:text-base text-sm">Confirmar contraseña</label>
                                <div class="relative w-full">
                                    <input :type="inputType.password_confirmation ? 'text' : 'password'" class="bg-base-100 rounded-lg p-2 pr-10 text-neutral w-full focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50"/>
                                    <!-- botón para ocultar contraseña -->
                                    <button type="button" @click="showPass('password_confirmation')" class="absolute right-2 top-1/2 -translate-y-1/2">
                                        <PhEyeClosed weight="duotone" v-if="!inputType.password_confirmation" class="text-neutral size-5" />
                                        <PhEye weight="duotone" v-else class="text-neutral size-6" />
                                    </button>
                                </div>
                                <!--Contenedor de error en input-->
                                <div class="flex items-center justify-start text-xs text-error" v-if="passForm.errors.password_confirmation">
                                    <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                        {{ passForm.errors.password_confirmation }}
                                </div>
                            </div>
                        </div>

                        <!-- ! Botón para actualizar la contraseña -->
                        <div class="flex items-center mt-8 gap-2">
                            <button type="submit" class="btn bg-primary-content w-full p-2 rounded-lg text-black text-base font-bold cursor-pointer tracking-wide border-0">
                                Cambiar contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import BoxComponent from '../../Components/UI/BoxComponent.vue';
import { PhUploadSimple } from '@phosphor-icons/vue';
import { route } from 'ziggy-js';
import { useForm } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';
import { PhWarningCircle, PhUserFocus, PhPassword, PhEyeClosed, PhEye } from '@phosphor-icons/vue';

const props = defineProps({
    'user': Object,
})

/* para mostrar contraseña */
const inputType = reactive({
    current_password: false,
    password: false,
    password_confirmation: false,
});

const showPass = (field) => {
    inputType[field] = !inputType[field];
}

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

const passForm = useForm({
    'current_password': '',
    'password': '',
    'password_confirmation': '',
})

const deleteAcc = useForm({
    'current_password': '',
});

function storeProfilePhoto(event) {
    form['profile-photo'] = event.target.files[0]
    form.post(route('my-account.profile-photo', {user: props.user.id}), {
        forceFormData: true,
    })
}

const update = () => form.put(route('my-account.edit-account', {user: props.user.id}))

const updatePassword = () => passForm.put(route('my-account.update-password', {user: props.user.id}))

const deleteAccount = () => deleteAcc.delete(route('my-account.delete-account', {user: props.user.id}))
</script>


