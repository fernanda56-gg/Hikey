    <template>
    <AppLayout>
        <!--Contenedor global-->
        <div class="text-neutral container mx-auto p-4">
            <!--Titulo de pagina-->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-6xl text-2xl md:m-10 m-4 p-2">proyectos</h1>
            </div>

            <!--Botón crear nuevo proyecto-->
            <div v-if="can('create projects')" class="flex items-center justify-end w-full">
                <div class="btn md:btn-md btn-sm text-black bg-primary md:font-bold border-0 hover:bg-primary-content hover:duration-200 duration-200">
                    <Link :href="route('projects.create')" class="flex items-center space-x-1">
                        <PhPlus class="md:size-6 size-4" />
                        <span>Nuevo proyecto</span>
                    </Link>
                </div>
            </div>

            <!--Contenedor de info de proyectos-->
            <div v-if="projects.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 m-8 pt-4">
                <BoxComponent v-for="project in projects" :key="project.id">
                    <ProjectDetails :projects="project" />
                </BoxComponent>
            </div>

            <!-- Contenedor en caso de que el usuario aun no este registrado a una empresa -->
            <div v-else class="p-4">
                <div class="flex items-center w-full justify-start border-2 border-base-300 bg-base-200 rounded-lg p-4">
                    Únete a una empresa para comenzar a trabajar.
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
    import AppLayout from '../../Layouts/AppLayout.vue';
    import BoxComponent from '../../Components/UI/BoxComponent.vue';
    import ProjectDetails from '../../Components/UI/ProjectDetails.vue';
    import { Link } from '@inertiajs/vue3';
    import {PhPlus} from '@phosphor-icons/vue';
    import { route } from 'ziggy-js';
    import { usePermission } from '../../composables/usePermission';

    //Comprobar permisos de usuario
    const {can} = usePermission();

    defineProps(
        {'projects': Object,}
    );
</script>
