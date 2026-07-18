<template>
    <div class="text-neutral">
        <div class="grid md:grid-cols-2 grid-cols-1 gap-4 w-1/2">
            <div class="flex flex-col ">
                <h1 class="uppercase font-bold md:text-xl text-base">nombre</h1>
                <span class="md:text-lg text-base whitespace-nowrap">{{ company.name }}</span>
            </div>
            <div class="flex flex-col w-1/2 md:ml-15">
                <h1 class="uppercase font-bold md:text-xl text-base whitespace-nowrap">correo electrónico</h1>
                <span class="md:text-lg text-base">{{ company.email }}</span>
            </div>
        </div>


        <div class="grid md:grid-cols-3 grid-cols-1 gap-4 w-3/4 mt-6">
            <div class="flex flex-col w-full">
                <h1 class="uppercase font-bold md:text-lg text-base">dirección</h1>
                <span class="md:text-lg text-base">{{ company.address }}</span>
            </div>
            <div class="flex flex-col w-full">
                <h1 class="uppercase font-bold md:text-lg text-base">ciudad</h1>
                <span class="md:text-lg text-base whitespace-nowrap">{{ company.city }}</span>
            </div>
            <div class="flex flex-col w-full">
                <h1 class="uppercase font-bold md:text-lg text-base">país</h1>
                <span class="md:text-lg text-base whitespace-nowrap">{{ company.country }}</span>
            </div>
        </div>


        <div class="grid md:grid-cols-3 grid-cols-1 gap-4 w-3/4 mt-4">
            <div class="flex flex-col w-full">
                <h1 class="uppercase font-bold md:text-lg text-base">teléfono</h1>
                <span class="md:text-lg text-base whitespace-nowrap">{{ company.phone}}</span>
            </div>
            <div class="flex flex-col w-full">
                <h1 class="uppercase font-bold md:text-lg text-base">Sitio web</h1>
                <a :href="company.web_address" class="flex items-center space-x-1 text-accent font-bold" target="_blank" rel="noopener noreferrer">
                    <span>Link</span>
                    <PhLink class="size-5" weight="bold"/>
                </a>
            </div>
            <div class="flex flex-col w-full">
                <h1 class="uppercase font-bold md:text-lg whitespace-nowrap">No. Identificación fiscal</h1>
                <span class="md:text-lg text-base whitespace-nowrap">{{ company.tax_id }}</span>
            </div>
        </div>

        <div v-if="can?.showCode" class="grid grid-col-1 w-full mt-4 gap-4">
            <div class="flex flex-col w-full">
                <h1 class="uppercase font-bold md:text-lg text-base">código de invitación</h1>
                <div class="flex items-center gap-2">
                    <span class="md:text-lg text-base whitespace-nowrap font-bold">{{ company.company_code }}</span>
                    <div class="tooltip tooltip-right" :data-tip="copyText">
                        <PhCopySimple @click="copy(company.company_code)" class="md:size-5 size-4 cursor-pointer hover:text-accent" weight="bold" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script  setup>
import { PhLink, PhCopySimple } from '@phosphor-icons/vue';
import { useClipboard } from '@vueuse/core';
import { ref, watch } from 'vue';

defineProps({
        company: Object,
        can: Object,
    }
)

const copyText = ref('Copiar')
const { copy, copied } = useClipboard({
    timeout: 2000,
})

watch(copied, (value) => {
    copyText.value = value ? 'Copiado' : 'Copiar'
})
</script>
