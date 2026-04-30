<template>
    <AppLayout>
        <!-- Contenedor global -->
        <div class="text-neutral container mx-auto p-4">
            <!-- Titulo de la pagina -->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-6xl text-2xl md:m-10 m-4 p-2">editar cliente</h1>
            </div>

            <!-- Link breadcrumbs -->
            <div class="breadcrumbs px-4 py-1.5 text-xs md:text-sm">
                <ul>
                    <li><Link :href="route('inicio')"><PhHouseLine class="md:size-6 size-5 cursor-pointer hover:text-success duration-200 hover:duration-200" weight="duotone" /></Link></li>
                    <li v-if="hasAnyRole(['admin', 'manager'])"><Link :href="route('clients.index')" class="hover:text-success duration-200 hover:duration-200 font-semibold">Clientes</Link></li>
                    <li>Editar cliente</li>
                </ul>
            </div>

            <!-- Form para actualizar info de cliente -->
            <form @submit.prevent="update">
                <div class="md:p-4">
                    <BoxComponent>
                        <!-- Nombre del cliente -->
                        <div class="flex flex-col space-y-2">
                            <label class="font-black text-neutral">Nombre de cliente</label>
                            <input v-model="form.name" type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50" placeholder="Óscar Sanchez Salazar"/>
                            <!--Contenedor de error en input-->
                            <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.name">
                                <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- Correo y teléfono -->
                        <div class="flex flex-col md:flex-row items-center md:space-x-4 mt-4">
                            <div class="flex flex-col space-y-2 w-full mt-4 md:mt-0">
                                <label class="font-black text-neutral">Correo electrónico</label>
                                <input v-model="form.email"  type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50" placeholder="oscarkaxawat980@icubik.com"/>
                                <!--Contenedor de error en input-->
                                    <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.email">
                                        <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                        {{ form.errors.email }}
                                    </div>
                            </div>

                            <div class="flex flex-col space-y-2 w-full mt-4 md:mt-0">
                                <label class="font-black text-neutral">Teléfono</label>
                                <input v-model="form.phone"  type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50" placeholder="+19707840311"/>
                                <!--Contenedor de error en input-->
                                    <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.phone">
                                        <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                        {{ form.errors.phone }}
                                    </div>
                            </div>
                        </div>

                        <!--Botón de crear cliente-->
                        <div class="flex items-center justify-center mt-8">
                            <button type="submit" class="bg-primary-content w-full p-3 rounded-lg text-black font-black cursor-pointer tracking-wide">Actualizar cliente</button>
                        </div>
                    </BoxComponent>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import BoxComponent from '../../Components/UI/BoxComponent.vue';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { PhHouseLine } from '@phosphor-icons/vue';
import { Link } from '@inertiajs/vue3';
import { usePermission } from '../../composables/usePermission';

//Comprobar permisos de usuario
const {hasAnyRole} = usePermission();

const props = defineProps({
    client: Object,
})

const form = useForm({
    name: props.client.name,
    email: props.client.email,
    phone: props.client.phone,
})

const update = () => form.put(route('clients.update', {client: props.client.id}))
</script>
