<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import { leagueStatus, matchStatus, sportEmoji } from '@/statusMeta';

const props = defineProps({
    league: Object,
    standings: Array,
});

const matchesByDay = computed(() => {
    const groups = {};
    for (const m of props.league.matches ?? []) {
        (groups[m.matchday] ??= []).push(m);
    }
    return Object.entries(groups).sort((a, b) => a[0] - b[0]);
});
</script>

<template>
    <Head :title="league.name" />

    <AppLayout>
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 text-white">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <Badge :variant="leagueStatus(league.status).variant">{{ sportEmoji(league.sport) }} {{ leagueStatus(league.status).label }}</Badge>
                <h1 class="mt-2 text-3xl font-bold">{{ league.name }}</h1>
                <p class="mt-1 text-gray-300">{{ league.season }} &middot; {{ league.teams.length }} teams &middot; home &amp; away</p>
                <p v-if="league.description" class="mt-3 max-w-2xl text-sm text-gray-400">{{ league.description }}</p>
            </div>
        </div>

        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <section>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Table</h2>
                <div class="mt-3 overflow-x-auto rounded-xl border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/40">
                            <tr class="text-xs font-semibold uppercase text-gray-500">
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Team</th>
                                <th class="px-3 py-3 text-center">P</th>
                                <th class="px-3 py-3 text-center">W</th>
                                <th class="px-3 py-3 text-center">D</th>
                                <th class="px-3 py-3 text-center">L</th>
                                <th class="px-3 py-3 text-center">GD</th>
                                <th class="px-3 py-3 text-center">Pts</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="(row, i) in standings" :key="row.team.id">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ i + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-medium">
                                    <Link :href="route('teams.show', row.team.slug)" class="text-gray-900 hover:text-emerald-600 dark:text-white">{{ row.team.name }}</Link>
                                </td>
                                <td class="px-3 py-3 text-center text-sm text-gray-600 dark:text-gray-300">{{ row.played }}</td>
                                <td class="px-3 py-3 text-center text-sm text-gray-600 dark:text-gray-300">{{ row.won }}</td>
                                <td class="px-3 py-3 text-center text-sm text-gray-600 dark:text-gray-300">{{ row.drawn }}</td>
                                <td class="px-3 py-3 text-center text-sm text-gray-600 dark:text-gray-300">{{ row.lost }}</td>
                                <td class="px-3 py-3 text-center text-sm text-gray-600 dark:text-gray-300">{{ row.goal_difference }}</td>
                                <td class="px-3 py-3 text-center text-sm font-bold text-gray-900 dark:text-white">{{ row.points }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="mt-10">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Fixtures & Results</h2>
                <div v-for="[day, matches] in matchesByDay" :key="day" class="mt-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-400">Matchday {{ day }}</h3>
                    <div class="mt-2 space-y-2">
                        <div v-for="m in matches" :key="m.id" class="flex items-center justify-between rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex flex-1 items-center justify-between gap-3">
                                <span class="font-medium text-gray-900 dark:text-white">{{ m.home_team.name }}</span>
                                <span class="text-sm font-bold text-gray-500">
                                    <template v-if="m.home_score !== null">{{ m.home_score }} - {{ m.away_score }}</template>
                                    <template v-else>vs</template>
                                </span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ m.away_team.name }}</span>
                            </div>
                            <Badge :variant="matchStatus(m.status).variant" class="ml-4">{{ matchStatus(m.status).label }}</Badge>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
