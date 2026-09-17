<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import { tournamentStatus, venueStatus, sportEmoji } from '@/statusMeta';

const props = defineProps({
    venue: Object,
    canReview: Boolean,
    userReview: Object,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const canManage = computed(() => user.value && (user.value.role === 'admin' || user.value.id === props.venue.owner_id));
const primaryTeam = computed(() => props.venue.team?.[0] ?? null);

const reviewForm = useForm({
    rating: props.userReview?.rating ?? 5,
    comment: props.userReview?.comment ?? '',
});

function submitReview() {
    reviewForm.post(route('venues.reviews.store', props.venue.slug), { preserveScroll: true });
}
</script>

<template>
    <Head :title="venue.name" />

    <AppLayout>
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 text-white">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <Badge v-if="venue.status !== 'approved'" :variant="venueStatus(venue.status).variant">{{ venueStatus(venue.status).label }}</Badge>
                        <h1 class="mt-2 text-3xl font-bold">{{ venue.name }}</h1>
                        <p class="mt-1 text-gray-300">{{ venue.address }}, {{ venue.city }}</p>
                        <div class="mt-3 flex items-center gap-3">
                            <span class="flex items-center gap-1 text-amber-400">★ {{ Number(venue.rating_avg).toFixed(1) }}</span>
                            <span class="text-gray-400 text-sm">({{ venue.rating_count }} reviews)</span>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <span v-for="s in venue.sports" :key="s" class="rounded-full bg-white/10 px-2.5 py-0.5 text-xs font-medium">
                                {{ sportEmoji(s) }} {{ s }}
                            </span>
                        </div>
                    </div>
                    <Link v-if="canManage" :href="route('venues.edit', venue.slug)" class="rounded-md border border-white/30 px-4 py-2 text-sm font-medium hover:bg-white/10">
                        Edit Venue
                    </Link>
                </div>
            </div>
        </div>

        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-3 lg:px-8">
            <div class="space-y-8 lg:col-span-2">
                <section v-if="venue.description">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">About</h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">{{ venue.description }}</p>
                    <p v-if="venue.phone" class="mt-2 text-sm text-gray-500 dark:text-gray-400">📞 {{ venue.phone }}</p>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tournaments Hosted Here</h2>
                    <div v-if="venue.tournaments?.length" class="mt-3 space-y-3">
                        <Link
                            v-for="t in venue.tournaments"
                            :key="t.id"
                            :href="route('tournaments.show', t.slug)"
                            class="flex items-center justify-between rounded-lg border border-gray-100 bg-white p-4 shadow-sm hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
                        >
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ t.name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ t.start_date }}</p>
                            </div>
                            <Badge :variant="tournamentStatus(t.status).variant">{{ tournamentStatus(t.status).label }}</Badge>
                        </Link>
                    </div>
                    <p v-else class="mt-2 text-sm text-gray-500 dark:text-gray-400">No tournaments hosted yet.</p>
                </section>

                <section>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Reviews</h2>
                    <div v-if="venue.reviews?.length" class="mt-3 space-y-4">
                        <div v-for="r in venue.reviews" :key="r.id" class="rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-gray-900 dark:text-white">{{ r.user.name }}</span>
                                <span class="text-amber-500">{{ '★'.repeat(r.rating) }}{{ '☆'.repeat(5 - r.rating) }}</span>
                            </div>
                            <p v-if="r.comment" class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ r.comment }}</p>
                        </div>
                    </div>
                    <p v-else class="mt-2 text-sm text-gray-500 dark:text-gray-400">No reviews yet — be the first!</p>

                    <form v-if="canReview" @submit.prevent="submitReview" class="mt-6 rounded-lg border border-gray-100 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p class="font-medium text-gray-900 dark:text-white">{{ userReview ? 'Update your review' : 'Leave a review' }}</p>
                        <div class="mt-2 flex gap-2">
                            <button
                                v-for="n in 5"
                                :key="n"
                                type="button"
                                @click="reviewForm.rating = n"
                                class="text-2xl"
                                :class="n <= reviewForm.rating ? 'text-amber-500' : 'text-gray-300 dark:text-gray-600'"
                            >★</button>
                        </div>
                        <textarea
                            v-model="reviewForm.comment"
                            rows="2"
                            placeholder="Share your experience..."
                            class="mt-3 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        />
                        <button type="submit" :disabled="reviewForm.processing" class="mt-3 rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50">
                            Submit Review
                        </button>
                    </form>
                </section>
            </div>

            <aside class="space-y-6">
                <div v-if="primaryTeam" class="rounded-lg border border-gray-100 bg-white p-5 dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Venue Team</h3>
                    <Link :href="route('teams.show', primaryTeam.slug)" class="mt-2 block text-emerald-600 hover:underline">
                        {{ primaryTeam.name }} &rarr;
                    </Link>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Represents {{ venue.name }} in inter-venue leagues & tournaments.</p>
                </div>
            </aside>
        </div>
    </AppLayout>
</template>
