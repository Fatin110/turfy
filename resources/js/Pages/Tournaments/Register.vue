<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    tournament: Object,
    eligibleTeams: Array,
});

const form = useForm({
    team_id: props.eligibleTeams[0]?.id ?? '',
});

function submit() {
    form.post(route('registrations.store', props.tournament.slug));
}
</script>

<template>
    <Head :title="`Register — ${tournament.name}`" />

    <AppLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Register for {{ tournament.name }}</h1>
        </template>

        <div class="mx-auto max-w-xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Entry fee:
                    <span class="font-semibold">{{ Number(tournament.entry_fee) > 0 ? `৳${tournament.entry_fee} (pay via bKash)` : 'Free' }}</span>
                </p>

                <div v-if="eligibleTeams.length" class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Choose your team</label>
                    <select v-model="form.team_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option v-for="t in eligibleTeams" :key="t.id" :value="t.id">{{ t.name }}</option>
                    </select>
                    <InputError :message="form.errors.team_id" class="mt-1" />

                    <PrimaryButton @click="submit" :disabled="form.processing" class="mt-6">
                        {{ Number(tournament.entry_fee) > 0 ? 'Continue to Payment' : 'Confirm Free Registration' }}
                    </PrimaryButton>
                </div>
                <div v-else class="mt-6 rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-700">
                    You don't have an eligible team for this sport yet.
                    <Link :href="route('teams.create')" class="mt-2 block font-medium text-emerald-600 hover:underline">Create a team &rarr;</Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
