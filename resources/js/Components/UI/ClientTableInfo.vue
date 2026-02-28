<template>
    <!-- Link breadcrumbs -->
    <div class="breadcrumbs px-4 py-1.5 text-xs md:text-sm">
        <ul>
            <li><Link :href="route('inicio')"><PhHouseLine class="md:size-6 size-5 cursor-pointer hover:text-success duration-200 hover:duration-200" weight="duotone" /></Link></li>
            <li v-if="hasAnyRole(['admin', 'manager'])"><Link :href="route('clients.index')" class="hover:text-success duration-200 hover:duration-200 font-semibold">Clientes</Link></li>
            <li>Lista de clientes</li>
        </ul>
    </div>

    <!-- Componente de filtro -->
    <FilterClient :filters="filters"/>

    <!-- Contenedor global -->
    <div v-if="clients.data.length" class="mx-auto md:p-4 mt-1 flex justify-center">
        <div class="w-fit overflow-x-auto rounded-box border-2 border-base-content/15 bg-base-100">
            <table class="table md:table-md table-sm w-auto">
                <thead class="bg-base-200 text-neutral">
                    <tr>
                        <th class="w-80">Nombre</th>
                        <th class="w-80">Email</th>
                        <th class="w-80">Teléfono</th>
                        <th v-if="hasRole('admin')" class="w-80">Empresa</th>
                        <th class="w-60 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="client in clients.data" :key="client.id">
                        <td class="w-80">{{ client.name }}</td>
                        <td class="w-80">{{ client.email }}</td>
                        <td class="w-80">{{ client.phone }}</td>
                        <td v-if="hasRole('admin')" class="w-80">{{ client.company.name }}</td>
                        <!-- Acciones del contenedor -->
                        <td class="w-80 flex items-center gap-4 justify-center">
                            <Link :href="route('clients.edit', {client: client.id})" class="flex items-center gap-1 font-bold link link-hover hover:text-[#f8961e] hover:duration-200"><PhPencil weight="duotone" class="md:size-6 size-5" /></Link>
                            <Link :href="route('clients.destroy', {client: client.id})" method="delete" as="button"  class="flex items-center gap-1 font-bold link link-hover hover:text-error hover:duration-200"><PhTrash weight="duotone" class="md:size-6 size-5" /></Link>
                            <Link :href="route('clients.projects', {client: client.id})" class="flex items-center gap-1 font-bold link link-hover hover:text-info hover:duration-200"><PhKanban weight="duotone" class="md:size-6 size-5" /></Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Contenedor de paginado -->
            <div v-if="clients.data.length" class="w-full flex justify-center">
                <PaginationComponent :links="clients.links" />
            </div>

    <!-- Contenedor en caso de que aun no haya clientes -->
    <div v-else class="p-4">
        <div class="flex items-center w-full justify-start border-2 border-base-300 bg-base-200 rounded-lg p-4">
            Aún no hay clientes registrados.
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import PaginationComponent from '../../Components/UI/PaginationComponent.vue';
import FilterClient from '../../Components/UI/FilterClient.vue';
import { PhKanban, PhPencil, PhTrash, PhHouseLine} from '@phosphor-icons/vue';
import { usePermission } from '../../composables/usePermission';

//Comprobar permisos de usuario
const {hasRole, hasAnyRole} = usePermission();

defineProps({
    'clients': Object,
    'filters': Object,
});
</script>
