<template>
    <div class="text-neutral">
        <div class="grid md:grid-cols-2 grid-cols-1 gap-4 w-1/2">
            <div class="flex flex-col ">
                <h1 class="uppercase font-bold md:text-xl text-lg">nombre</h1>
                <span class="text-lg whitespace-nowrap">{{ company.name }}</span>
            </div>
            <div class="flex flex-col w-1/2 md:ml-15">
                <h1 class="uppercase font-bold md:text-xl text-lg whitespace-nowrap">correo electrónico</h1>
                <span class="text-lg">{{ company.email }}</span>
            </div>
        </div>


        <div class="grid md:grid-cols-3 grid-cols-1 gap-4 w-3/4 mt-6">
            <div class="flex flex-col w-full">
                <h1 class="uppercase font-bold text-lg">dirección</h1>
                <span class="text-lg">{{ company.address }}</span>
            </div>
            <div class="flex flex-col w-full">
                <h1 class="uppercase font-bold text-lg">ciudad</h1>
                <span class="text-lg whitespace-nowrap">{{ company.city }}</span>
            </div>
            <div class="flex flex-col w-full">
                <h1 class="uppercase font-bold text-lg">país</h1>
                <span class="text-lg whitespace-nowrap">{{ company.country }}</span>
            </div>
        </div>


        <div class="grid md:grid-cols-3 grid-cols-1 gap-4 w-3/4 mt-4">
            <div class="flex flex-col w-full">
                <h1 class="uppercase font-bold text-lg">teléfono</h1>
                <span class="text-lg whitespace-nowrap">{{ company.phone}}</span>
            </div>
            <div class="flex flex-col w-full">
                <h1 class="uppercase font-bold text-lg">Sitio web</h1>
                <a :href="company.web_address" class="flex items-center space-x-1 text-accent font-bold" target="_blank" rel="noopener noreferrer">
                    <span>Link</span>
                    <PhLink class="size-5" weight="bold"/>
                </a>
            </div>
            <div class="flex flex-col w-full">
                <h1 class="uppercase font-bold md:text-lg whitespace-nowrap">No. Identificación fiscal</h1>
                <span class="text-lg whitespace-nowrap">{{ company.tax_id }}</span>
            </div>
        </div>

        <div class="grid grid-col-1 w-full mt-4 gap-4">
            <div class="flex flex-col w-full">
                <h1 class="uppercase font-bold text-lg">código de invitación</h1>
                <div class="flex items-center gap-2">
                    <span class="text-lg whitespace-nowrap font-bold">{{ company.company_code }}</span>
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
import { ref } from 'vue';

defineProps(
    {'company': Object,}
)

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
