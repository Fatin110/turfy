<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import Pagination from '@/Components/Pagination.vue';
import { tournamentStatus, sportEmoji } from '@/statusMeta';

const props = defineProps({
    tournaments: Object,
    sports: Array,
    statuses: Array,
    filters: Object,
});

const form = reactive({
    sport: props.filters.sport ?? '',
    status: props.filters.status ?? '',
    city: props.filters.city ?? '',
});

function applyFilters() {
    router.get(route('tournaments.index'), form, { preserveState: true, replace: true });
}
</script>

<template>
    <Head title="Tournaments" />

    <AppLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tournaments</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Live, upcoming, and past tournaments across every venue.</p>
        </template>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-wrap gap-3 rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                <select v-model="form.sport" @change="applyFilters" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All sports</option>
                    <option v-for="s in sports" :key="s" :value="s">{{ s === 'cricket' ? '🏏 Cricket' : '⚽ Football' }}</option>
                </select>
                <select v-model="form.status" @change="applyFilters" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All statuses</option>
                    <option v-for="s in statuses" :key="s" :value="s">{{ tournamentStatus(s).label }}</option>
                </select>
            </div>

            <div v-if="tournaments.data.length" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="t in tournaments.data"
                    :key="t.id"
                    :href="route('tournaments.show', t.slug)"
                    class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="flex items-start justify-between">
                        <Badge :variant="tournamentStatus(t.status).variant">{{ sportEmoji(t.sport) }} {{ tournamentStatus(t.status).label }}</Badge>
                        <span class="text-xs font-medium text-gray-400">{{ Number(t.entry_fee) > 0 ? `৳${t.entry_fee}` : 'Free' }}</span>
                    </div>
                    <h3 class="mt-3 font-semibold text-gray-900 dark:text-white">{{ t.name }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ t.venue?.name ?? t.city }}</p>
                    <p class="mt-2 text-xs text-gray-400">{{ t.confirmed_registrations_count }}/{{ t.max_teams }} teams &middot; starts {{ t.start_date }}</p>
                </Link>
            </div>
            <div v-else class="rounded-xl border border-dashed border-gray-300 p-12 text-center text-gray-500 dark:border-gray-700">
                No tournaments match your filters yet.
            </div>

            <Pagination :links="tournaments.links" />
        </div>
    </AppLayout>
</template>
