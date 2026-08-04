<template>
    <AppLayout>
        <!--Contenedor global-->
        <div class="container text-neutral mx-auto p-4 mb-8">
            <!--Titulo de pagina-->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-6xl text-2xl md:m-10 m-4 p-2">información de proyecto</h1>
            </div>

            <!-- Link breadcrumbs -->
            <div class="breadcrumbs px-4 py-1.5 text-xs md:text-sm">
                <ul>
                    <li><Link :href="route('inicio')"><PhHouseLine class="md:size-6 size-5 cursor-pointer hover:text-success duration-200 hover:duration-200" weight="duotone" /></Link></li>
                    <li><Link :href="route('projects.index')" class="hover:text-success duration-200 hover:duration-200 font-semibold">Proyectos</Link></li>
                    <li>{{ project.name }}</li>
                </ul>
            </div>

            <!--Contenedor de información de proyecto-->
            <div class="flex flex-col lg:flex-row md:p-4 text-neutral">
                <!--Contenedor de información (grande)-->
                <div class="card border-2 border-base-300 bg-base-200 rounded-box h-auto min-w-2/3 grow place-items-start p-4">
                    <ProjectInformation :projects="project" />
                </div>

                <div class="divider lg:divider-horizontal"></div>

                <!--Contenedor de acciones (pequeño)-->
                <div class="card bg-transparent rounded-box lg:h-32 h-20 w-full grow place-items-center lg:place-items-start">
                    <ProjectActions :project="project" :can="can" />
                    <div class="divider"></div>
                    <ClientButton :projects="project"/>
                    <div class="divider"></div>
                    <TeamInfo :project="project" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
    import AppLayout from '../../Layouts/AppLayout.vue';
    import ProjectInformation from '../../Components/UI/ProjectInformation.vue';
    import ProjectActions from '../../Components/UI/ProjectActions.vue';
    import ClientButton from '../../Components/UI/ClientButton.vue';
    import TeamInfo from '../../Components/UI/TeamInfo.vue';
    import { PhHouseLine } from '@phosphor-icons/vue';
    import { Link } from '@inertiajs/vue3';
    import { route } from 'ziggy-js';
    defineProps({
        'project': Object,
        'can': Object,
        })
</script>

