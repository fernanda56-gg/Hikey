<template>
    <AppLayout>
        <!--Contenedor global-->
        <div class="text-neutral mx-auto container p-4">
            <!--Titulo de pagina-->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-6xl text-2xl md:m-10 m-4 p-2">Empresas</h1>
            </div>

            <!--Botón crear nueva empresa-->
            <div class="flex items-center justify-end w-full gap-4">
                <div class="btn md:btn-md btn-sm text-black bg-primary md:font-bold border-0 hover:bg-primary-content hover:duration-200 duration-200">
                    <Link v-if="can('create companies')" :href="route('companies.create')" class="flex items-center space-x-1">
                        <PhPlus class="md:size-6 size-4" />
                        <span>Crear empresa</span>
                    </Link>
                </div>

                <!--Botón para unirte a una empresa-->
                <div class="link text-neutral link-hover font-bold md:text-sm text-xs">
                    <Link :href="route('companies.join')" class="flex items-center space-x-1">
                        <span>Unirse a empresa</span>
                        <PhCaretRight class="md:size-5 size-4" :weight="bold" />
                    </Link>
                </div>
            </div>

            <!--Tabla para la información de las empresas solo para ADMIN-->
            <CompanyTableInfo v-if="hasRole('admin')" :companies="companies" />

            <!--Vista para usuarios-->
            <div v-else class="p-4 mt-4">
                <div class="flex items-center w-full justify-start border-2 border-base-300 bg-base-200 rounded-lg p-4">
                    Aún no formas parte de una empresa.
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { PhPlus, PhCaretRight } from '@phosphor-icons/vue';
import { route } from 'ziggy-js';
import CompanyTableInfo from '../../Components/UI/CompanyTableInfo.vue';
import { usePermission } from '../../composables/usePermission';

defineProps(
    {'companies': Object,}
);

const {hasRole, can} = usePermission();
</script>
