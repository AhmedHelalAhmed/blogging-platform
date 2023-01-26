<script setup>
defineProps(['title', 'description', 'publishedAt']);
</script>

<template>
    <article>
        <div class="border shadow-lg mt-8 py-1 px-4 border 2xl:shadow-md">
            <div>
                <h2 class="mb-3 uppercase border-black-600 font-bold shadow-sm pt-2 pb-2">
                    {{ title }}
                </h2>
                <div class="break-normal whitespace-pre-wrap">
                    <div>{{ description }}</div>
                </div>
                <div class="text-right">
                    <small class="font-bold text-gray-600">{{ publishedAt }}</small>
                </div>
            </div>
        </div>
    </article>
</template>
