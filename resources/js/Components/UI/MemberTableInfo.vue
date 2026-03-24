<template>
    <!-- Link breadcrumbs -->
            <div class="breadcrumbs p-4 text-xs md:text-sm">
                <ul>
                    <li><Link :href="route('inicio')"><PhHouseLine class="md:size-6 size-5 cursor-pointer hover:text-success duration-200 hover:duration-200" weight="duotone" /></Link></li>
                    <li v-if="can?.admin"><Link :href="route('companies.index')" class="hover:text-success duration-200 hover:duration-200 font-semibold">Empresas</Link></li>
                    <li v-if="can?.admin"><Link :href="route('companies.show', {company: company.id})" class="hover:text-success duration-200 hover:duration-200 font-semibold">{{ company.name }}</Link></li>
                    <li v-if="can?.viewMembers"><Link :href="route('companies.show', {company: company.id})" class="hover:text-success duration-200 hover:duration-200 font-semibold">{{ company.name }}</Link></li>
                    <li>Miembros</li>
                </ul>
            </div>

    <!-- Filtro -->
    <FilterMember :filters="filters" :company="company"/>

    <!-- Contenedor global -->
    <div v-if="members.data.length" class="mx-auto md:p-4 mt-3 flex justify-center">
        <div class="w-fit overflow-x-auto rounded-box border-2 border-base-content/15 bg-base-100">
            <table class="table md:table-md table-sm w-auto">
                <thead class="bg-base-200 text-neutral">
                    <tr>
                        <th class="w-80">Nombre</th>
                        <th class="w-80">Email</th>
                        <th class="w-80">Rol</th>
                        <th class="w-40 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="member in members.data" :key="member.id">
                        <td class="w-80">{{ member.name }} {{ member.last_name }}</td>
                        <td class="w-80 font-semibold"><div class="flex items-center gap-2">
                                {{ member.email}}
                                <div class="tooltip tooltip-right" :data-tip="copyText">
                                    <PhCopySimple @click="copy(member.email)" class="md:size-5 size-4 cursor-pointer text-info" weight="bold"/>
                                </div>
                            </div></td>
                        <td class="w-80 font-black capitalize">{{ member.pivot.role}}</td>
                        <!--botones de acciones para usuarios-->
                        <td class="w-40">
                            <div class="flex items-center justify-center gap-4">
                                <Link v-if="can?.leave" :href="route('companies.leave', {company: company.id, user: member.id})" method="delete"  class="flex items-center gap-1 font-bold link link-hover hover:text-error hover:duration-200"><PhSignOut class="md:size-6 size-5" /></Link>
                                <Link :href="route('companies.index')" class="flex items-center gap-1 font-bold link link-hover hover:text-info hover:duration-200"><PhKanban weight="duotone" class="md:size-6 size-5" /></Link>
                            </div>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Contenedor de paginado -->
    <div v-if="members.data.length" class="w-full flex justify-center">
        <PaginationComponent :links="members.links" />
    </div>

    <!-- Contenedor en caso de que aun no haya miembros -->
    <div v-else class="p-4">
        <div class="flex items-center w-full justify-start border-2 border-base-300 bg-base-200 rounded-lg p-4">
            Aún no hay miembros de esta empresa registrados.
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { PhSignOut, PhKanban, PhCopySimple, PhHouseLine} from '@phosphor-icons/vue';
import { route } from 'ziggy-js';
import { ref } from 'vue';
import PaginationComponent from './PaginationComponent.vue';
import FilterMember from './FilterMember.vue';


defineProps({
    'company': Object,
    'members': Object,
    'can': Object,
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
