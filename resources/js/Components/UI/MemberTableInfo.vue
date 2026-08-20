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
                                <Link v-if="can?.leave" :href="route('companies.leave', {company: company.id, user: member.id})" method="delete"  class="flex items-center gap-1 font-bold link link-hover hover:text-error hover:duration-200">
                                    <PhSignOut class="md:size-6 size-5" />
                                </Link>
                                <!-- <Link :href="route('companies.index')" class="flex items-center gap-1 font-bold link link-hover hover:text-info hover:duration-200"><PhKanban weight="duotone" class="md:size-6 size-5" /></Link> -->
                                <button @click="showMemberProjects(member)" class="flex items-center gap-1 font-bold link link-hover hover:text-info hover:duration-200">
                                    <PhKanban weight="duotone" class="md:size-6 size-5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- modal de lista de proyectos de usuario -->
                <dialog ref="dialogRef" @close="selectedMember = null" class="modal">

                    <!-- tabla de info de proyecto del usuario -->
                    <div v-if="selectedMember?.project_team?.length" class="modal-box w-11/12">

                        <!-- botón de salir de modal -->
                        <form method="dialog">
                            <button class="btn btn-sm btn-square btn-ghost absolute right-2 top-2">
                                <PhX class="size-4" weight="bold" />
                            </button>
                        </form>

                        <!-- titulo de modal -->
                        <h3 class="text-lg font-bold">Proyectos de {{ selectedMember.name }}</h3>

                        <!-- tabla de proyectos de usuario -->
                        <div class="w-fit overflow-x-auto rounded-box border-2 border-base-content/15 bg-base-100 overflow-y-auto max-h-50 scrollbar-thin scroll-smooth mt-2.5">
                            <table class="table md:table-md table-sm w-auto">
                                <!-- head -->
                                <thead class="bg-base-200 text-neutral">
                                    <tr>
                                        <th class="w-80">Nombre</th>
                                        <th class="w-40">Estatus</th>
                                        <th class="w-30">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- body -->
                                    <tr v-for="project in selectedMember.project_team" :key="project.id">
                                        <td>{{ project.name }}</td>
                                        <td class="align-middle">
                                            <span
                                                class="badge badge-sm badge-soft font-semibold"
                                                :class="[
                                                    project.status === 'Pendiente' ? 'badge-error' :
                                                    project.status === 'En progreso' ? 'badge-warning' :
                                                    'badge-success'
                                                ]"
                                            >{{ project.status }}</span>
                                        </td>
                                        <td>
                                            <Link
                                                :href="route('projects.show', {project: project.id})"
                                                class="btn btn-sm bg-primary text-black border-0 hover:bg-primary-content hover:duration-200 duration-200">
                                                <span class="font-semibold">Mostrar</span>
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- en caso de que aun no este colaborando en un proyecto -->
                    <div v-else class="modal-box w-11/12">

                        <!-- botón de salir de modal -->
                        <form method="dialog">
                            <button class="btn btn-sm btn-square btn-ghost absolute right-2 top-2">
                                <PhX class="size-4" weight="bold" />
                            </button>
                        </form>

                        <!-- titulo de modal -->
                        <h3 class="text-lg font-bold">Proyectos</h3>

                        <div class="flex items-center justify-between mt-2">
                            <span class="md:text-sm text-xs">El usuario aun no ha colaborado en ningún proyecto.</span>
                        </div>
                    </div>
                </dialog>
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
import { PhSignOut, PhKanban, PhCopySimple, PhHouseLine, PhX} from '@phosphor-icons/vue';
import { route } from 'ziggy-js';
import PaginationComponent from './PaginationComponent.vue';
import FilterMember from './FilterMember.vue';
import { useClipboard } from '@vueuse/core';
import { ref, watch } from 'vue';


defineProps({
    'company': Object,
    'members': Object,
    'can': Object,
    'filters': Object,
    }
);

const copyText = ref('Copiar')
const { copy, copied } = useClipboard({
    timeout: 2000,
})

watch(copied, (value) => {
    copyText.value = value ? 'Copiado' : 'Copiar'
})

const selectedMember = ref(null)
const dialogRef = ref(null)

function showMemberProjects(member) {
    selectedMember.value = member
    dialogRef.value?.showModal()
}
</script>
