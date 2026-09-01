<template>
    <NavbarComponent>
        <slot></slot>
    </NavbarComponent>
</template>

<script setup>

//Notificación de éxito o error
import { usePage} from "@inertiajs/vue3"
import { Notyf } from "notyf"
import "notyf/notyf.min.css"
import NavbarComponent from "../Components/UI/NavbarComponent.vue"
import { watch } from "vue"

const page = usePage()
const notyf = new Notyf({
    duration: 3000,
    position: { x: 'right', y: 'top' }
})

/* Para notificaciones cuando redirige y se monta el componente de inmediato lanza la alerta al sistema */
watch( () => page.props.flash?.success,
    (message) => {
        /* console.log('Success:', message) */
        if (message) {
            notyf.success(message)
        }
    },
    { immediate: true }
)

watch( () => page.props.flash?.error,
    (message) => {
        /* console.log('Error:', message) */
        if (message) {
            notyf.error(message)
        }
    },
    { immediate: true }
)
</script>
