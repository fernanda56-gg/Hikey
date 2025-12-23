<template>
    <AppLayout>
        <!--Contenedor global-->
        <div class="text-neutral container mx-auto p-4">
            <!--Titulo de pagina-->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-6xl text-2xl md:m-10 m-4 p-2">usuarios</h1>
            </div>
            <!--Botón crear nuevo proyecto-->
            <div class="flex items-center justify-end w-full">
                <div class="btn md:btn-md btn-sm text-black bg-primary md:font-bold border-0 hover:bg-primary-content hover:duration-200 duration-200">
                    <Link :href="route('manage-account.create')" class="flex items-center space-x-1">
                        <PhPlus class="md:size-6 size-4" />
                        <span>Nuevo usuario</span>
                    </Link>
                </div>
            </div>
            <!--Tabla de información de usuarios-->
            <UserTableInfo :userAccounts="userAccounts" />
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import UserTableInfo from '../../Components/UserTableInfo.vue';
import { PhPlus} from '@phosphor-icons/vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';


defineProps(
    {'userAccounts': Object,}
)

</script>
