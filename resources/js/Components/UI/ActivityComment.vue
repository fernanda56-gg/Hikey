<template>
    <div class="flex flex-col w-full">

        <!-- Form para añadir nuevos comentarios -->
        <form v-if="activity.comment_p" @submit.prevent="addComment">
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-neutral font-black">Comentarios</legend>
                <textarea v-model="form.content" class="textarea textarea-xs outline-none" placeholder="Comentario"></textarea>
            </fieldset>

            <!-- Contenedor de error -->
            <div class="flex items-center justify-start text-error text-xs mt-1" v-if="form.errors.content">
                <PhWarningCircle class="mx-1 md:size-4 size-6" weight="bold" />
                {{ form.errors.content }}
            </div>

            <!-- Btn de publicar comentarios -->
            <div class="flex items-center justify-end">
                <button type="submit" class="btn btn-primary btn-xs text-black">Publicar</button>
            </div>
        </form>

        <!-- Cuando aún no hay comentarios -->
        <p v-if="!activity.comments?.length" class="flex items-center gap-1 font-black text-slate-400/50 mt-5 cursor-not-allowed text-xs">
            <PhXCircle class="size-4" weight="bold" />
            Aún no hay comentarios.
        </p>

        <!-- Cuando ya hay comentarios -->
        <div v-for="comment in activity.comments ?? []" :key="comment.id" class="chat chat-start">
            <div class="chat-header text-neutral mb-1 font-semibold">
                {{ comment.author?.name }} {{ comment.author?.last_name }}
            </div>
            <div class="chat-bubble bg-base-200 text-neutral text-xs">{{ comment.content }}</div>
            <div class="chat-footer text-slate-400/70 mt-1">Enviado:
                <time class="chat-footer text-slate-400/70">{{ commentDateFormat(comment.created_at) }}</time>
            </div>
        </div>

    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { PhWarningCircle, PhXCircle } from '@phosphor-icons/vue';

const props = defineProps({
    activity: Object,
})

const form = useForm({content: ''})

function addComment() {
    if (!form.content.trim()) return

    form.post(route('comment.store', {activity: props.activity.id}), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => form.reset()
    })
}
console.log(JSON.stringify(props.activity.comments?.[0]))

// ? muestra el momento en que se publico el comentario
function commentDateFormat(value) {
    return new Date(value).toLocaleString('es-MX', {
        day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit',
    })
}
</script>
