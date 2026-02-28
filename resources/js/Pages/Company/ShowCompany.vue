<template>
    <AppLayout>
        <!--Contenedor global-->
        <div class="text-neutral container mx-auto p-4">
            <!--Titulo de pagina-->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-6xl text-2xl md:m-10 m-4 p-2">información de empresa</h1>
            </div>

            <!-- Link breadcrumbs -->
            <div class="breadcrumbs px-4 py-1.5 text-xs md:text-sm">
                <ul>
                    <li><Link :href="route('inicio')"><PhHouseLine class="md:size-6 size-5 cursor-pointer hover:text-success duration-200 hover:duration-200" weight="duotone" /></Link></li>
                    <li v-if="hasRole('admin')"><Link :href="route('companies.index')" class="hover:text-success duration-200 hover:duration-200 font-semibold">Empresas</Link></li>
                    <li v-if="hasRole('admin')"><Link :href="route('companies.show', {company: company.id})" class="hover:text-success duration-200 hover:duration-200 font-semibold">{{ company.name }}</Link></li>
                    <li v-if="hasAnyRole(['manager', 'team-leader', 'user'])"><Link :href="route('companies.redirect')" class="hover:text-success duration-200 hover:duration-200 font-semibold">{{ company.name }}</Link></li>
                    <li>Información de empresa</li>
                </ul>
            </div>

            <!--Contenedor de info de empresa-->
            <div class="flex flex-col lg:flex-row md:p-4 text-neutral">
                <!--Contenedor de información (grande)-->
                <div class="card border-2 border-base-300 bg-base-200 rounded-box h-auto min-w-4/5 grow place-items-start p-4">
                    <CompanyInfo :company="company" />
                </div>

                <div class="divider lg:divider-horizontal"></div>

                <!--Contenedor de acciones (pequeño)-->
                <div class="card bg-transparent rounded-box lg:h-32 h-20 w-full grow place-items-center lg:place-items-start">
                    <div class="space-x-6 md:space-x-0 md:space-y-4 flex flex-row md:flex-col items-start justify-start m-4">
                        <Link v-if="can?.update" :href="route('companies.edit', {company: company.id})" class="flex items-center gap-2 font-bold link link-hover hover:text-[#f8961e] hover:duration-200"><PhPencil class="md:size-6 size-5" />Editar</Link>
                        <Link v-if="can?.delete" :href="route('companies.destroy', {company: company.id})" method="delete" as="button" class="flex items-center gap-2 font-bold link link-hover hover:text-error hover:duration-200"><PhTrash class="md:size-6 size-5" />Eliminar</Link>
                        <Link :href="route('companies.listMember', {company: company.id})" class="flex items-center gap-2 font-bold link link-hover hover:text-info hover:duration-200"><PhUserList class="md:size-6 size-5" />Miembros</Link>
                    </div>
                    <div class="divider"></div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import CompanyInfo from '../../Components/UI/CompanyInfo.vue';
import AppLayout from '../../Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { PhPencil, PhTrash, PhUserList, PhHouseLine } from '@phosphor-icons/vue';
import { usePermission } from '../../composables/usePermission';

//Comprobar permisos de usuario
const {hasRole, hasAnyRole} = usePermission();

defineProps(
    {'company': Object,
    'can': Object
    }
)
</script>
