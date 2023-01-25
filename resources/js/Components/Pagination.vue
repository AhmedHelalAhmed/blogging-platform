<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<script>
export default {
    props: {
        data: Object
    },
}
</script>

<template>
    <div v-if="data.links.length > 3" class="mb-4 mt-4 flex justify-center space-x-1">
        <Link
            v-for="(link, k) in data.links"
            :key="k"
            class="px-4 py-3 text-sm leading-4 rounded hover:bg-white border focus:text-indigo-500 hover:shadow bg-white"
            :class="{'bg-indigo-400 text-white': link.active}"
            :href="link.url"
            v-html="link.label"
        />
    </div>
</template>
