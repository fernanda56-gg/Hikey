<template>
    <div>
        <div class="flex flex-col space-y-2">
            <Link :href="route('projects.edit', {project: projects.id})" class="btn rounded-lg border-amber-400 bg-amber-200/50 text-neutral border-2">Editar proyecto</Link>
            <Link :href="route('projects.destroy', {project: projects.id})" method="delete" as="button" class="btn rounded-lg border-red-400 bg-red-200/50 text-neutral border-2">Eliminar proyecto</Link>
            <Link class="btn btn-info font-bold text-base-100">opción 1</Link>
        </div>

    </div>
</template>
<script setup>
    import { Link } from '@inertiajs/vue3';
    import { route } from 'ziggy-js';
    defineProps(
                {'projects': Object,}
            );
</script>
