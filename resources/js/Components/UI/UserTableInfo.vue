<template>
    <!-- Link breadcrumbs -->
        <div class="breadcrumbs p-4 text-xs md:text-sm">
            <ul>
                <li><Link :href="route('inicio')"><PhHouseLine class="md:size-6 size-5 cursor-pointer hover:text-success duration-200 hover:duration-200" weight="duotone" /></Link></li>
                <li v-if="hasRole('admin')"><Link :href="route('manage-account.index')" class="hover:text-success duration-200 hover:duration-200 font-semibold">Usuarios</Link></li>
                <li>Lista de usuarios</li>
            </ul>
        </div>

    <!-- Filtro -->
    <FilterUser :filters="filters" :roles="roles"/>

    <!-- Contenedor global -->
    <div v-if="userAccounts.data.length" class="mx-auto md:p-4 flex justify-center">
        <div class="w-fit overflow-x-auto rounded-box border-2 border-base-content/15 bg-base-100">
            <table class="table md:table-md table-sm w-auto">
                <thead class="bg-base-200 text-neutral ">
                    <tr>
                        <th class="w-20">ID</th>
                        <th class="w-80">Nombre</th>
                        <th class="w-75">Email</th>
                        <th class="w-75">Roles</th>
                        <th class="w-70 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- info de usuarios -->
                    <tr v-for="userAccount in userAccounts.data" :key="userAccount.id">
                        <td class="w-20">{{ userAccount.id }}</td>
                        <td class="w-80">{{ userAccount.name }} {{ userAccount.last_name }}</td>
                        <td class="w-75">
                            <div class="flex items-center gap-2 text-neutral">
                                <span>{{ userAccount.email }}</span>
                                <div class="tooltip tooltip-right tooltip-end" :data-tip="copyText">
                                    <PhCopySimple @click="copy(userAccount.email)"
                                        class="md:size-5 size-4 cursor-pointer hover:text-accent" weight="bold" />
                                </div>
                            </div>
                        </td>
                        <!--rol de usuario-->
                        <td class="w-75">
                        <span v-for="role in userAccount.roles" :key="role.id" :class="[
                            role.name === 'admin' ? 'badge badge-soft badge-success badge-outline md:badge-md badge-sm' :
                            role.name === 'manager' ? 'badge badge-soft badge-error badge-outline md:badge-md badge-sm' :
                            role.name === 'team-leader' ? 'badge badge-soft badge-warning badge-outline md:badge-md badge-sm whitespace-nowrap' :
                            'badge badge-soft badge-info badge-outline md:badge-md badge-sm'
                        ]" class="gap-0.5">
                        <PhUserFocus class="md:size-5 size-4" />
                            {{ role.name === 'admin' ? 'Admin' :
                            role.name === 'manager' ? 'Manager' :
                            role.name === 'team-leader' ? 'Líder de equipo' :
                            'Miembro' }}
                        </span>
                        </td>
                        <!--botones de acción usuarios-->
                        <td class="w-75">
                            <div class="flex items-center justify-center gap-4">
                                <Link v-if="userAccount.update_c" :href="route('manage-account.edit', { user: userAccount.id })"><PhPencil :size="24" weight="duotone" class="hover:text-[#ffc300] hover:duration-200 duration-200"/></Link>
                                <Link v-if="userAccount.delete_c" :href="route('manage-account.destroy', { user: userAccount.id })" method="delete" as="button" ><PhTrash :size="24" weight="duotone" class="hover:text-[#ef233c] hover:duration-200 duration-200 cursor-pointer"/></Link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Contenedor de paginado -->
    <div v-if="userAccounts.data.length" class="w-full flex justify-center">
        <PaginationComponent :links="userAccounts.links" />
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { useClipboard } from '@vueuse/core';
import { ref, watch } from 'vue';
import { PhPencil, PhTrash, PhUserFocus, PhHouseLine, PhCopySimple} from '@phosphor-icons/vue';
import { route } from 'ziggy-js';
import PaginationComponent from '../../Components/UI/PaginationComponent.vue';
import FilterUser from './FilterUser.vue';
import { usePermission } from '../../composables/usePermission';

//Comprobar permisos de usuario
const {hasRole} = usePermission();

defineProps({
    'userAccounts': Object,
    'filters': Object,
    'roles': Object,
    })

const copyText = ref('Copiar')
const { copy, copied } = useClipboard({
    timeout: 2000,
})

watch(copied, (value) => {
    copyText.value = value ? 'Copiado' : 'Copiar'
})
</script>
