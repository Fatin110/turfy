<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import { tournamentStatus, matchStatus, sportEmoji } from '@/statusMeta';

const props = defineProps({
    tournament: Object,
    canManage: Boolean,
    alreadyRegisteredTeamIds: Array,
    myEligibleTeams: Array,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const canRegister = computed(() =>
    user.value &&
    ['registration_open', 'upcoming'].includes(props.tournament.status) &&
    props.myEligibleTeams.length > 0
);

const matchesByRound = computed(() => {
    const groups = {};
    for (const m of props.tournament.matches ?? []) {
        const key = m.round || 'Matches';
        (groups[key] ??= []).push(m);
    }
    return groups;
});
</script>

<template>
    <Head :title="tournament.name" />

    <AppLayout>
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 text-white">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <Badge :variant="tournamentStatus(tournament.status).variant">{{ sportEmoji(tournament.sport) }} {{ tournamentStatus(tournament.status).label }}</Badge>
                        <h1 class="mt-2 text-3xl font-bold">{{ tournament.name }}</h1>
                        <p class="mt-1 text-gray-300">
                            <Link v-if="tournament.venue" :href="route('venues.show', tournament.venue.slug)" class="hover:underline">{{ tournament.venue.name }}</Link>
                            <span v-else>{{ tournament.city }}</span>
                            &middot; {{ tournament.start_date }}
                        </p>
                        <p class="mt-2 text-sm text-gray-400">Format: {{ tournament.format === 'knockout' ? 'Knockout' : 'Round Robin' }} &middot; {{ tournament.registrations.length }}/{{ tournament.max_teams }} teams &middot; {{ Number(tournament.entry_fee) > 0 ? `৳${tournament.entry_fee} entry` : 'Free entry' }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <Link v-if="canRegister" :href="route('registrations.create', tournament.slug)" class="rounded-md bg-emerald-500 px-5 py-2.5 text-sm font-semibold hover:bg-emerald-400">
                            Register a Team
                        </Link>
                        <Link v-if="canManage" :href="route('tournaments.edit', tournament.slug)" class="rounded-md border border-white/30 px-4 py-2 text-sm font-medium hover:bg-white/10">
                            Edit Tournament
                        </Link>
                        <Link v-if="canManage" :href="route('tournaments.registrations', tournament.slug)" class="rounded-md border border-white/30 px-4 py-2 text-sm font-medium hover:bg-white/10">
                            Manage Registrations
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-3 lg:px-8">
            <div class="space-y-8 lg:col-span-2">
                <section v-if="tournament.description || tournament.rules">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">About</h2>
                    <p v-if="tournament.description" class="mt-2 text-gray-600 dark:text-gray-300">{{ tournament.description }}</p>
                    <p v-if="tournament.rules" class="mt-2 whitespace-pre-line text-sm text-gray-500 dark:text-gray-400"><strong>Rules:</strong> {{ tournament.rules }}</p>
                    <p v-if="tournament.prize_info" class="mt-2 text-sm font-medium text-emerald-600">🏆 {{ tournament.prize_info }}</p>
                </section>

                <section v-if="Object.keys(matchesByRound).length">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Fixtures & Results</h2>
                    <div v-for="(matches, round) in matchesByRound" :key="round" class="mt-4">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-400">{{ round }}</h3>
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

                <section>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Registered Teams</h2>
                    <div v-if="tournament.registrations.length" class="mt-3 grid gap-3 sm:grid-cols-2">
                        <Link
                            v-for="r in tournament.registrations"
                            :key="r.id"
                            :href="route('teams.show', r.team.slug)"
                            class="rounded-lg border border-gray-100 bg-white p-3 text-sm font-medium text-gray-900 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        >
                            {{ r.team.name }}
                        </Link>
                    </div>
                    <p v-else class="mt-2 text-sm text-gray-500 dark:text-gray-400">No teams registered yet — be the first!</p>
                </section>
            </div>

            <aside class="space-y-4">
                <div class="rounded-lg border border-gray-100 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Organized by</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ tournament.creator.name }}</p>
                </div>
            </aside>
        </div>
    </AppLayout>
</template>
