<template>
    <AppLayout>
        <!--Contenedor global-->
        <div class="text-neutral container mx-auto p-4">
            <!--Titulo de pagina-->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-6xl text-2xl md:m-10 m-4 p-2">nuevo proyecto</h1>
            </div>

            <!--Form de proyectos-->
            <form @submit.prevent="update">
                <div class="md:p-4">
                    <BoxComponent>
                        <!-- Nombre de proyecto -->
                        <div class="flex flex-col space-y-2">
                            <label class="font-bold text-neutral">Nombre de proyecto</label>
                            <input v-model="form.name" type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50" />
                            <!--Contenedor de error en input-->
                            <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.name">
                                <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- Descripción del proyecto -->
                        <div class="flex flex-col space-y-2 mt-4">
                            <label class="font-bold text-neutral">Descripción del proyecto</label>
                            <textarea v-model="form.description" class="textarea w-full rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 text-base border-none"></textarea>
                        <!--Contenedor de error en input-->
                            <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.description">
                                <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                {{ form.errors.description }}
                            </div>
                        </div>

                        <!-- Links de ayuda -->
                        <div class="flex flex-col space-y-2 mt-4">
                            <label class="font-bold text-neutral">Link del proyecto</label>
                            <input v-model="form.link" type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50"/>
                            <!--Contenedor de error en input-->
                            <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.link">
                                <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                {{ form.errors.link }}
                            </div>
                        </div>

                        <div class="flex flex-col space-y-2 mt-4">
                            <label class="font-bold text-neutral">Link de img</label>
                            <input v-model="form.image_path" type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50"/>
                            <!--Contenedor de error en input-->
                            <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.image_path">
                                <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                {{ form.errors.image_path }}
                            </div>
                        </div>

                        <!-- Select para área del proyecto -->
                        <div class="flex flex-col space-y-2 mt-4">
                            <label class="font-bold text-neutral">Área del proyecto</label>
                            <select v-model="form.area_id" class="select select-ghost bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">
                                <option disabled selected>Selecciona un área</option>
                                <option v-for="area in props.areas" :key="area.id" :value="area.id">{{ area.name }}</option>
                            </select>
                            <!--Contenedor de error en input-->
                            <div class="flex items-center justify-start text-error text-xs" v-if="form.errors.area_id">
                                <PhWarningCircle class="mx-1 md:size-4 size-6" weight="bold"/>
                                    {{ form.errors.area_id }}
                            </div>
                        </div>

                        <!-- Fechas del proyecto -->
                        <div class="flex items-center space-x-4">
                            <div class="flex flex-col mt-4 space-y-2">
                                <label class="font-bold text-neutral">Inicio de proyecto</label>
                                <input v-model="form.start_date" type="date" class="p-2 rounded-lg text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 bg-base-100">
                                <!--Contenedor de error en input-->
                                <div class="flex items-center justify-start text-error text-xs" v-if="form.errors.start_date">
                                    <PhWarningCircle class="mx-1 md:size-4 size-6" weight="bold"/>
                                    {{ form.errors.start_date }}
                                </div>
                            </div>

                            <div class="flex flex-col mt-4 space-y-2 md:mx-4">
                                <label class="font-bold text-neutral">Fin de proyecto</label>
                                <input v-model="form.end_date" type="date" class="p-2 rounded-lg text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 bg-base-100">
                                <!--Contenedor de error en input-->
                                <div class="flex items-center justify-start text-error text-xs" v-if="form.errors.end_date">
                                    <PhWarningCircle class="mx-1 md:size-4 size-6" weight="bold"/>
                                    {{ form.errors.end_date }}
                                </div>
                            </div>
                        </div>
                        <!--Botón de actualizar proyecto-->
                        <div class="flex items-center justify-center mt-8">
                            <button type="submit" class="bg-primary-content w-full p-3 rounded-lg text-black font-bold cursor-pointer tracking-wide">Actualizar proyecto</button>
                        </div>
                    </BoxComponent>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { route } from 'ziggy-js';
import AppLayout from '../../Layouts/AppLayout.vue';
import BoxComponent from '../../Components/UI/BoxComponent.vue';
import { PhWarningCircle } from '@phosphor-icons/vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    project: Object,
    areas: Object,
})

const form = useForm(
    {
        name: props.project.name,
        description: props.project.description,
        link: props.project.link,
        image_path: props.project.image_path,
        start_date: props.project.start_date,
        end_date: props.project.end_date,
        status: props.project.status,
        area_id: props.project.area_id,
    })

const update = () => form.put(route('projects.update', {project: props.project.id}))
</script>
