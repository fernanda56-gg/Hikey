<template>
    <div>
        <div class="space-x-6 md:space-x-0 md:space-y-4 flex flex-row md:flex-col items-center justify-center m-4">
                        <Link :href="route('projects.edit', {project: projects.id})" class="btn text-neutral bg-warning/60 border-0 px-6"><PhPencilSimple class="md:size-6 size-4" />Editar</Link>
                        <Link :href="route('projects.destroy', {project: projects.id})" method="delete" as="button" class="btn text-neutral bg-error/60 border-0 px-4"><PhEraser class="md:size-6 size-4" />Eliminar</Link>
                    </div>

    </div>
</template>
<script setup>
    import { Link } from '@inertiajs/vue3';
    import { route } from 'ziggy-js';
    import { PhPencilSimple, PhEraser } from '@phosphor-icons/vue';
    defineProps(
                {'projects': Object,}
            );
</script>
