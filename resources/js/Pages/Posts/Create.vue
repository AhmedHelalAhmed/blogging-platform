<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, useForm} from '@inertiajs/vue3';
import WriteLayout from "@/Layouts/WriteLayout.vue";

const form = useForm({
    title: '',
    description: '',
});

const submit = () => {
    // client side validation

    if (!form.title.trim()) {
        form.errors.title = 'The title is required.';
        return;
    }

    if (form.title.trim().length < 3 || form.title.trim().length >= 255) {
        form.errors.title = 'The title must be between 3 and 255 characters.';
        return;
    }
    if (!form.description.trim()) {
        form.errors.description = 'The description is required.';
        return;
    }

    if (form.description.trim().length < 30 || form.description.trim().length >= 600) {
        form.errors.description = 'The description must be between 30 and 600 characters';
        return;
    }
    form.post(route('posts.store'));
};
</script>

<template>
    <WriteLayout>
        <Head title="Create new post"/>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="title" value="Title"/>

                <TextInput
                    id="title"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.title"
                    required
                    autofocus
                    autocomplete="Title"
                />

                <InputError class="mt-2" :message="form.errors.title"/>

            </div>

            <div class="mt-4">
                <InputLabel for="description" value="Description"/>

                <textarea
                    id="description"
                    class="mt-1 block w-full"
                    v-model="form.description"
                    required
                    rows="10"
                    autocomplete="description"
                    minlength="30"
                    maxlength="600"
                />

                <InputError class="mt-2" :message="form.errors.description"/>
            </div>


            <div class="flex items-center justify-end mt-4">

                <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Submit
                </PrimaryButton>
            </div>
        </form>
    </WriteLayout>
</template>
