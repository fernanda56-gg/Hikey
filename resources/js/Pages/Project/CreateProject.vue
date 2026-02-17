<template>
    <AppLayout>
        <!--Contenedor global-->
        <div class="text-neutral container mx-auto p-4">
            <!--Titulo de pagina-->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-6xl text-2xl md:m-10 m-4 p-2">nuevo proyecto</h1>
            </div>

            <!-- Link breadcrumbs -->
            <div class="breadcrumbs p-4 text-xs md:text-sm">
                <ul>
                    <li><Link :href="route('inicio')"><PhHouseLine class="md:size-6 size-5 cursor-pointer hover:text-success duration-200 hover:duration-200" weight="duotone" /></Link></li>
                    <li><Link :href="route('projects.index')" class="hover:text-success duration-200 hover:duration-200 font-semibold">Proyectos</Link></li>
                    <li>Nuevo proyecto</li>
                </ul>
            </div>

            <!--Form de proyectos-->
            <form @submit.prevent="create">
                <div class="md:p-4">
                    <BoxComponent>
                        <!-- Nombre del proyecto -->
                        <div class="flex flex-col space-y-2">
                            <label class="font-bold text-neutral">Nombre de proyecto</label>
                            <input v-model="form.name" type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50" placeholder="Gestor de proyectos"/>
                            <!--Contenedor de error en input-->
                            <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.name">
                                <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- Descripción del proyecto -->
                        <div class="flex flex-col space-y-2 mt-4">
                            <label class="font-bold text-neutral">Descripción del proyecto</label>
                            <textarea v-model="form.description" class="textarea w-full rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 text-base border-none" placeholder="Descripción"></textarea>
                        <!--Contenedor de error en input-->
                            <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.description">
                                <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                {{ form.errors.description }}
                            </div>
                        </div>

                        <!-- Link de material de apoyo de proyecto -->
                        <div class="flex flex-col space-y-2 mt-4">
                            <label class="font-bold text-neutral">Link del proyecto</label>
                            <input v-model="form.link" type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50" placeholder="Url de recursos"/>
                            <!--Contenedor de error en input-->
                            <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.link">
                                <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                {{ form.errors.link }}
                            </div>
                        </div>

                        <div class="flex flex-col space-y-2 mt-4">
                            <label class="font-bold text-neutral">Link de img</label>
                            <input v-model="form.image_path" type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50" placeholder="Url de img"/>
                            <!--Contenedor de error en input-->
                            <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.image_path">
                                <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                {{ form.errors.image_path }}
                            </div>
                        </div>

                        <!-- Select de area para el proyecto -->
                        <div class="flex flex-col space-y-2 mt-4">
                            <label class="font-bold text-neutral">Área del proyecto</label>
                            <select v-model="form.area_id" class="select select-ghost bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">
                                <option disabled selected>Selecciona un área</option>
                                <option v-for="area in areas" :key="area.id" :value="area.id">{{ area.name }}</option>
                            </select>
                            <!--Contenedor de error en input-->
                            <div class="flex items-center justify-start text-error text-xs" v-if="form.errors.area_id">
                                <PhWarningCircle class="mx-1 md:size-4 size-6" weight="bold"/>
                                    {{ form.errors.area_id }}
                            </div>
                        </div>

                        <!-- Fecha del proyecto -->
                        <div class="flex items-start space-x-4">
                            <div class="flex flex-col mt-4 space-y-2">
                                <label class="font-bold text-neutral">Inicio de proyecto</label>
                                <input v-model="form.start_date" type="date" class="p-2 rounded-lg text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 bg-base-100">
                                <!--Contenedor de error en input-->
                                <div class="flex items-center justify-start text-error text-xs" v-if="form.errors.start_date">
                                    <PhWarningCircle class="mx-1 md:size-4 size-6" weight="bold"/>
                                    {{ form.errors.start_date }}
                                </div>
                            </div>

                            <!-- Select de seleccionar empresa solo para ADMIN -->
                            <div v-if="hasRole('admin')" class="flex flex-col space-y-2 mt-4 md:w-1/4 w-auto">
                                <label class="font-bold text-neutral">Empresa</label>
                                    <select v-model="form.company_id" class="select select-ghost bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">
                                        <option disabled selected>Selecciona una empresa</option>
                                        <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
                                    </select>
                                <!--Contenedor de error en input-->
                                <div class="flex items-center justify-start text-error text-xs" v-if="form.errors.company_id">
                                    <PhWarningCircle class="mx-1 md:size-4 size-6" weight="bold"/>
                                        {{ form.errors.company_id }}
                                </div>
                            </div>
                        </div>
                        <!--Botón de crear proyecto-->
                        <div class="flex items-center justify-center mt-8">
                            <button type="submit" class="bg-primary-content w-full p-3 rounded-lg text-black font-bold cursor-pointer tracking-wide">Generar proyecto</button>
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
import { PhWarningCircle, PhHouseLine } from '@phosphor-icons/vue';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { usePermission } from '../../composables/usePermission';
import { Link } from '@inertiajs/vue3';

//Comprobar permisos de usuario
const {hasRole} = usePermission();

defineProps({
    areas: Object,
    companies: Object
})

const form = useForm(
    {
        name: null,
        description: null,
        link: null,
        image_path: null,
        start_date:null,
        end_date: null,
        status: 'planned',
        area_id: null,
        company_id: null,
    })

const create = () => form.post(route('projects.store'))
</script>
