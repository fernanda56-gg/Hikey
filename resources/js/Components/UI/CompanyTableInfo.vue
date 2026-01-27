<template>
    <div class="mx-auto md:p-4 mt-3">
        <div class="overflow-x-auto rounded-box border-2 border-base-content/15 bg-base-100">
            <table class="table md:table-md table-sm">
                <thead class="bg-base-200 text-neutral ">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Ciudad</th>
                        <th>País</th>
                        <th>Propietario</th>
                        <th>Código</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="company in companies" :key="company.id">
                        <td>{{ company.id }}</td>
                        <td>{{ company.name }}</td>
                        <td>{{ company.email }}</td>
                        <td>{{ company.city }}</td>
                        <td>{{ company.country }}</td>
                        <td>{{ company.owner.name }}</td>
                        <td class="font-black">
                            <div class="flex items-center gap-2">
                                {{ company.company_code }}
                                <div class="tooltip tooltip-right" data-tip="Copiar">
                                    <PhCopySimple @click="copy(company.company_code)" class="md:size-5 size-4 cursor-pointer hover:text-accent" weight="bold"/>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <!-- Acción de visualizar datos de la empresa -->
                                <Link :href="route('companies.show', { company: company.id })">
                                    <PhEye :size="24" weight="duotone" class="hover:text-info hover:duration-200 duration-200"/>
                                </Link>
                                <!-- Acción de editar empresa -->
                                <Link :href="route('companies.edit', { company: company.id })">
                                    <PhPencil :size="24" weight="duotone" class="hover:text-warning hover:duration-200 duration-200"/>
                                </Link>
                                <!-- Acción de eliminar empresa -->
                                <Link :href="route('companies.destroy', { company: company.id })" method="delete" as="button" >
                                    <PhTrash :size="24" weight="duotone" class="hover:text-error hover:duration-200 duration-200 cursor-pointer"/>
                                </Link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { PhPencil, PhTrash, PhCopySimple, PhEye} from "@phosphor-icons/vue";
import { Link } from "@inertiajs/vue3";
import { route } from "ziggy-js";

defineProps(
    {'companies': Object,}
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

</script>
