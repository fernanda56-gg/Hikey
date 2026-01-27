    <template>
    <AppLayout>
        <!--Contenedor global-->
        <div class="text-neutral container mx-auto p-4">
            <!--Titulo de pagina-->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-6xl text-2xl md:m-10 m-4 p-2">proyectos</h1>
            </div>

            <!--Botón crear nuevo proyecto-->
            <div class="flex items-center justify-end w-full">
                <div class="btn md:btn-md btn-sm text-black bg-primary md:font-bold border-0 hover:bg-primary-content hover:duration-200 duration-200">
                    <Link :href="route('projects.create')" class="flex items-center space-x-1">
                        <PhPlus class="md:size-6 size-4" />
                        <span>Nuevo proyecto</span>
                    </Link>
                </div>
            </div>

            <!--Contenedor de info de proyectos-->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 m-8 pt-4">
                <BoxComponent v-for="project in projects" :key="project.id">
                    <ProjectDetails :projects="project" />
                    <!--Contenedor de acciones de proyecto-->
                    <div class="flex items-center justify-end space-x-3 mt-2">
                        <Link :href="route('projects.show', {project: project.id})"><PhEye :size="28" weight="duotone" class="hover:text-info hover:duration-200 duration-200"/></Link>
                        <Link :href="route('projects.edit', {project: project.id})"><PhPencil :size="28" weight="duotone" class="hover:text-warning hover:duration-200 duration-200"/></Link>
                        <Link :href="route('projects.destroy', {project: project.id})" method="delete" as="button"><PhTrash :size="28" weight="duotone" class="hover:text-error hover:duration-200 duration-200 cursor-pointer"/></Link>
                    </div>
                </BoxComponent>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
    import AppLayout from '../../Layouts/AppLayout.vue';
    import BoxComponent from '../../Components/UI/BoxComponent.vue';
    import ProjectDetails from '../../Components/UI/ProjectDetails.vue';
    import { Link } from '@inertiajs/vue3';
    import { PhEye, PhPencil, PhTrash, PhPlus} from '@phosphor-icons/vue';
    import { route } from 'ziggy-js';
    defineProps(
        {'projects': Object,}
    );
</script>
