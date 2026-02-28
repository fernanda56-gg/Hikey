<template>
<div v-for="(link, index) in links" :key="index" class="join">
    <Link v-if="link.url" :href="link.url"
            class="join-item btn text-mist-500 font-black"
            :class="{ 'btn-primary text-stone-950': link.active }">
            <span v-html="link.label"></span>
    </Link>

    <button v-else class="join-item btn btn-disabled" disabled>
        <span v-html="link.label"></span>
    </button>
</div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    'links': Array,
})
</script>
