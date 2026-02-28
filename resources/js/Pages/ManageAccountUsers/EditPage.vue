<template>
    <AppLayout>
        <!--Contenedor global-->
        <div class="container text-neutral mx-auto p-4">
            <!--Titulo de pagina-->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-6xl text-2xl md:m-10 m-4 p-2">editar usuario</h1>
            </div>

            <!-- Link breadcrumbs -->
            <div class="breadcrumbs px-4 py-1.5 text-xs md:text-sm">
                <ul>
                    <li><Link :href="route('inicio')"><PhHouseLine class="md:size-6 size-5 cursor-pointer hover:text-success duration-200 hover:duration-200" weight="duotone" /></Link></li>
                    <li v-if="hasRole('admin')"><Link :href="route('manage-account.index')" class="hover:text-success duration-200 hover:duration-200 font-semibold">Usuarios</Link></li>
                    <li>Editar usuario</li>
                </ul>
            </div>

            <!--Form de usuario-->
            <form @submit.prevent="update">
                <div class="md:p-4">
                    <BoxComponent>
                        <!--Nombre y apellidos-->
                        <div class="flex flex-col space-y-2">
                            <label class="font-bold text-neutral">Nombre</label>
                            <input type="text" v-model="form.name" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50" placeholder="Silvia Maria">
                            <!--Contenedor de error en input-->
                            <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.name">
                                <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <div class="flex flex-col space-y-2 mt-4">
                            <label class="font-bold text-neutral">Apellidos</label>
                            <input type="text" v-model="form.last_name" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50" placeholder="Calderon Becerra">
                            <!--Contenedor de error en input-->
                            <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.last_name">
                                <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                {{ form.errors.last_name }}
                            </div>
                        </div>

                        <!--Correo-->
                        <div class="flex flex-col space-y-2 mt-4">
                            <label class="font-bold text-neutral">Correo</label>
                            <input type="text" v-model="form.email" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50" placeholder="silvia@example.com">
                            <!--Contenedor de error en input-->
                            <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.email">
                                <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <!--Rol de usuario-->
                        <div class="flex flex-col mt-4">
                            <label class="font-bold text-neutral">Rol</label>
                        <!-- Checkboxes de rol-->
                            <div class="flex flex-row space-x-4 mt-2">
                                <div class="flex space-x-2" v-for="role in props.roles" :key="role.id">
                                    <input type="checkbox" v-model="form.roles" :value="role.id" class="checkbox checkbox-success border border-gray-500"/>
                                    <label for="role.name" class="text-sm capitalize">{{ role.name }}</label>
                                </div>
                            </div>

                            <!-- Contenedor de error en input-->
                            <div class="flex items-center justify-start text-xs text-error mt-2" v-if="form.errors.roles">
                                <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                {{ form.errors.roles }}
                            </div>
                        </div>

                        <!--Botón de actualizar usuario-->
                        <div class="flex items-center justify-center mt-8">
                            <button type="submit" class="bg-primary-content w-full p-3 rounded-lg text-black font-bold cursor-pointer tracking-wide">Editar usuario</button>
                        </div>
                    </BoxComponent>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { route } from 'ziggy-js';
import BoxComponent from '../../Components/UI/BoxComponent.vue';
import { PhWarningCircle, PhHouseLine } from '@phosphor-icons/vue';
import { useForm, Link } from '@inertiajs/vue3';
import { usePermission } from '../../composables/usePermission';

//Comprobar permisos de usuario
const {hasRole} = usePermission();


const props = defineProps({
    'userAccount': Object,
    'roles': Array,
    'userRoles': Array
})

const form = useForm(
    {
    name: props.userAccount.name,
    last_name: props.userAccount.last_name,
    email: props.userAccount.email,
    roles: props.userRoles ?? [],
})

const update = () => form.put(route('manage-account.update', props.userAccount.id));

</script>
