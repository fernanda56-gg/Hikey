<template>
    <!--Contenedor global-->
    <div class="flex flex-col h-full">
        <!-- Nombre del proyecto -->
        <div class="flex items-center">
            <h1 class="font-bold md:text-xl text-lg uppercase">{{ projects.name }}</h1>
        </div>

        <!-- Área del proyecto -->
        <div class="flex items-center mt-1.5">
                <span :class="[
                    projects.area.name === 'Desarrollo' ? 'text-[#0496ff]':
                    projects.area.name === 'Marketing' ? 'text-[#f2b705]':
                    projects.area.name === 'Recursos Humanos' ? 'text-[#ff65b3]':
                    projects.area.name === 'Finanzas' ? 'text-[#52b788]':
                    projects.area.name === 'Producción' ? 'text-[#f26419]':
                    'text-[#9a8f97]'
                ]" class="flex items-center gap-2 text-sm font-black">
                    <component :is="areaIcons[projects.area.name]" class="size-6" />
                    {{ projects.area.name }}
                </span>
        </div>

        <!-- Link de proyecto -->
        <div class="flex items-center mt-1.5">
            <a :href="projects.link" class="link-hover flex items-center gap-2 text-sm uppercase font-semibold duration-200 hover:duration-200 hover:text-accent" target="_blank" rel="noopener noreferrer">
                <span>Link de proyecto</span>
                <PhLink class="size-5" weight="duotone"/>
            </a>
        </div>

        <!-- Estatus del proyecto -->
        <div class="flex items-center mt-4 space-x-2 text-neutral font-bold">
            <span :class="[
                projects.status === 'Pendiente' ? 'status bg-[#d90429] status-lg' :
                projects.status === 'En progreso' ? 'status bg-[#ffc300] status-lg' :
                'status bg-[#70e000] status-lg'
            ]">
            </span>
            <span>
                {{ projects.status }}
            </span>
        </div>

        <div class="flex items-end justify-end space-x-3 mt-auto">
            <Link :href="route('projects.show', {project: projects.id})"><PhEye :size="28" weight="duotone" class="hover:text-info hover:duration-200 duration-200"/></Link>
            <Link :href="route('projects.edit', {project: projects.id})"><PhPencil :size="28" weight="duotone" class="hover:text-warning hover:duration-200 duration-200"/></Link>
            <Link :href="route('projects.destroy', {project: projects.id})" method="delete" as="button"><PhTrash :size="28" weight="duotone" class="hover:text-error hover:duration-200 duration-200 cursor-pointer"/></Link>
        </div>
    </div>
</template>
<script setup>
    import { PhLink, PhCode, PhChartLineUp, PhPerson, PhCoins, PhGearSix, PhLightbulbFilament, PhEye, PhPencil, PhTrash, } from '@phosphor-icons/vue';
    import { Link } from '@inertiajs/vue3';

    defineProps(
            {'projects': Object,}
        );

    const areaIcons = {
        'Desarrollo': PhCode,
        'Marketing': PhChartLineUp,
        'Recursos Humanos': PhPerson,
        'Finanzas': PhCoins,
        'Producción': PhGearSix,
        'Otros': PhLightbulbFilament,
    }
</script>
