<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import InputError from '@/Components/InputError.vue';
import { matchStatus, sportEmoji } from '@/statusMeta';

const props = defineProps({
    team: Object,
    canManage: Boolean,
});

const addForm = useForm({ email: '', jersey_number: '' });

function addPlayer() {
    addForm.post(route('teams.players.add', props.team.slug), {
        preserveScroll: true,
        onSuccess: () => addForm.reset(),
    });
}

function removePlayer(userId) {
    if (confirm('Remove this player from the roster?')) {
        router.delete(route('teams.players.remove', [props.team.slug, userId]), { preserveScroll: true });
    }
}

const allMatches = computed(() => {
    const home = (props.team.home_matches ?? []).map(m => ({ ...m, opponent: m.away_team, isHome: true }));
    const away = (props.team.away_matches ?? []).map(m => ({ ...m, opponent: m.home_team, isHome: false }));
    return [...home, ...away].sort((a, b) => new Date(b.scheduled_at ?? 0) - new Date(a.scheduled_at ?? 0));
});
</script>

<template>
    <Head :title="team.name" />

    <AppLayout>
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 text-white">
            <div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <Badge variant="purple">{{ team.type === 'venue' ? 'Venue Team' : 'Open Team' }}</Badge>
                        <h1 class="mt-2 text-3xl font-bold">{{ team.name }}</h1>
                        <p class="mt-1 text-gray-300">{{ sportEmoji(team.sport) }} {{ team.sport }} &middot; {{ team.city }}</p>
                        <Link v-if="team.venue" :href="route('venues.show', team.venue.slug)" class="mt-1 inline-block text-sm text-emerald-300 hover:underline">
                            {{ team.venue.name }} &rarr;
                        </Link>
                    </div>
                    <Link v-if="canManage" :href="route('teams.edit', team.slug)" class="rounded-md border border-white/30 px-4 py-2 text-sm font-medium hover:bg-white/10">
                        Edit Team
                    </Link>
                </div>
            </div>
        </div>

        <div class="mx-auto grid max-w-5xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-2 lg:px-8">
            <section>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Roster</h2>
                <ul class="mt-3 divide-y divide-gray-100 rounded-lg border border-gray-100 bg-white dark:divide-gray-700 dark:border-gray-700 dark:bg-gray-800">
                    <li v-for="p in team.players" :key="p.id" class="flex items-center justify-between px-4 py-3">
                        <span class="text-sm text-gray-900 dark:text-white">
                            {{ p.name }}
                            <span v-if="p.pivot?.role === 'captain'" class="ml-1 text-xs text-emerald-600">(C)</span>
                            <span v-if="p.pivot?.jersey_number" class="ml-1 text-xs text-gray-400">#{{ p.pivot.jersey_number }}</span>
                        </span>
                        <button v-if="canManage" @click="removePlayer(p.id)" class="text-xs text-red-500 hover:underline">Remove</button>
                    </li>
                    <li v-if="!team.players?.length" class="px-4 py-6 text-center text-sm text-gray-500">No players added yet.</li>
                </ul>

                <form v-if="canManage" @submit.prevent="addPlayer" class="mt-4 flex flex-wrap gap-2 rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                    <input v-model="addForm.email" type="email" placeholder="Player's account email" required class="min-w-[180px] flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                    <input v-model="addForm.jersey_number" type="number" placeholder="#" class="w-16 rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                    <button type="submit" :disabled="addForm.processing" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Add</button>
                    <InputError :message="addForm.errors.email" class="w-full" />
                </form>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent & Upcoming Matches</h2>
                <div class="mt-3 space-y-2">
                    <div v-for="m in allMatches" :key="m.id" class="flex items-center justify-between rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="text-sm">
                            <span class="text-gray-400">{{ m.isHome ? 'vs' : '@' }}</span>
                            <span class="ml-1 font-medium text-gray-900 dark:text-white">{{ m.opponent.name }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span v-if="m.home_score !== null" class="text-sm font-bold text-gray-500">
                                {{ m.isHome ? `${m.home_score}-${m.away_score}` : `${m.away_score}-${m.home_score}` }}
                            </span>
                            <Badge :variant="matchStatus(m.status).variant">{{ matchStatus(m.status).label }}</Badge>
                        </div>
                    </div>
                    <p v-if="!allMatches.length" class="text-sm text-gray-500 dark:text-gray-400">No matches yet.</p>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
