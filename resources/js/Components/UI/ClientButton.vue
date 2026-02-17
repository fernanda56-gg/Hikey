<template>
    <div class="flex items-center justify-center w-full">
        <h2 class="font-black uppercase text-center">cliente</h2>
    </div>

    <!-- Contenedor de info de clientes (cuando ya se asigno a uno) -->
    <div v-if="projects.clients.length" class=" flex items-center justify-start md:mt-3 mt-1.5 pb-6 md:pb-0 mx-2">
        <div v-for="client in projects.clients" :key="client.id" class="flex flex-row items-center gap-3">
            <h1 class="font-black">{{ client.name }}</h1>

            <div class="tooltip tooltip-bottom" :data-tip="copyText">
                <PhAt @click="copy(client.email)" class="size-5 cursor-pointer hover:text-accent" weight="bold"/>
            </div>

            <div class="tooltip tooltip-bottom" :data-tip="copyText">
                <PhPhone @click="copy(client.phone)" class="size-5 cursor-pointer hover:text-accent" weight="bold"/>
            </div>

            <div class="flex flex-row gap-3" v-if="hasAnyRole(['admin', 'manager'])">
                <div class="flex items-center">
                    <Link v-if="client.client_update" :href="route('clients.destroy', {client: client.id})" method="delete" as="button"><PhTrash class="size-5 text-error cursor-pointer" weight="bold"/></Link>
                </div>

                <div class="flex items-center" v-if="hasAnyRole(['admin', 'manager'])">
                    <Link v-if="client.client_delete" :href="route('clients.edit', {client: client.id})"><PhPencil class="size-5 text-[#f8961e]" weight="bold"/></Link>
                </div>

                <div class="flex items-center" v-if="hasAnyRole(['admin', 'manager'])">
                    <Link :href="route('clients.projects.detach', {project: projects.id, client: client.id})" method="delete" as="button" class="cursor-pointer"><PhLinkBreak class="size-5 text-accent" weight="bold"/></Link>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones para acceder a clientes -->
        <div v-else class="flex flex-row items-center gap-2 mt-2 pb-6 md:pb-0">
            <div class="btn md:btn-sm btn-md text-black bg-primary md:font-bold border-0 hover:bg-primary-content hover:duration-200 duration-200">
                    <Link :href="route('clients.create', { project: projects.id })" class="flex items-center space-x-1">
                        <PhPlus class="size-4" weight="bold"/>
                        <span class="md:text-xs text-sm">Nuevo cliente</span>
                    </Link>
            </div>

            <div class="btn md:btn-sm btn-md text-black bg-primary md:font-bold border-0 hover:bg-primary-content hover:duration-200 duration-200">
                    <Link :href="route('clients.projects.assign', { project: projects.id })" class="flex items-center space-x-1">
                        <PhStar class="size-4" weight="bold"/>
                        <span class="md:text-xs text-sm">Asignar cliente</span>
                    </Link>
            </div>
        </div>
</template>

<script setup>
import { PhStar, PhPlus, PhAt, PhPhone, PhPencil, PhTrash, PhLinkBreak} from '@phosphor-icons/vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
/* import { useClipboard } from '@vueuse/core'; no funcionara hasta que este en un sea https
import { ref, watch } from 'vue'; */
import { ref } from 'vue';
import { usePermission } from '../../composables/usePermission';

//Comprobar permisos de usuario
const {hasAnyRole} = usePermission();

defineProps({
    projects: Object,
})


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

/* const { copy, copied, isSupported } = useClipboard({
    timeout: 2000,
})

watch(copied, (value) => {
    copyText.value = value ? 'Copiado' : 'Copiar'
}) */
</script>
