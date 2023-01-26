<script setup>
import {Head, Link} from '@inertiajs/vue3';
import Post from '@/Components/Post.vue';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    posts: Object,
    sortByPublicationDate: Number,
    optionsForSort: Object,
});

const isSelected = (value, optionValue) => {
    return parseInt(value) === parseInt(optionValue);
};
</script>

<template>
    <Head title="Welcome"/>

    <div
        class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0"
    >
        <div v-if="canLogin" class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="text-sm underline"
            >Dashboard
            </Link
            >

            <template v-else>
                <Link :href="route('login')" class="text-sm text-gray-700 dark:text-gray-500 underline">Log in</Link>

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline"
                >Register
                </Link
                >
            </template>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="w-[70rem] p-6  bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="GET" v-if="posts.data.length">
                    <label for="sort-by" class="block mt-2 text-sm font-medium text-gray-900 dark:text-white ">
                        Sort by publication date:
                    </label>
                    <div class="flex">
                        <select
                            name="sort[published_at]"
                            id="sort-by"
                            class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option v-for="option in optionsForSort" :key="option.value" :value="option.value"
                                    :selected="isSelected(sortByPublicationDate,option.value)">{{ option.text }}
                            </option>
                        </select>

                        <button type="submit"
                                class="ml-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                            Sort
                        </button>

                    </div>

                </form>
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
</template>
