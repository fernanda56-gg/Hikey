<template>
    <div class="flex flex-col w-full">
        <div class="flex w-full items-center justify-between">
            <h1 class="text-sm font-semibold text-neutral">Plan de acción</h1>
            <span class="flex items-center gap-1 text-xs font-semibold text-slate-500">
                <PhCheckSquareOffset class="size-4" weight="bold" />
                {{ doneCount }}/{{ items.length }}
            </span>
        </div>


        <!-- barra de progreso de acciones -->
        <div class="flex items-center justify-start gap-1.5">
            <progress class="progress progress-primary w-56 mt-3" :value="progress" max="100"></progress>
            <span class="flex items-center text-xs font-semibold mt-2 text-neutral">
                {{ progress }}
                <PhPercent class="size-3" weight="bold" />
            </span>
        </div>

        <!-- cuando aún no hay acciones registradas -->
        <p v-if="!items.length" class="flex items-center gap-1 font-black text-slate-400/50 mt-5 cursor-not-allowed text-sm">
            <PhXCircle class="size-4" weight="bold" />
            Aún no hay acciones registradas.
        </p>

        <!-- form para registrar nuevas acciones (PASOS A SEGUIR DE LA ACT.) -->
        <form v-if="activity.update_p"
            @submit.prevent="addItem"
            class="flex items-center mt-5 gap-2">
            <input v-model="form.description" type="text" placeholder="Investigar información relacionada con la actividad."
                class="input input-sm flex-1 outline-none" />
            <button type="submit" class="btn btn-sm btn-primary text-black" :disabled="form.processing">
                Añadir
            </button>
        </form>

        <!--Contenedor de error en input-->
        <div class="flex items-center justify-start text-error text-xs mt-1" v-if="form.errors.description">
            <PhWarningCircle class="mx-1 md:size-4 size-6" weight="bold" />
            {{ form.errors.description }}
        </div>

        <!-- Contenedor de lista de acciones (PASOS A SEGUIR DE LA ACT.) -->
        <div class="flex items-center mt-3">
            <ul class="space-y-1">
                <li v-for="item in items" :key="item.id" class="flex items-center gap-2">
                    <input type="checkbox"
                    :checked="item.is_completed"
                    :disabled="!activity.toggle_p ||activity.status === 'Completada'"
                    @change="toggleItem(item)"
                    class="checkbox checkbox-xs text-neutral" />

                    <input v-if="editingId === item.id" v-model="editText" type="text"
                        class="input input-xs flex-1 outline-none" @keyup.enter="updateItem(item)"
                        @keyup.esc="editingId = null" />

                    <span v-else class="text-xs font-semibold flex-1 cursor-text"
                        @dblclick="activity.update_p && startEdit(item)"
                        :class="{ 'line-through text-slate-400/50 cursor-not-allowed': item.is_completed }"
                        >
                        {{ item.description }}
                    </span>

                    <!-- Btn de acciones -->
                    <button class="btn btn-square btn-xs btn-ghost" @click="removeItem(item)" >
                        <PhTrash class="text-error size-4" weight="fill" />
                    </button>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';
import { PhCheckSquareOffset, PhPercent, PhXCircle, PhWarningCircle, PhTrash } from '@phosphor-icons/vue';

const props = defineProps({
    activity: Object,
})

const form = useForm({ description: '' })


const items = computed( () => props.activity.items ?? [])
const doneCount = computed ( () => items.value.filter(i => i.is_completed).length) // ? realiza el conteo de las acciones ya realizadas
const progress = computed ( () => // ? obtiene el % de las acciones realizadas del plan de acción
        items.value.length ?  Math.round((doneCount.value / items.value.length) * 100 ) : 0)
const editingId = ref(null)
const editText = ref('')

function addItem() {
    if (!form.description.trim()) return

    form.post(route('action.store', {activity: props.activity.id}), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => form.reset()
    })
}

function toggleItem(item) {
    router.patch(route('action.toggle-item', item.id), {}, {
        preserveScroll: true,
        preserveState: true
    })
}

function removeItem(item) {
    router.delete(route('action.destroy', item.id), {
        preserveScroll: true,
        preserveState: true
    })
}

function startEdit(item) {
    editingId.value = item.id
    editText.value = item.description
}

function updateItem(item) {
    if (!editText.value.trim()) return

    router.put(route('action.update', item.id), { description: editText.value }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => editingId.value = null,
    })
}
</script>
