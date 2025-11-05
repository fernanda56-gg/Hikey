<template>
    <AppLayout>
        <!--Contenedor global-->
        <div class="container text-neutral mx-auto p-4">
            <!--Titulo de pagina-->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-6xl text-2xl md:m-10 m-4 p-2">información de proyecto</h1>
            </div>
            <!--Contenedor de información de proyecto-->
            <div class="flex items-start justify-start md:space-x-4 md:mx-8 mx-auto pt-4">
                <!--Contenedor de información (grande)-->
                <div class="flex-1 p-4 bg-base-200 border-2 border-base-300 rounded-lg md:w-5/6">
                    <ProjectInformation :projects="project" />
                </div>

                <!--Contenedor de acciones (pequeño)-->
                <div class="hidden md:flex items-center justify-center space-y-2 md:w-1/6 border-2 border-base-300 rounded-lg bg-base-200 p-4">
                    <ProjectActions :projects="project" />
                </div>
            </div>
            <!--Contenedor acciones vista celular-->
            <div class="flex items-center justify-center border-2 border-base-300 rounded-lg bg-base-200 p-4 mt-2 w-full md:hidden">
                <ProjectActions :projects="project" />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
    import AppLayout from '../../Layouts/AppLayout.vue';
    import ProjectInformation from '../../Components/ProjectInformation.vue';
    import ProjectActions from '../../Components/ProjectActions.vue';
    defineProps(
        {'project': Object,}
    )
</script>

