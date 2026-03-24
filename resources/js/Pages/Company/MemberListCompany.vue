<template>
<AppLayout>
    <!--Contenedor global-->
    <div class="text-neutral container mx-auto p-4">
        <!--Titulo de pagina-->
            <div class="py-3 bg-primary-content/80 my-4 flex justify-start items-center md:rounded-3xl rounded-xl">
                <h1 class="text-neutral uppercase font-bold md:text-5xl text-2xl md:m-10 m-4 p-2">{{ company.name }}</h1>
            </div>

        <!--Tabla de info de miembros de la empresa-->
        <MemberTableInfo :company="company" :members="members" :filters="filters" :can="can"/>
    </div>
</AppLayout>

</template>

<script setup>
import MemberTableInfo from '../../Components/UI/MemberTableInfo.vue';
import AppLayout from '../../Layouts/AppLayout.vue';

defineProps({
    'company': Object,
    'members': Object,
    'can': Object,
    'filters': Object,
})
</script>

