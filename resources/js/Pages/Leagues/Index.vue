<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import Pagination from '@/Components/Pagination.vue';
import { leagueStatus, sportEmoji } from '@/statusMeta';

const props = defineProps({
    leagues: Object,
    filters: Object,
});

const form = reactive({ sport: props.filters.sport ?? '' });

function applyFilters() {
    router.get(route('leagues.index'), form, { preserveState: true, replace: true });
}
</script>

<template>
    <Head title="Leagues" />

    <AppLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Leagues</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">City-wide competitions where venues field a team, home and away.</p>
        </template>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8 flex gap-3 rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                <select v-model="form.sport" @change="applyFilters" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All sports</option>
                    <option value="cricket">🏏 Cricket</option>
                    <option value="football">⚽ Football</option>
                </select>
            </div>

            <div v-if="leagues.data.length" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="l in leagues.data"
                    :key="l.id"
                    :href="route('leagues.show', l.slug)"
                    class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
                >
                    <Badge :variant="leagueStatus(l.status).variant">{{ sportEmoji(l.sport) }} {{ leagueStatus(l.status).label }}</Badge>
                    <h3 class="mt-3 font-semibold text-gray-900 dark:text-white">{{ l.name }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ l.season }} &middot; {{ l.teams_count }} teams</p>
                </Link>
            </div>
            <div v-else class="rounded-xl border border-dashed border-gray-300 p-12 text-center text-gray-500 dark:border-gray-700">
                No leagues yet — check back soon.
            </div>

            <Pagination :links="leagues.links" />
        </div>
    </AppLayout>
</template>
