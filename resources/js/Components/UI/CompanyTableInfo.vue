<template>
    <!-- Link breadcrumbs -->
    <div class="breadcrumbs px-4 py-1.5 text-xs md:text-sm">
        <ul>
            <li><Link :href="route('inicio')"><PhHouseLine class="md:size-6 size-5 cursor-pointer hover:text-success duration-200 hover:duration-200" weight="duotone" /></Link></li>
            <li><Link :href="route('companies.redirect')" class="hover:text-success duration-200 hover:duration-200 font-semibold">Empresas</Link></li>
            <li>Lista de empresas</li>
        </ul>
    </div>

    <!-- Filtro -->
    <FilterCompany :filters="filters"/>

    <!-- Contenedor global -->
    <div v-if="companies.data.length" class="mx-auto md:p-4 mt-1 flex justify-center">
        <div class="w-fit overflow-x-auto rounded-box border-2 border-base-content/15 bg-base-100">
            <table class="table md:table-md table-sm w-auto">
                <thead class="bg-base-200 text-neutral ">
                    <tr>
                        <th class="w-20">ID</th>
                        <th class="w-100">Nombre</th>
                        <th class="w-100">Email</th>
                        <th class="w-100">Ciudad</th>
                        <th class="w-100">País</th>
                        <th class="w-100">Propietario</th>
                        <th class="w-100">Código</th>
                        <th class="w-40 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="company in companies.data" :key="company.id">
                        <td class="w-20">{{ company.id }}</td>
                        <td class="w-100">{{ company.name }}</td>
                        <td class="w-100">{{ company.email }}</td>
                        <td class="w-100">{{ company.city }}</td>
                        <td class="w-100">{{ company.country }}</td>
                        <td class="w-100">{{ company.owner.name }}</td>
                        <td class="font-black w-100">
                            <div class="flex items-center gap-2">
                                {{ company.company_code }}
                                <div class="tooltip tooltip-right" :data-tip="copyText">
                                    <PhCopySimple @click="copy(company.company_code)" class="md:size-6 size-5 cursor-pointer hover:text-accent" weight="bold"/>
                                </div>
                            </div>
                        </td>
                        <!-- Acciones del contenedor -->
                        <td class="w-40">
                            <div class="flex items-center gap-3 justify-center">
                                <Link :href="route('companies.show', { company: company.id })"><PhEye weight="duotone" class="md:size-6 size-5 hover:text-info hover:duration-200 duration-200"/></Link>
                                <Link :href="route('companies.edit', { company: company.id })"><PhPencil weight="duotone" class="md:size-6 size-5 hover:text-warning hover:duration-200 duration-200"/></Link>
                                <Link :href="route('companies.destroy', { company: company.id })" method="delete" as="button" ><PhTrash weight="duotone" class="md:size-6 size-5 hover:text-error hover:duration-200 duration-200 cursor-pointer"/></Link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Contenedor de paginado -->
    <div v-if="companies.data.length" class="w-full flex justify-center">
        <PaginationComponent :links="companies.links" />
    </div>
</template>

<script setup>
import { PhPencil, PhTrash, PhCopySimple, PhEye, PhHouseLine } from "@phosphor-icons/vue";
import PaginationComponent from '../../Components/UI/PaginationComponent.vue';
import FilterCompany from "./FilterCompany.vue";
import { Link } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import { ref } from 'vue';

defineProps(
    {'companies': Object,
        'filters': Object,
    }
);

const copyText = ref('Copiar')
const copy = async (text) => {
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text)
        } else {
            const textarea = document.createElement('textarea')
            textarea.value = text
            textarea.style.position = 'fixed'
            textarea.style.left = '-9999px'
            document.body.appendChild(textarea)
            textarea.focus()
            textarea.select()
            document.execCommand('copy')
            document.body.removeChild(textarea)
        }

        copyText.value = 'Copiado!'

        setTimeout(() => {
            copyText.value = 'Copiar'
        }, 1500)

    } catch (e) {
        console.error(e)
    }
}

</script>
