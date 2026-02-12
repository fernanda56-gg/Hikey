<template>
    <AppLayout>
        <!--Contenedor global-->
        <div class="container text-neutral mx-auto p-4">
            <!--Titulo de pagina-->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-6xl text-2xl md:m-10 m-4 p-2">información de proyecto</h1>
            </div>
            <!--Contenedor de información de proyecto-->
            <div class="flex flex-col lg:flex-row md:p-4 text-neutral">
                <!--Contenedor de información (grande)-->
                <div class="card border-2 border-base-300 bg-base-200 rounded-box h-auto min-w-4/5 grow place-items-start p-4">
                    <ProjectInformation :projects="project" />
                </div>

                <div class="divider lg:divider-horizontal"></div>

                <!--Contenedor de acciones (pequeño)-->
                <div class="card bg-transparent rounded-box lg:h-32 h-20 w-full grow place-items-center lg:place-items-start">
                    <ProjectActions :project="project" :can="can" />
                    <div class="divider"></div>
                    <ClientButton :projects="project"/>
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
    defineProps(
        {'project': Object,
            'can': Object,
        }
    )
</script>

