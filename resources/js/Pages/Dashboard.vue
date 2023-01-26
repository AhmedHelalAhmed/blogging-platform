<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import Post from '@/Components/Post.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    posts: Object,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div :class="{'py-12': !$page.props.flash.message&&!$page.props.flash.error}">

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <Link
                            :href="route('posts.create')"
                            class="ml-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full"
                        >Create new post</Link>
                        <div v-for="post in posts.data" :key="post.id">
                            <Post
                                :title="post.title"
                                :description="post.description"
                                :published-at="post.published_at"/>
                        </div>
                        <div v-if="!posts.data.length" class="mt-6 border shadow-lg py-10 px-4 font-bold text-center">
                            No posts to show
                        </div>
                        <Pagination :data="posts"/>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
