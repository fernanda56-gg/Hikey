<template>

    <!--Contenedor global-->
    <div class="flex min-h-screen justify-center items-center bg-background-day">

        <!--Contedor del login-->
        <div class="flex flex-col relative bg-white rounded-2xl shadow-2xl m-6 space-y-10 md:flex-row md:space-y-0">

            <!--Lado izquierdo-->
            <div class="p-6 md:p-20">
                <h2 class="font-bold text-4xl">Iniciar sesion</h2>
                <p class="text-gray-500 font-light mb-12 max-w-sm mt-2">Inicia sesion para poder acceder a tu cuenta</p>

                <!--Formulario-->
                <p class="font-medium text-sm text-black m-2">Email</p>
                <input type="text" class="w-full font-light rounded-md border border-gray-300 p-4" placeholder="Ingresa tu correo electronico">
                <p class="font-medium text-sm text-black m-2">Contraseña</p>
                <input type="text" class="w-full font-light rounded-md border border-gray-300 p-4" placeholder="Ingresa tu contraseña">

                <!--Contenido final-->
                <div class="flex item-center justify-between mt-4 space-y-6 md:flex-row md:space-y-0">
                    <Link href="/register" class="text-blue-600 text-sm font-light">Registrate</Link>
                    <Link href="#" class="text-blue-600 text-sm font-light">¿Olvide mi contraseña?</Link>
                </div>

                <div class="flex flex-row item-center justify-center m-3">
                    <button class="flex w-full bg-primary-v font-bold text-white rounded-xl justify-center items-center p-3 space-x-4 px-6 duration-500 hover:bg-primary hover:shadow-2xl">
                        <Link href="/home" method="get" as="button">Iniciar sesion</Link>
                    </button>
                </div>

                <!--Borde-->
                <div class="border-b border-b-gray-300 mt-12"></div>
                <div class="flex justify-center items-center m-4">logo</div>
            </div>

            <!--Lado izquierdo-->
            <img src="images/ocean.avif" class="w-[430px] hidden md:block rounded-r-xl">
        </div>
    </div>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3';

</script>

