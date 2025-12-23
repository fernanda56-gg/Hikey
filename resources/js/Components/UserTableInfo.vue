<!--Componente para tabla usuarios de index-->
<template>
    <div class="mx-auto md:p-4 mt-3">
        <div class="overflow-x-auto rounded-box border-2 border-base-content/15 bg-base-100">
            <table class="table md:table-md table-sm">
                <!-- head -->
                <thead class="bg-base-200 text-neutral ">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- info de usuarios -->
                    <tr v-for="userAccount in userAccounts" :key="userAccount.id">
                        <td>{{ userAccount.id }}</td>
                        <td>{{ userAccount.name }}</td>
                        <td>{{ userAccount.last_name }}</td>
                        <td>{{ userAccount.email }}</td>
                        <!--rol de usuario-->
                        <td>
                        <span v-for="role in userAccount.roles" :key="role.id" :class="[
                            role.name === 'admin' ? 'badge badge-soft badge-success badge-outline md:badge-md badge-sm' :
                            role.name === 'user' ? 'badge badge-soft badge-info badge-outline md:badge-md badge-sm' :
                            role.name === 'manager' ? 'badge badge-soft badge-warning badge-outline md:badge-md badge-sm' :
                            'badge badge-soft badge-warning badge-outline md:badge-md badge-sm'
                        ]" class="gap-0.5">
                        <PhUserFocus class="md:size-5 size-4" />
                            {{ role.name === 'admin' ? 'Admin' :
                            role.name === 'user' ? 'Usuario' :
                            role.name === 'manager' ? 'Manager' :
                            'Invitado' }}
                        </span>
                        </td>
                        <!--botones de acción usuarios-->
                        <td class="flex items-center space-x-3">
                            <Link :href="route('manage-account.edit', { user: userAccount.id })"><PhPencil :size="24" weight="duotone" class="hover:text-[#ffc300] hover:duration-200 duration-200"/></Link>
                            <Link :href="route('manage-account.destroy', { user: userAccount.id })" method="delete" as="button" ><PhTrash :size="24" weight="duotone" class="hover:text-[#ef233c] hover:duration-200 duration-200 cursor-pointer"/></Link>
                        </td>
                    </tr>



                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { PhPencil, PhTrash, PhUserFocus} from '@phosphor-icons/vue';
import { route } from 'ziggy-js';

defineProps({
    'userAccounts': Object,})
</script>
