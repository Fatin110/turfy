<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import { tournamentStatus, sportEmoji } from '@/statusMeta';

defineProps({
    liveTournaments: Array,
    upcomingTournaments: Array,
    ongoingLeagues: Array,
    featuredVenues: Array,
    stats: Object,
});
</script>

<template>
    <Head title="Turfy — Every indoor turf, one platform" />

    <AppLayout>
        <section class="bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-600 text-white">
            <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
                <div class="max-w-2xl">
                    <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl">
                        Every indoor cricket &amp; football turf. One platform.
                    </h1>
                    <p class="mt-6 text-lg text-emerald-50">
                        Discover indoor venues across the city, join live tournaments, follow home-and-away
                        leagues, and pay your entry fee with bKash — all in one place.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <Link :href="route('tournaments.index')" class="rounded-md bg-white px-5 py-3 text-sm font-semibold text-emerald-700 shadow-sm hover:bg-emerald-50">
                            Browse Tournaments
                        </Link>
                        <Link :href="route('venues.index')" class="rounded-md border border-white/40 px-5 py-3 text-sm font-semibold text-white hover:bg-white/10">
                            Find a Venue
                        </Link>
                    </div>
                </div>

                <dl v-if="stats" class="mt-14 grid grid-cols-3 gap-6 border-t border-white/20 pt-8 sm:max-w-lg">
                    <div>
                        <dt class="text-sm text-emerald-100">Venues</dt>
                        <dd class="text-3xl font-bold">{{ stats.venues }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-emerald-100">Tournaments</dt>
                        <dd class="text-3xl font-bold">{{ stats.tournaments }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-emerald-100">Leagues</dt>
                        <dd class="text-3xl font-bold">{{ stats.leagues }}</dd>
                    </div>
                </dl>
            </div>
        </section>

        <section v-if="liveTournaments?.length" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="flex items-center gap-2 text-2xl font-bold text-gray-900 dark:text-white">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span>
                    </span>
                    Live Now
                </h2>
                <Link :href="route('tournaments.index')" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">View all &rarr;</Link>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="t in liveTournaments"
                    :key="t.id"
                    :href="route('tournaments.show', t.slug)"
                    class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="flex items-start justify-between">
                        <Badge variant="red">{{ sportEmoji(t.sport) }} Live</Badge>
                        <span class="text-xs text-gray-400">{{ t.confirmed_registrations_count }} teams</span>
                    </div>
                    <h3 class="mt-3 font-semibold text-gray-900 dark:text-white">{{ t.name }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ t.venue?.name ?? t.city }}</p>
                </Link>
            </div>
        </section>

        <section v-if="upcomingTournaments?.length" class="bg-white py-14 dark:bg-gray-800/50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Open for Registration</h2>
                    <Link :href="route('tournaments.index')" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">View all &rarr;</Link>
                </div>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="t in upcomingTournaments"
                        :key="t.id"
                        :href="route('tournaments.show', t.slug)"
                        class="rounded-xl border border-gray-100 bg-gray-50 p-5 transition hover:shadow-md dark:border-gray-700 dark:bg-gray-900"
                    >
                        <div class="flex items-start justify-between">
                            <Badge :variant="tournamentStatus(t.status).variant">{{ sportEmoji(t.sport) }} {{ tournamentStatus(t.status).label }}</Badge>
                            <span class="text-xs text-gray-400">{{ Number(t.entry_fee) > 0 ? `৳${t.entry_fee}` : 'Free' }}</span>
                        </div>
                        <h3 class="mt-3 font-semibold text-gray-900 dark:text-white">{{ t.name }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ t.venue?.name ?? t.city }} &middot; {{ t.confirmed_registrations_count }}/{{ t.max_teams }} teams</p>
                    </Link>
                </div>
            </div>
        </section>

        <section v-if="ongoingLeagues?.length" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Leagues</h2>
                <Link :href="route('leagues.index')" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">View all &rarr;</Link>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <Link
                    v-for="l in ongoingLeagues"
                    :key="l.id"
                    :href="route('leagues.show', l.slug)"
                    class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
                >
                    <p class="text-xs font-medium uppercase tracking-wide text-emerald-600">{{ sportEmoji(l.sport) }} {{ l.season }}</p>
                    <h3 class="mt-2 font-semibold text-gray-900 dark:text-white">{{ l.name }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ l.teams_count }} teams &middot; home &amp; away</p>
                </Link>
            </div>
        </section>

        <section v-if="featuredVenues?.length" class="bg-white py-14 dark:bg-gray-800/50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Top-rated Venues</h2>
                    <Link :href="route('venues.index')" class="text-sm font-medium text-emerald-600 hover:text-emerald-700">View all &rarr;</Link>
                </div>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <Link
                        v-for="v in featuredVenues"
                        :key="v.id"
                        :href="route('venues.show', v.slug)"
                        class="rounded-xl border border-gray-100 bg-gray-50 p-5 transition hover:shadow-md dark:border-gray-700 dark:bg-gray-900"
                    >
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ v.name }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ v.city }}</p>
                        <div class="mt-3 flex items-center gap-1 text-sm text-amber-500">
                            <span>★ {{ Number(v.rating_avg).toFixed(1) }}</span>
                            <span class="text-gray-400">({{ v.rating_count }})</span>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-gray-900 px-8 py-12 text-center text-white sm:px-16">
                <h2 class="text-2xl font-bold">Run an indoor turf?</h2>
                <p class="mx-auto mt-3 max-w-xl text-gray-300">
                    List your venue, host tournaments, take bKash payments on registration, and get your team
                    into our city-wide leagues.
                </p>
                <Link :href="route('register')" class="mt-6 inline-block rounded-md bg-emerald-500 px-6 py-3 text-sm font-semibold hover:bg-emerald-400">
                    Get Started
                </Link>
            </div>
        </section>
    </AppLayout>
</template>
