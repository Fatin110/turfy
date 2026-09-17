<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    team: Object,
    sports: Array,
});

const isEdit = !!props.team;

const form = useForm({
    name: props.team?.name ?? '',
    sport: props.team?.sport ?? props.sports[0],
    city: props.team?.city ?? '',
});

function submit() {
    if (isEdit) {
        form.put(route('teams.update', props.team.slug));
    } else {
        form.post(route('teams.store'));
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Edit Team' : 'Create Team'" />

    <AppLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ isEdit ? 'Edit Team' : 'Create a Team' }}</h1>
        </template>

        <div class="mx-auto max-w-lg px-4 py-8 sm:px-6 lg:px-8">
            <form @submit.prevent="submit" class="space-y-6 rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div>
                    <InputLabel for="name" value="Team name" />
                    <TextInput id="name" v-model="form.name" class="mt-1 block w-full" required />
                    <InputError :message="form.errors.name" class="mt-1" />
                </div>

                <div v-if="!isEdit">
                    <InputLabel for="sport" value="Sport" />
                    <select id="sport" v-model="form.sport" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option v-for="s in sports" :key="s" :value="s">{{ s === 'cricket' ? '🏏 Cricket' : '⚽ Football' }}</option>
                    </select>
                </div>

                <div>
                    <InputLabel for="city" value="City" />
                    <TextInput id="city" v-model="form.city" class="mt-1 block w-full" />
                </div>

                <PrimaryButton :disabled="form.processing">{{ isEdit ? 'Save Changes' : 'Create Team' }}</PrimaryButton>
            </form>
        </div>
    </AppLayout>
</template>
