<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    venue: Object,
    sports: Array,
});

const isEdit = !!props.venue;

const form = useForm({
    name: props.venue?.name ?? '',
    description: props.venue?.description ?? '',
    city: props.venue?.city ?? '',
    address: props.venue?.address ?? '',
    phone: props.venue?.phone ?? '',
    cover_image: props.venue?.cover_image ?? '',
    sports: props.venue?.sports ?? [],
});

function submit() {
    if (isEdit) {
        form.put(route('venues.update', props.venue.slug));
    } else {
        form.post(route('venues.store'));
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Edit Venue' : 'List Your Venue'" />

    <AppLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ isEdit ? 'Edit Venue' : 'List Your Venue' }}</h1>
        </template>

        <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
            <form @submit.prevent="submit" class="space-y-6 rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div>
                    <InputLabel for="name" value="Venue name" />
                    <TextInput id="name" v-model="form.name" class="mt-1 block w-full" required />
                    <InputError :message="form.errors.name" class="mt-1" />
                </div>

                <div>
                    <InputLabel for="description" value="Description" />
                    <textarea id="description" v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                    <InputError :message="form.errors.description" class="mt-1" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="city" value="City / Area" />
                        <TextInput id="city" v-model="form.city" class="mt-1 block w-full" required placeholder="e.g. Gulshan" />
                        <InputError :message="form.errors.city" class="mt-1" />
                    </div>
                    <div>
                        <InputLabel for="phone" value="Phone" />
                        <TextInput id="phone" v-model="form.phone" class="mt-1 block w-full" />
                        <InputError :message="form.errors.phone" class="mt-1" />
                    </div>
                </div>

                <div>
                    <InputLabel for="address" value="Full address" />
                    <TextInput id="address" v-model="form.address" class="mt-1 block w-full" required />
                    <InputError :message="form.errors.address" class="mt-1" />
                </div>

                <div>
                    <InputLabel value="Sports offered" />
                    <div class="mt-2 flex gap-4">
                        <label v-for="s in sports" :key="s" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" :value="s" v-model="form.sports" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                            {{ s === 'cricket' ? '🏏 Cricket' : '⚽ Football' }}
                        </label>
                    </div>
                    <InputError :message="form.errors.sports" class="mt-1" />
                </div>

                <div>
                    <InputLabel for="cover_image" value="Cover image URL (optional)" />
                    <TextInput id="cover_image" v-model="form.cover_image" class="mt-1 block w-full" placeholder="https://..." />
                    <InputError :message="form.errors.cover_image" class="mt-1" />
                </div>

                <PrimaryButton :disabled="form.processing">{{ isEdit ? 'Save Changes' : 'Submit for Review' }}</PrimaryButton>
                <p v-if="!isEdit" class="text-xs text-gray-500 dark:text-gray-400">An admin will review and approve your venue before it appears publicly.</p>
            </form>
        </div>
    </AppLayout>
</template>
