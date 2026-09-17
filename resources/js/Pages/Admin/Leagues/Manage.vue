<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import { leagueStatus, matchStatus, sportEmoji } from '@/statusMeta';

const props = defineProps({
    league: Object,
    standings: Array,
    availableTeams: Array,
});

const addTeamForm = useForm({ team_id: props.availableTeams[0]?.id ?? '' });

function addTeam() {
    addTeamForm.post(route('admin.leagues.teams.add', props.league.slug), { preserveScroll: true });
}

function removeTeam(teamId) {
    if (confirm('Remove this team from the league? Existing fixtures will need to be regenerated.')) {
        router.delete(route('admin.leagues.teams.remove', [props.league.slug, teamId]), { preserveScroll: true });
    }
}

function generateFixtures() {
    if (confirm('Generate the full home & away schedule? This replaces any existing fixtures.')) {
        router.post(route('admin.leagues.fixtures.generate', props.league.slug), {}, { preserveScroll: true });
    }
}

function updateStatus(e) {
    router.patch(route('admin.leagues.status', props.league.slug), { status: e.target.value }, { preserveScroll: true });
}

const matchesByDay = computed(() => {
    const groups = {};
    for (const m of props.league.matches ?? []) {
        (groups[m.matchday] ??= []).push(m);
    }
    return Object.entries(groups).sort((a, b) => a[0] - b[0]);
});

const scoreForms = reactive({});
function scoreForm(match) {
    return scoreForms[match.id] ??= { home_score: match.home_score ?? 0, away_score: match.away_score ?? 0 };
}
function saveResult(match) {
    const data = scoreForm(match);
    router.patch(route('matches.update', match.id), { ...data, status: 'completed' }, { preserveScroll: true });
}
</script>

<template>
    <Head :title="`Manage — ${league.name}`" />

    <AppLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ league.name }}</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ sportEmoji(league.sport) }} {{ league.season }}</p>
                </div>
                <select :value="league.status" @change="updateStatus" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="draft">Draft</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
        </template>

        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-1">
                    <div class="rounded-xl border border-gray-100 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                        <h2 class="font-semibold text-gray-900 dark:text-white">Teams ({{ league.teams.length }})</h2>
                        <ul class="mt-3 space-y-2">
                            <li v-for="t in league.teams" :key="t.id" class="flex items-center justify-between text-sm">
                                <span class="text-gray-700 dark:text-gray-300">{{ t.name }}</span>
                                <button @click="removeTeam(t.id)" class="text-xs text-red-500 hover:underline">Remove</button>
                            </li>
                        </ul>

                        <form v-if="availableTeams.length" @submit.prevent="addTeam" class="mt-4 flex gap-2">
                            <select v-model="addTeamForm.team_id" class="flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option v-for="t in availableTeams" :key="t.id" :value="t.id">{{ t.name }}</option>
                            </select>
                            <button type="submit" class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700">Add</button>
                        </form>
                        <p v-else class="mt-3 text-xs text-gray-400">All eligible venue teams for this sport are already in the league.</p>

                        <button
                            @click="generateFixtures"
                            :disabled="league.teams.length < 2"
                            class="mt-5 w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 disabled:opacity-40 dark:bg-emerald-600 dark:hover:bg-emerald-700"
                        >
                            Generate Fixtures
                        </button>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                        <h2 class="font-semibold text-gray-900 dark:text-white">Standings</h2>
                        <table class="mt-3 w-full text-sm">
                            <thead>
                                <tr class="text-xs uppercase text-gray-400">
                                    <th class="pb-2 text-left">Team</th>
                                    <th class="pb-2 text-center">P</th>
                                    <th class="pb-2 text-center">Pts</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="row in standings" :key="row.team.id">
                                    <td class="py-1.5 text-gray-700 dark:text-gray-300">{{ row.team.name }}</td>
                                    <td class="py-1.5 text-center text-gray-500">{{ row.played }}</td>
                                    <td class="py-1.5 text-center font-bold text-gray-900 dark:text-white">{{ row.points }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <h2 class="font-semibold text-gray-900 dark:text-white">Fixtures &amp; Result Entry</h2>
                    <div v-for="[day, matches] in matchesByDay" :key="day" class="mt-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-400">Matchday {{ day }}</h3>
                        <div class="mt-2 space-y-2">
                            <div v-for="m in matches" :key="m.id" class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                                <div class="flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-white">
                                    <span>{{ m.home_team.name }}</span>
                                    <input type="number" min="0" v-model.number="scoreForm(m).home_score" class="w-14 rounded-md border-gray-300 text-center text-sm dark:border-gray-600 dark:bg-gray-700" />
                                    <span class="text-gray-400">-</span>
                                    <input type="number" min="0" v-model.number="scoreForm(m).away_score" class="w-14 rounded-md border-gray-300 text-center text-sm dark:border-gray-600 dark:bg-gray-700" />
                                    <span>{{ m.away_team.name }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Badge :variant="matchStatus(m.status).variant">{{ matchStatus(m.status).label }}</Badge>
                                    <button @click="saveResult(m)" class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p v-if="!matchesByDay.length" class="mt-3 text-sm text-gray-500 dark:text-gray-400">No fixtures generated yet.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
