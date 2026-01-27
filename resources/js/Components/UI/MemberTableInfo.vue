<template>
    <div class="mx-auto md:p-4 mt-3 flex justify-center">
        <div class="w-fit overflow-x-auto rounded-box border-2 border-base-content/15 bg-base-100">
            <table class="table md:table-md table-sm w-auto">
                <thead class="bg-base-200 text-neutral">
                    <tr>
                        <th class="w-80">Nombre</th>
                        <th class="w-80">Email</th>
                        <th class="w-80">Rol</th>
                        <th class="w-60 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="member in members" :key="member.id">
                        <td class="w-80">{{ member.name }} {{ member.last_name }}</td>
                        <td class="w-80 underline text-blue-600 font-semibold"><div class="flex items-center gap-2">
                                {{ member.email}}
                                <div class="tooltip tooltip-right" data-tip="Copiar">
                                    <PhCopySimple @click="copy(member.email)" class="md:size-5 size-4 cursor-pointer hover:text-black" weight="bold"/>
                                </div>
                            </div></td>
                        <td class="w-80 font-black capitalize">{{ member.pivot.role}}</td>
                        <!--botones de acciones para usuarios-->
                        <td class="w-80 flex items-center gap-6 justify-center">
                            <Link v-if="can('leave company')" :href="route('companies.leave', {company: company.id, user: member.id})" method="delete" class="flex items-center gap-1 font-bold link link-hover hover:text-error hover:duration-200"><PhSignOut class="md:size-6 size-5" />Abandonar</Link>
                            <Link :href="route('companies.index')" class="flex items-center gap-1 font-bold link link-hover hover:text-info hover:duration-200"><PhKanban weight="duotone" class="md:size-6 size-5" />Proyectos</Link>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { PhSignOut, PhKanban, PhCopySimple} from '@phosphor-icons/vue';
import { route } from 'ziggy-js';
import { usePermission } from '../../composables/usePermission';

defineProps(
    {'company': Object,
    'members': Object,
    }
);

const copy = async (text) => {
    try {
    if (navigator.clipboard && window.isSecureContext) {
    await navigator.clipboard.writeText(text)
    return
    }

    const textarea = document.createElement('textarea')
    textarea.value = text
    textarea.style.position = 'fixed'
    textarea.style.left = '-9999px'
    document.body.appendChild(textarea)
    textarea.focus()
    textarea.select()
    document.execCommand('copy')
    document.body.removeChild(textarea)
} catch (e) {
    console.error(e)
}
}

const {can} = usePermission();
</script>
