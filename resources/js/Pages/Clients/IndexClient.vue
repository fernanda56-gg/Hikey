<template>
    <AppLayout>
        <!-- Contenedor global -->
        <div class="text-neutral mx-auto container p-4">
            <!-- Titulo de la pagina -->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-6xl text-2xl md:m-10 m-4 p-2">Clientes</h1>
            </div>

            <!-- Tabla de info de los clientes de la empresa -->
            <ClientTableInfo :clients="clients" :filters="filters"/>
        </div>
    </AppLayout>
</template>

<script setup>
import ClientTableInfo from '../../Components/UI/ClientTableInfo.vue';
import AppLayout from '../../Layouts/AppLayout.vue';

defineProps({
    'clients': Object,
    'filters': Object,
});

</script>
