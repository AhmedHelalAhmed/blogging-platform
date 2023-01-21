<script setup>
defineProps(['title', 'description', 'publishedAt']);
</script>

<template>
    <article class="w-[70rem]">
        <div class="border shadow-lg mt-8 py-1 px-4 border 2xl:shadow-md">
            <div>
                <h2 class="mb-3 uppercase border-black-600 shadow-sm">
                    {{ title }}
                </h2>
                <p class="break-words">{{ description }}</p>
                <time class="text-sm font-bold text-gray-600" datetime="2022-02-24T00:00:00.000Z">{{ publishedAt }}</time>
            </div>
        </div>
    </article>
</template>
