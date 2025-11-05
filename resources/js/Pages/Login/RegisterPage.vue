<template>

    <!--Contenedor global-->
    <div class="flex min-h-screen justify-center items-center bg-background-day">

        <!--Contedor del registro-->
        <div class="flex flex-col relative bg-white rounded-2xl shadow-2xl m-6 space-y-10 md:flex-row md:space-y-0">

            <!--Lado izquierdo-->
            <div class="p-6 md:p-20">
                <h2 class="font-bold text-4xl">Registrate</h2>
                <p class="text-gray-500 font-light mb-12 max-w-md mt-2">Unete a hikey y comienza a administrar tus proyectos</p>

                <!--Formulario-->
                <p class="font-medium text-sm text-black m-2">Email</p>
                <input type="text" class="w-full font-light rounded-md border border-gray-300 p-4" placeholder="Ingresa tu correo electronico">
                <p class="font-medium text-sm text-black m-2">Contraseña</p>
                <input type="text" class="w-full font-light rounded-md border border-gray-300 p-4" placeholder="Ingresa tu contraseña">
                <p class="font-medium text-sm text-black m-2">Confirmar contraseña</p>
                <input type="text" class="w-full font-light rounded-md border border-gray-300 p-4" placeholder="Confirma tu contraseña">

                <div class="flex flex-row space-x-4">
                    <div class="w-1/2">
                        <p class="block font-medium text-sm text-black m-2">Nombre</p>
                        <input type="text" class="block w-full font-light rounded-md border border-gray-300 p-4" placeholder="Nombre">
                    </div>
                    <div class="w-1/2">
                        <p class="block font-medium text-sm text-black m-2">Apellidos</p>
                        <input type="text" class="block w-full font-light rounded-md border border-gray-300 p-4" placeholder="Apellidos">
                    </div>
                </div>

                <div class="flex flex-row space-x-4">
                    <div class="w-1/2">
                        <p class="block font-medium text-sm text-black m-2">Telefono</p>
                        <input type="text" class="block w-full font-light rounded-md border border-gray-300 p-4" placeholder="Telefono">
                    </div>
                    <div class="w-1/2">
                        <p class="block font-medium text-sm text-black m-2">Empresa</p>
                        <input type="text" class="block w-full font-light rounded-md border border-gray-300 p-4" placeholder="Ej: Google, Facebook, etc">
                    </div>
                </div>


                <!--Contenido final-->
                <div class="flex item-center justify-end mt-4 space-y-6 md:flex-row md:space-y-0">
                    <Link href="/" class="font-light text-blue-600">Inicia sesion</Link>
                </div>

                <div class="flex flex-row item-center justify-center m-3">
                    <button class="flex w-full bg-primary-v font-bold text-white rounded-xl justify-center items-center p-3 space-x-4 px-6 duration-500 hover:bg-primary hover:shadow-2xl">
                        <span>Registrate</span>
                    </button>
                </div>

                <!--Borde-->
                <div class="border-b border-b-gray-300 mt-12"></div>
                <div class="flex justify-center items-center m-4">logo</div>
            </div>

            <!--Lado izquierdo-->
            <img src="images/ocean.avif" class="w-[500px] hidden md:block rounded-r-xl">
        </div>
    </div>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3';
</script>


