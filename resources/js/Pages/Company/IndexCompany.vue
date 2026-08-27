<template>
    <AppLayout>
        <!--Contenedor global-->
        <div class="text-neutral mx-auto container p-4">
            <!--Titulo de pagina-->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-6xl text-2xl md:m-10 m-4 p-2">Empresas</h1>
            </div>

            <!--Botón crear nueva empresa-->
            <div class="flex items-center justify-end w-full gap-2">
                <div class="btn md:btn-md btn-sm text-black bg-primary md:font-bold border-0 hover:bg-primary-content hover:duration-200 duration-200">
                    <Link v-if="can?.create" :href="route('companies.create')" class="flex items-center space-x-1">
                        <PhPlus class="md:size-5 size-4" />
                        <span>Crear empresa</span>
                    </Link>
                </div>

                <!--Botón para unirte a una empresa-->
                <div class="link text-neutral link-hover font-bold md:text-sm text-xs">
                    <button type="button" class="btn btn-sm md:btn-md btn-link text-neutral" @click="form.reset() ; joinCompanyDialog?.showModal()">
                        <span>Unirse a empresa</span>
                        <PhCaretRight class="md:size-5 size-4" :weight="bold" />
                    </button>
                </div>
            </div>

            <!-- modal para unirse a empresa -->
            <dialog ref="joinCompanyDialog" class="modal">
                <div class="modal-box">

                    <!-- input del modal -->
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend text-neutral font-semibold">Ingresa el código de invitación para unirte.</legend>
                        <input type="text" v-model="form.code" class="input outline-none text-neutral border-neutral" placeholder="Ej: XK7J2M9PZ8" />
                        <div class="label text-red-600" v-if="form.errors.code">
                            <PhWarningCircle class="mx-1 size-4" weight="bold" />
                            {{ form.errors.code }}
                        </div>
                    </fieldset>

                    <!-- botón de unirse a empresa o cancelar accion -->
                    <div class="modal-action">
                        <form @submit.prevent="checkCode">
                            <button class="btn btn-primary text-black">Unirse</button>
                        </form>

                        <form method="dialog">
                            <button class="btn btn-error text-white" @click="form.reset()">Cancelar</button>
                        </form>
                    </div>
                </div>
            </dialog>

            <!--Tabla para la información de las empresas solo para ADMIN-->
            <CompanyTableInfo v-if="hasRole('admin')" :companies="companies" :filters="filters"/>

            <!--Vista para usuarios-->
            <div v-else class="p-4 mt-4">
                <div class="flex items-center w-full justify-start border-2 border-base-300 bg-base-200 rounded-lg p-4">
                    <span class="text-sm md:text-base">Aún no formas parte de una empresa.</span>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { PhPlus, PhCaretRight, PhWarningCircle } from '@phosphor-icons/vue';
import { route } from 'ziggy-js';
import CompanyTableInfo from '../../Components/UI/CompanyTableInfo.vue';
import { usePermission } from '../../composables/usePermission';
import { ref } from 'vue';

defineProps(
    {'companies': Object,
        'filters': Object,
        'can': Object,
    }
);

const form = useForm({
    code: '',
})

const {hasRole} = usePermission();

const checkCode = () => form.post(route('companies.checkCode'));

const joinCompanyDialog = ref(null)
</script>
