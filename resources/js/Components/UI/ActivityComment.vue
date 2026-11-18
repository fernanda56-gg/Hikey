<template>

    <!-- Comentarios  //! solo los integrantes de equipo puede ver comentarios -->
    <div v-if="activity.comment_p" class="flex flex-col w-full">

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
        <div v-for="comment in comments ?? []" :key="comment.id" class="chat chat-start">
            <div class="chat-header text-neutral mb-1 font-semibold">
                {{ comment.author?.name }} {{ comment.author?.last_name }}
            </div>
            <div class="chat-bubble bg-base-200 text-neutral text-xs pb-2">{{ comment.content }}</div>
            <div class="chat-footer text-slate-400/70 mt-1">Enviado:
                <div class="flex items-center justify-between gap-3">
                    <time class="chat-footer text-slate-400/70">{{ commentDateFormat(comment.created_at) }}</time>
                    <div class="dropdown dropdown-end">
                        <div v-if="comment.manage_p" tabindex="0" role="button" class="">
                            <PhDotsThreeVertical class="size-4 cursor-pointer" />
                        </div>
                        <ul tabindex="-1" class="dropdown-content menu bg-base-200 rounded-box z-1 w-32 mt-1.5 shadow-sm text-xs p-2">

                            <!-- btn para editar comentario -->
                            <li v-if="comment.updateComment_p">
                                <div class="dropdown dropdown-top dropdown-end p-0">
                                    <div tabindex="0" role="button" @focus="startEdit(comment)"
                                        class="flex items-center justify-start gap-1 px-2 py-1 text-neutral font-semibold hover:text-warning duration-200">
                                        <PhPencilSimple class="size-4" />
                                        Editar
                                    </div>

                                    <div tabindex="-1"
                                        class="dropdown-content bg-base-200 rounded-box z-10 w-52 p-2 shadow-sm">
                                        <form class="flex flex-col gap-1" @submit.prevent="editComment(comment)">
                                            <textarea v-model="editForm.content" rows="3"
                                                class="textarea textarea-xs w-full outline-none text-neutral"></textarea>

                                            <!-- Contenedor de error -->
                                            <div class="flex items-center justify-start text-error text-xs mt-1"
                                                v-if="editForm.errors.content">
                                                <PhWarningCircle class="mx-1 md:size-4 size-6" weight="bold" />
                                                {{ editForm.errors.content }}
                                            </div>

                                            <button type="submit" class="btn btn-xs btn-primary text-black self-end">
                                                Editar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>

                            <!-- Btn para eliminar comentario -->
                            <li><a v-if="comment.deleteComment_p" class="flex items-center justify-start gap-1 px-2 py-1 font-semibold text-neutral hover:text-error duration-200"
                                type="button" @click="removeComment(comment)">
                                <PhTrash class="size-4" />
                                Eliminar </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- lo que ve cualquier usuario que no pertenece al equipo -->
    <div v-else class="flex flex-col w-full">
        <p class="flex items-center gap-1 font-black text-slate-400/50 mt-5 cursor-not-allowed text-xs">
            <PhXCircle class="size-6" weight="bold" />
            Solo los integrantes del equipo pueden comentar en esta actividad.
        </p>
    </div>
</template>

<script setup>
import { router, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { PhWarningCircle, PhXCircle, PhDotsThreeVertical, PhTrash, PhPencilSimple } from '@phosphor-icons/vue';
import { computed } from 'vue';

const props = defineProps({
    activity: Object,
})

const form = useForm({content: ''})
const editForm = useForm({content: ''})
const comments = computed(() => props.activity.comments ?? [])

function addComment() {
    if (!form.content.trim()) return

    form.post(route('comment.store', {activity: props.activity.id}), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => form.reset()
    })
}

// ? muestra el momento en que se publico el comentario
function commentDateFormat(value) {
    return new Date(value).toLocaleString('es-MX', {
        day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit',
    })
}

function removeComment(comment) {
    router.delete(route('comment.destroy', comment.id), {
        preserveScroll: true,
        preserveState: true
    })
}

function startEdit(comment) {
    editForm.content = comment.content
    editForm.clearErrors()
}

function editComment(comment) {
    if (editForm.processing || !editForm.content.trim()) return

    editForm.patch(route('comment.update', {comment: comment.id}), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => document.activeElement?.blur(), // ? cierra el textarea cuando se actualiza el comentario
    })
}
</script>
