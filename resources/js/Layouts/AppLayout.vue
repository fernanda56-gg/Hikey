<template>
    <NavbarComponent>
        <slot></slot>
    </NavbarComponent>
</template>

<script setup>

//Notificación de éxito o error
import { usePage, router } from "@inertiajs/vue3"
import { Notyf } from "notyf"
import "notyf/notyf.min.css"
import NavbarComponent from "../Components/UI/NavbarComponent.vue"

const page = usePage()
const notyf = new Notyf({
    duration: 3000,
    position: { x: 'right', y: 'top' }
})

//router se encarga de manejar los eventos de inertia
router.on('navigate', () => {
    const flash = page.props.flash

    if (flash?.success) { //evento en caso de éxito
    notyf.success(flash.success)
    flash.success = null
    }

    if (flash?.error) {//evento en caso de error
    notyf.error(flash.error)
    flash.error = null
    }
})

/* watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) notyf.success(flash.success)
        if (flash?.error) notyf.error(flash.error)
    }
) */
</script>
