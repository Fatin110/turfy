<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    sports: Array,
});

const form = useForm({
    name: '',
    description: '',
    sport: props.sports[0],
    season: new Date().getFullYear().toString(),
    city: 'Dhaka',
    start_date: '',
    end_date: '',
    double_round_robin: true,
});

function submit() {
    form.post(route('admin.leagues.store'));
}
</script>

<template>
    <Head title="Create League" />

    <AppLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create a League</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Bring venues together — add teams and generate fixtures next.</p>
        </template>

        <div class="mx-auto max-w-xl px-4 py-8 sm:px-6 lg:px-8">
            <form @submit.prevent="submit" class="space-y-6 rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div>
                    <InputLabel for="name" value="League name" />
                    <TextInput id="name" v-model="form.name" class="mt-1 block w-full" required placeholder="e.g. Dhaka Football Premier League" />
                    <InputError :message="form.errors.name" class="mt-1" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="sport" value="Sport" />
                        <select id="sport" v-model="form.sport" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option v-for="s in sports" :key="s" :value="s">{{ s === 'cricket' ? '🏏 Cricket' : '⚽ Football' }}</option>
                        </select>
                    </div>
                    <div>
                        <InputLabel for="season" value="Season" />
                        <TextInput id="season" v-model="form.season" class="mt-1 block w-full" required />
                    </div>
                </div>

                <div>
                    <InputLabel for="description" value="Description" />
                    <textarea id="description" v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="start_date" value="Start date" />
                        <TextInput id="start_date" type="date" v-model="form.start_date" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel for="end_date" value="End date" />
                        <TextInput id="end_date" type="date" v-model="form.end_date" class="mt-1 block w-full" />
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <input type="checkbox" v-model="form.double_round_robin" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                    Home &amp; away (double round robin), like La Liga / Premier League
                </label>

                <PrimaryButton :disabled="form.processing">Create League</PrimaryButton>
            </form>
        </div>
    </AppLayout>
</template>
