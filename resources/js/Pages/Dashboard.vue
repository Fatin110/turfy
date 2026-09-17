<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import { tournamentStatus, registrationStatus, matchStatus, venueStatus, sportEmoji } from '@/statusMeta';

const props = defineProps({
    role: String,
    // player
    myTeams: { type: Array, default: () => [] },
    myRegistrations: { type: Array, default: () => [] },
    upcomingMatches: { type: Array, default: () => [] },
    // venue_owner
    myVenues: { type: Array, default: () => [] },
    myTournaments: { type: Array, default: () => [] },
    revenueCollected: { type: [Number, String], default: 0 },
    matchesNeedingResults: { type: Array, default: () => [] },
    // admin
    stats: { type: Object, default: null },
    pendingVenuesList: { type: Array, default: () => [] },
    recentPayments: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
        </template>

        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- PLAYER -->
            <div v-if="role === 'player'" class="space-y-8">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">My Teams</h2>
                    <Link :href="route('teams.create')" class="text-sm font-medium text-emerald-600 hover:underline">+ Create a team</Link>
                </div>
                <div v-if="myTeams.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Link v-for="t in myTeams" :key="t.id" :href="route('teams.show', t.slug)" class="rounded-lg border border-gray-100 bg-white p-4 hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                        <p class="font-medium text-gray-900 dark:text-white">{{ t.name }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ sportEmoji(t.sport) }} {{ t.sport }} &middot; {{ t.players_count }} players</p>
                    </Link>
                </div>
                <p v-else class="text-sm text-gray-500 dark:text-gray-400">You're not on a team yet.</p>

                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">My Registrations</h2>
                    <div v-if="myRegistrations.length" class="mt-3 space-y-2">
                        <Link v-for="r in myRegistrations" :key="r.id" :href="route('tournaments.show', r.tournament.slug)" class="flex items-center justify-between rounded-lg border border-gray-100 bg-white p-4 hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ r.tournament.name }}</span>
                            <Badge :variant="registrationStatus(r.status).variant">{{ registrationStatus(r.status).label }}</Badge>
                        </Link>
                    </div>
                    <p v-else class="mt-2 text-sm text-gray-500 dark:text-gray-400">No tournament registrations yet.</p>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Upcoming Matches</h2>
                    <div v-if="upcomingMatches.length" class="mt-3 space-y-2">
                        <div v-for="m in upcomingMatches" :key="m.id" class="flex items-center justify-between rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ m.home_team.name }} vs {{ m.away_team.name }}</span>
                            <span class="text-xs text-gray-500">{{ m.venue?.name ?? '' }}</span>
                        </div>
                    </div>
                    <p v-else class="mt-2 text-sm text-gray-500 dark:text-gray-400">No upcoming matches scheduled.</p>
                </div>
            </div>

            <!-- VENUE OWNER -->
            <div v-else-if="role === 'venue_owner'" class="space-y-8">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs uppercase text-gray-400">Venues</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ myVenues.length }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs uppercase text-gray-400">Tournaments Hosted</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ myTournaments.length }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs uppercase text-gray-400">Revenue Collected</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">৳{{ revenueCollected }}</p>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">My Venues</h2>
                        <Link :href="route('venues.create')" class="text-sm font-medium text-emerald-600 hover:underline">+ Add venue</Link>
                    </div>
                    <div v-if="myVenues.length" class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="v in myVenues" :key="v.id" class="rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center justify-between">
                                <Link :href="route('venues.show', v.slug)" class="font-medium text-gray-900 hover:text-emerald-600 dark:text-white">{{ v.name }}</Link>
                                <Badge :variant="venueStatus(v.status).variant">{{ venueStatus(v.status).label }}</Badge>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ v.tournaments_count }} tournaments hosted</p>
                            <Link :href="route('venues.edit', v.slug)" class="mt-2 inline-block text-xs text-emerald-600 hover:underline">Edit &rarr;</Link>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">My Tournaments</h2>
                        <Link :href="route('tournaments.create')" class="text-sm font-medium text-emerald-600 hover:underline">+ Create tournament</Link>
                    </div>
                    <div v-if="myTournaments.length" class="mt-3 space-y-2">
                        <div v-for="t in myTournaments" :key="t.id" class="flex items-center justify-between rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                            <div>
                                <Link :href="route('tournaments.show', t.slug)" class="text-sm font-medium text-gray-900 hover:text-emerald-600 dark:text-white">{{ t.name }}</Link>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ t.confirmed_registrations_count }}/{{ t.max_teams }} teams</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <Badge :variant="tournamentStatus(t.status).variant">{{ tournamentStatus(t.status).label }}</Badge>
                                <Link :href="route('tournaments.registrations', t.slug)" class="text-xs text-emerald-600 hover:underline">Registrations</Link>
                                <Link :href="route('tournaments.edit', t.slug)" class="text-xs text-emerald-600 hover:underline">Edit</Link>
                            </div>
                        </div>
                    </div>
                    <p v-else class="mt-2 text-sm text-gray-500 dark:text-gray-400">You haven't created any tournaments yet.</p>
                </div>

                <div v-if="matchesNeedingResults.length">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Matches Needing Results</h2>
                    <div class="mt-3 space-y-2">
                        <div v-for="m in matchesNeedingResults" :key="m.id" class="flex items-center justify-between rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                            <span class="text-sm text-gray-900 dark:text-white">{{ m.home_team.name }} vs {{ m.away_team.name }}</span>
                            <Link :href="route('tournaments.show', m.tournament.slug)" class="text-xs text-emerald-600 hover:underline">Enter result &rarr;</Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ADMIN -->
            <div v-else-if="role === 'admin'" class="space-y-8">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <div class="rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs uppercase text-gray-400">Users</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ stats.users }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs uppercase text-gray-400">Venues</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ stats.venues }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs uppercase text-gray-400">Pending Venues</p>
                        <p class="mt-1 text-2xl font-bold text-amber-600">{{ stats.pendingVenues }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs uppercase text-gray-400">Leagues</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ stats.leagues }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p class="text-xs uppercase text-gray-400">Revenue</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">৳{{ stats.revenue }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <Link :href="route('admin.venues.index')" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 dark:bg-emerald-600 dark:hover:bg-emerald-700">Manage Venues</Link>
                    <Link :href="route('admin.leagues.create')" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">+ Create League</Link>
                    <Link :href="route('admin.payments.index')" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">View Payments</Link>
                    <Link :href="route('admin.users.index')" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Manage Users</Link>
                </div>

                <div v-if="pendingVenuesList.length">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pending Venue Approvals</h2>
                    <div class="mt-3 space-y-2">
                        <div v-for="v in pendingVenuesList" :key="v.id" class="flex items-center justify-between rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                            <span class="text-sm text-gray-900 dark:text-white">{{ v.name }} &middot; {{ v.city }}</span>
                            <Link :href="route('admin.venues.index')" class="text-xs text-emerald-600 hover:underline">Review &rarr;</Link>
                        </div>
                    </div>
                </div>

                <div v-if="recentPayments.length">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Payments</h2>
                    <div class="mt-3 space-y-2">
                        <div v-for="p in recentPayments" :key="p.id" class="flex items-center justify-between rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                            <span class="text-sm text-gray-900 dark:text-white">{{ p.user.name }} &middot; {{ p.registration?.tournament?.name }}</span>
                            <span class="text-sm font-semibold text-emerald-600">৳{{ p.amount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
