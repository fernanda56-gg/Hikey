<template>
    <AppLayout>
        <!--Contenedor global-->
        <div class="text-neutral container mx-auto p-4">
            <!--Titulo de pagina-->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-6xl text-2xl md:m-10 m-4 p-2">nueva empresa</h1>
            </div>

            <!-- Link breadcrumbs -->
            <div class="breadcrumbs px-4 py-1.5 text-xs md:text-sm">
                <ul>
                    <li><Link :href="route('inicio')"><PhHouseLine class="md:size-6 size-5 cursor-pointer hover:text-success duration-200 hover:duration-200" weight="duotone" /></Link></li>
                    <li><Link :href="route('companies.redirect')" class="hover:text-success duration-200 hover:duration-200 font-semibold">Empresa</Link></li>
                    <li>Agregar nueva empresa</li>
                </ul>
            </div>

            <!--Form de empresas-->
            <form @submit.prevent="create">
                <div class="md:p-4">
                    <BoxComponent>
                        <!--Nombre y email de la empresa-->
                        <div class="flex flex-col md:flex-row items-center md:space-x-4">
                            <div class="flex flex-col space-y-2 w-full">
                                <label class="font-bold text-neutral">Nombre</label>
                                <input v-model="form.name"  type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50" placeholder="Global Trade Consulting LLC"/>
                                <!--Contenedor de error en input-->
                                    <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.name">
                                        <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                        {{ form.errors.name }}
                                    </div>
                            </div>

                            <div class="flex flex-col space-y-2 w-full mt-4 md:mt-0">
                                <label class="font-bold text-neutral">Correo electrónico</label>
                                <input v-model="form.email"  type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50" placeholder="info@globaltradeconsulting.com"/>
                                <!--Contenedor de error en input-->
                                    <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.email">
                                        <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                        {{ form.errors.email }}
                                    </div>
                            </div>
                        </div>

                        <!--Contenedor de ubicación de empresa-->
                        <div class="flex flex-col md:flex-row items-center md:space-x-4 mt-4 w-full">
                            <div class="flex flex-col space-y-2 w-full">
                                <label class="font-bold text-neutral">Dirección</label>
                                <input v-model="form.address" type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 w-full" placeholder="742 Evergreen Terrace"/>
                                <!--Contenedor de error en input-->
                                <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.address">
                                    <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                    {{ form.errors.address }}
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row items-center md:space-x-3 space-y-4 md:space-y-0 w-full">
                                <div class="flex flex-col space-y-2 w-full mt-4 md:mt-0">
                                    <label class="font-bold text-neutral">Ciudad</label>
                                    <input v-model="form.city" type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 w-full" placeholder="Austin"/>
                                    <!--Contenedor de error en input-->
                                    <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.city">
                                        <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                        {{ form.errors.city }}
                                    </div>
                                </div>

                                <div class="flex flex-col space-y-2 w-full md:mt-0">
                                    <label class="font-bold text-neutral">País</label>
                                    <input v-model="form.country" type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 w-full" placeholder="Estados Unidos"/>
                                    <!--Contenedor de error en input-->
                                    <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.country">
                                        <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                        {{ form.errors.country }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--Teléfono, sitio web y tax-->
                        <div class="flex flex-col md:flex-row items-center md:space-x-4 mt-4 w-full">
                            <div class="flex flex-col space-y-2 w-full md:mt-0">
                                    <label class="font-bold text-neutral">Teléfono</label>
                                    <input v-model="form.phone"  type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 w-full" placeholder="+1 512 555 0199"/>
                                    <!--Contenedor de error en input-->
                                        <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.phone">
                                            <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                            {{ form.errors.phone }}
                                        </div>
                            </div>

                            <div class="flex flex-col space-y-2 w-full md:mt-0">
                                    <label class="font-bold text-neutral">Sitio web</label>
                                    <input v-model="form.web_address"  type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 w-full" placeholder="https://www.globaltradeconsulting.com"/>
                                    <!--Contenedor de error en input-->
                                        <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.web_address">
                                            <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                            {{ form.errors.web_address }}
                                        </div>
                            </div>

                            <div class="flex flex-col space-y-2 w-full md:mt-0">
                                    <label class="font-bold text-neutral">No. Identificación fiscal</label>
                                    <input v-model="form.tax_id"  type="text" class="bg-base-100 rounded-lg p-2 text-neutral focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 w-full" placeholder="84-5678912"/>
                                    <!--Contenedor de error en input-->
                                        <div class="flex items-center justify-start text-xs text-error" v-if="form.errors.tax_id">
                                            <PhWarningCircle class="mx-1 size-4" weight="bold"/>
                                            {{ form.errors.tax_id }}
                                        </div>
                            </div>
                        </div>

                        <!--Botón de crear empresa-->
                        <div class="flex items-center justify-center mt-8">
                            <button type="submit" class="bg-primary-content w-full p-3 rounded-lg text-black font-bold cursor-pointer tracking-wide">Añadir empresa</button>
                        </div>
                    </BoxComponent>
                </div>
            </form>

        </div>
    </AppLayout>
</template>
<script setup>
import BoxComponent from '../../Components/UI/BoxComponent.vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import { PhWarningCircle, PhHouseLine } from '@phosphor-icons/vue';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {Link} from '@inertiajs/vue3';


const form = useForm({
    name: null,
    email: null,
    address: null,
    city: null,
    country: null,
    phone: null,
    web_address: null,
    tax_id: null,
    company_code: null,
})

const create = () => form.post(route('companies.store'));
</script>
