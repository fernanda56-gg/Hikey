<template>
    <div class="flex items-center justify-center w-full">
        <h2 class="font-black text-center">Equipo</h2>
    </div>


    <!-- botón para agregar integrantes de equipo -->
    <div class="flex flex-row items-center gap-2 justify-center w-full mt-2 pb-6 md:pb-0"
        v-if="!props.project.users.length">
        <button
            class="btn md:btn-sm btn-md text-black bg-primary md:font-bold border-0 hover:bg-primary-content hover:duration-200 duration-200 outline-none"
            @click="openModal">
            <PhPlus class="size-4" weight="bold" />
            <span>Añadir integrantes</span>
        </button>
    </div>

    <!-- Collapse de tabla de integrantes de equipo -->
    <div v-else class="collapse collapse-arrow font-black">
        <input type="checkbox" />
        <div class="collapse-title text-sm link">Mostrar integrantes</div>
        <div class="collapse-content text-sm">
            <div class="flex flex-row items-center gap-2 justify-center w-full min-w-0 mt-2 pb-6 md:pb-0">
                <div class="flex items-center justify-between w-full md:w-fit min-w-0">
                    <div class="w-full md:w-fit overflow-x-auto">
                        <table class="table table-sm w-auto">
                            <thead>
                                <tr>
                                    <th class="w-100 text-neutral">Integrantes - {{ props.project.users.length }}/9</th>
                                    <th class="w-40"></th>
                                    <th class="w-40 text-end">
                                        <button
                                            class="btn btn-sm text-black bg-primary md:font-bold border-0 hover:bg-primary-content hover:duration-200 duration-200 outline-none disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed"
                                            @click="openModal" :disabled="props.project.users.length === 9">
                                            <PhPlus class="size-4" weight="bold" />
                                            <span>Añadir</span>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in organizedUsers" :key="user.id">
                                    <td class="w-100 font-semibold">{{ user.name }}</td>
                                    <td class="w-40">
                                        <span class="badge badge-sm badge-soft font-semibold"
                                            :class="user.pivot.role === 'Lider' ? 'badge-success' : 'badge-info'">
                                            {{ user.pivot.role }}
                                        </span>
                                    </td>
                                    <td class="w-40 text-center space-x-2">

                                        <!-- botón para cambiar de rol miembro-lider -->
                                        <button v-if="user.pivot.role === 'Miembro' && !isLeader"
                                            @click="makeLeader(user.id)"
                                            :preserve-scroll="true"
                                            class="btn btn-square btn-sm border-0 hover:bg-mist-300/50">
                                            <PhArrowFatLinesUp class="size-4 text-neutral" weight="fill" />
                                        </button>

                                        <!-- boton para quitar rol de lider -->
                                        <button v-if="user.pivot.role === 'Lider'"
                                            @click="removeLeader(user.id)"
                                            :preserve-scroll="true"
                                            class="btn btn-square btn-sm border-0 hover:bg-mist-300/50">
                                            <PhArrowFatLinesDown class="size-4 text-neutral" weight="fill" />
                                        </button>

                                        <!-- botón para sacar a integrante de equipo -->
                                        <Link
                                            v-if="user.pivot.role === 'Miembro'"
                                            :href="route('project-team.destroy', { project: project.id, user: user.id })"
                                            method="delete" as="button" :preserve-scroll="true"
                                            class="btn btn-square btn-sm border-0 hover:bg-mist-300/50">
                                            <PhX class="size-4 text-neutral" weight="bold" />
                                        </Link>

                                        <!-- botón para copiar correo electronico de integrante -->
                                        <button :data-tip="copyText"
                                                @click="copy(user.email)"
                                                class="btn btn-square btn-sm border-0 hover:bg-mist-300/50">
                                            <PhAt class="size-4 text-neutral" weight="bold" />
                                        </button>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- modal para seleccionar integrantes del equipo -->
    <dialog id="my_modal_1" class="modal">
        <div class="modal-box w-11/12">
            <!-- Titulo del modal -->
            <h3 class="text-lg font-bold">Selecciona a los integrantes</h3>

            <!-- input de busqueda de usuarios -->
            <div class="flex items-center py-2">
                <label class="input outline-none input-sm md:input-md">
                    <PhMagnifyingGlass class="md:size-4" weight="bold" />
                    <input v-model="searchUser" type="search" required placeholder="Buscar" class="font-semibold" />
                </label>
            </div>

            <!-- Tabla de disponibilidad de usuarios -->
            <div class="w-fit overflow-x-auto rounded-box border-2 border-base-content/15 bg-base-100 overflow-y-auto max-h-50 scrollbar-thin scroll-smooth ">
                <table class="table md:table-md table-sm w-auto">
                    <!-- head -->
                    <thead class="bg-base-200 text-neutral">
                        <tr>
                            <th class="w-100">Usuarios</th>
                            <th class="w-40 text-center">Disponibiidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="member in filterUsers" :key="member.id">
                            <td>{{ member.name }} {{ member.last_name }}</td>
                            <td class="flex justify-center items-center space-x-2">
                                <input
                                    type="checkbox"
                                    class="checkbox checkbox-neutral checkbox-sm outline-none"
                                    :value="member.id"
                                    v-model="selectMembers"
                                    :disabled="
                                        workingInProject.includes(member.id) || !member.is_available
                                    "
                                />

                                <!-- si el usuario esta en el equipo del proyecto -->
                                <span v-if="workingInProject.includes(member.id)" class="badge badge-sm badge-soft font-semibold badge-neutral">En equipo</span>

                                <!-- si el usuario esta trabajando en otro proyecto -->
                                <span v-else class="badge badge-sm badge-soft font-semibold" :class="member.is_available ? 'badge-info' : 'badge-error'">
                                    {{ member.is_available ? 'Disponible' : 'Ocupado' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-action">
                <button class="btn btn-info text-white" @click="submitTeam" :disabled="selectMembers.length === 0">Añadir</button>
                <form method="dialog">
                    <!-- if there is a button in form, it will close the modal -->
                    <button class="btn btn-error text-white" @click="cancelButton"> Cancelar </button>
                </form>
            </div>
        </div>
    </dialog>
</template>

<script setup>
import { PhPlus, PhMagnifyingGlass, PhArrowFatLinesUp, PhX, PhArrowFatLinesDown, PhAt } from "@phosphor-icons/vue";
import { computed, ref, watch } from "vue";
import axios from "axios";
import { route } from "ziggy-js";
import { Link, router } from "@inertiajs/vue3";
import { useClipboard } from "@vueuse/core";

const members = ref([]);
const workingInProject = ref([]);
const selectMembers = ref([]);
const searchUser = ref('');

const props = defineProps({
    project: Object,
});

async function openModal(){
    const { data } = await axios.get(
        route('project-team.index', props.project.id),
    );

    members.value = data.members;
    workingInProject.value = data.workingInProject;

    document.getElementById("my_modal_1").showModal();
}

function resetValues(){
    selectMembers.value = [];
    members.value = [];
    workingInProject.value = [];
}

function cancelButton() {
    document.getElementById("my_modal_1").close();
    resetValues();
}

const filterUsers = computed ( () => {
    if(!searchUser.value.trim()){
        return members.value
    }
    const query = searchUser.value.toLowerCase()

    return members.value.filter(member => {
        const fullName = `${member.name} ${member.last_name}`.toLowerCase()
        return fullName.includes(query)
    })
})

function submitTeam(){
    router.post(route('project-team.store', props.project.id), {
        members_ids:selectMembers.value,
    }, {
        preserveScroll:true,
        onSuccess: () => {
            resetValues(),
            document.getElementById('my_modal_1').close()
        },
        onError: () => {
            resetValues(),
            document.getElementById('my_modal_1').close()
        }
    })
}

function makeLeader(userId){
    router.patch(
        route('project-team.update-role', [props.project.id, userId]),
        { role: 'Lider' },
        { preserveScroll: true }
    )
}

function removeLeader(userId){
    router.put(
        route('project-team.remove-leader', [props.project.id, userId]),
        { preserveScroll: true }
    )
}

const isLeader = computed(() => {
    return props.project.users.some(user => user.pivot.role === 'Lider')
})

const organizedUsers = computed(() => {
    return[...props.project.users].sort((x,y) => {
        if(x.pivot.role === y.pivot.role) return 0
        return x.pivot.role === 'Lider' ? -1 : 1
    })
})

const copyText = ref('Copiar')
const {copy, copied} = useClipboard({
    timeout:2000,
})

watch(copied, (value) => {
    copyText.value = value ? 'Copiado' : 'Copiar'
})
</script>
