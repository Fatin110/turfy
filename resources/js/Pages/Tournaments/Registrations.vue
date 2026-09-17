<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import { registrationStatus, paymentStatus } from '@/statusMeta';

defineProps({
    tournament: Object,
});
</script>

<template>
    <Head :title="`Registrations — ${tournament.name}`" />

    <AppLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Registrations</h1>
            <Link :href="route('tournaments.show', tournament.slug)" class="text-sm text-emerald-600 hover:underline">{{ tournament.name }}</Link>
        </template>

        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Team</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Registered By</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Payment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="r in tournament.registrations" :key="r.id">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ r.team.name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ r.registered_by?.name }}</td>
                            <td class="px-4 py-3"><Badge :variant="registrationStatus(r.status).variant">{{ registrationStatus(r.status).label }}</Badge></td>
                            <td class="px-4 py-3">
                                <Badge v-if="r.payment" :variant="paymentStatus(r.payment.status).variant">
                                    {{ r.payment.method === 'free' ? 'Free' : `৳${r.payment.amount}` }} &middot; {{ paymentStatus(r.payment.status).label }}
                                </Badge>
                                <span v-else class="text-xs text-gray-400">—</span>
                            </td>
                        </tr>
                        <tr v-if="!tournament.registrations.length">
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">No registrations yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
