<template>
    <div>
        <!--Info sobre el proyecto-->
        <span class="text-neutral uppercase font-bold text-lg md:text-xl xl:text-2xl block">{{ projects.name }}</span>
        <a :href="projects.link" class="mt-2 flex items-center space-x-1 text-accent font-bold" target="_blank" rel="noopener noreferrer">
            <span>Link de proyecto</span>
            <PhLink :size="20" weight="bold"/>
        </a>
        <!--Estatus de proyecto-->
        <div class="flex items-center mt-2 space-x-2 text-neutral font-bold">
            <span :class="[
                projects.status === 'planned' ? 'status bg-[#d90429] status-lg' :
                projects.status === 'in_progress' ? 'status bg-[#ffc300] status-lg' :
                'status bg-[#70e000] status-lg'
            ]">
            </span>
            <span>
                {{ projects.status === 'planned' ? 'Pendiente' :
                projects.status === 'in_progress' ? 'En progreso' :
                'Completado' }}
            </span>
        </div>
    </div>
</template>
<script setup>
    import { PhLink } from '@phosphor-icons/vue';
    defineProps(
            {'projects': Object,}
        );
</script>
