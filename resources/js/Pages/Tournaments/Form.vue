<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    tournament: Object,
    sports: Array,
    formats: Array,
    statuses: { type: Array, default: () => [] },
    venues: Array,
});

const isEdit = !!props.tournament;

const form = useForm({
    name: props.tournament?.name ?? '',
    description: props.tournament?.description ?? '',
    rules: props.tournament?.rules ?? '',
    sport: props.tournament?.sport ?? props.sports[0],
    format: props.tournament?.format ?? props.formats[0],
    venue_id: props.tournament?.venue_id ?? (props.venues[0]?.id ?? ''),
    city: props.tournament?.city ?? '',
    start_date: props.tournament?.start_date ?? '',
    end_date: props.tournament?.end_date ?? '',
    registration_deadline: props.tournament?.registration_deadline?.slice(0, 16) ?? '',
    max_teams: props.tournament?.max_teams ?? 8,
    entry_fee: props.tournament?.entry_fee ?? 0,
    prize_info: props.tournament?.prize_info ?? '',
    status: props.tournament?.status,
});

function submit() {
    if (isEdit) {
        form.put(route('tournaments.update', props.tournament.slug));
    } else {
        form.post(route('tournaments.store'));
    }
}
</script>

<template>
    <Head :title="isEdit ? 'Edit Tournament' : 'Create Tournament'" />

    <AppLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ isEdit ? 'Edit Tournament' : 'Create Tournament' }}</h1>
        </template>

        <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
            <form @submit.prevent="submit" class="space-y-6 rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div>
                    <InputLabel for="name" value="Tournament name" />
                    <TextInput id="name" v-model="form.name" class="mt-1 block w-full" required />
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
                        <InputLabel for="format" value="Format" />
                        <select id="format" v-model="form.format" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option v-for="f in formats" :key="f" :value="f">{{ f === 'knockout' ? 'Knockout' : 'Round Robin' }}</option>
                        </select>
                    </div>
                </div>

                <div>
                    <InputLabel for="venue_id" value="Host venue (optional)" />
                    <select id="venue_id" v-model="form.venue_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">No specific venue</option>
                        <option v-for="v in venues" :key="v.id" :value="v.id">{{ v.name }}</option>
                    </select>
                    <InputError :message="form.errors.venue_id" class="mt-1" />
                </div>

                <div>
                    <InputLabel for="city" value="City (if no venue selected)" />
                    <TextInput id="city" v-model="form.city" class="mt-1 block w-full" />
                </div>

                <div>
                    <InputLabel for="description" value="Description" />
                    <textarea id="description" v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                </div>

                <div>
                    <InputLabel for="rules" value="Rules" />
                    <textarea id="rules" v-model="form.rules" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="start_date" value="Start date" />
                        <TextInput id="start_date" type="date" v-model="form.start_date" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.start_date" class="mt-1" />
                    </div>
                    <div>
                        <InputLabel for="end_date" value="End date" />
                        <TextInput id="end_date" type="date" v-model="form.end_date" class="mt-1 block w-full" />
                    </div>
                </div>

                <div>
                    <InputLabel for="registration_deadline" value="Registration deadline" />
                    <TextInput id="registration_deadline" type="datetime-local" v-model="form.registration_deadline" class="mt-1 block w-full" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="max_teams" value="Max teams" />
                        <TextInput id="max_teams" type="number" min="2" v-model="form.max_teams" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <InputLabel for="entry_fee" value="Entry fee (৳, 0 = free)" />
                        <TextInput id="entry_fee" type="number" min="0" step="0.01" v-model="form.entry_fee" class="mt-1 block w-full" required />
                    </div>
                </div>

                <div>
                    <InputLabel for="prize_info" value="Prize info (optional)" />
                    <TextInput id="prize_info" v-model="form.prize_info" class="mt-1 block w-full" placeholder="e.g. ৳30,000 winner prize" />
                </div>

                <div v-if="isEdit">
                    <InputLabel for="status" value="Status" />
                    <select id="status" v-model="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
                    </select>
                </div>

                <PrimaryButton :disabled="form.processing">{{ isEdit ? 'Save Changes' : 'Create Tournament' }}</PrimaryButton>
            </form>
        </div>
    </AppLayout>
</template>
