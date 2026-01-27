<template>
    <div>
        <div class="space-x-6 md:space-x-0 md:space-y-4 flex flex-row md:flex-col items-center justify-center m-4">
            <Link :href="route('projects.edit', {project: projects.id})" class="flex items-center gap-2 font-bold link link-hover hover:text-[#f8961e] hover:duration-200 mr-4"><PhPencil class="md:size-6 size-5" />Editar</Link>
            <Link :href="route('projects.destroy', {project: projects.id})" method="delete" as="button" class="flex items-center gap-2 font-bold link link-hover hover:text-error hover:duration-200"><PhTrash class="md:size-6 size-5" />Eliminar</Link>
        </div>
    </div>
</template>
<script setup>
    import { Link } from '@inertiajs/vue3';
    import { route } from 'ziggy-js';
    import { PhPencil, PhTrash } from '@phosphor-icons/vue';
    defineProps(
                {'projects': Object,}
            );
</script>
