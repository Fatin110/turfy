<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import Pagination from '@/Components/Pagination.vue';
import { venueStatus } from '@/statusMeta';

const props = defineProps({
    venues: Object,
    filters: Object,
});

const form = reactive({ status: props.filters.status ?? '' });

function applyFilters() {
    router.get(route('admin.venues.index'), form, { preserveState: true, replace: true });
}

function approve(venue) {
    router.patch(route('admin.venues.approve', venue.id), {}, { preserveScroll: true });
}

function reject(venue) {
    if (confirm(`Reject ${venue.name}?`)) {
        router.patch(route('admin.venues.reject', venue.id), {}, { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Admin — Venues" />

    <AppLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Venue Approvals</h1>
        </template>

        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex gap-3 rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                <select v-model="form.status" @change="applyFilters" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr class="text-xs font-semibold uppercase text-gray-500">
                            <th class="px-4 py-3 text-left">Venue</th>
                            <th class="px-4 py-3 text-left">Owner</th>
                            <th class="px-4 py-3 text-left">City</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="v in venues.data" :key="v.id">
                            <td class="px-4 py-3 text-sm font-medium">
                                <Link :href="route('venues.show', v.slug)" class="text-gray-900 hover:text-emerald-600 dark:text-white">{{ v.name }}</Link>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ v.owner.name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ v.city }}</td>
                            <td class="px-4 py-3"><Badge :variant="venueStatus(v.status).variant">{{ venueStatus(v.status).label }}</Badge></td>
                            <td class="px-4 py-3 text-right">
                                <div v-if="v.status === 'pending'" class="flex justify-end gap-2">
                                    <button @click="approve(v)" class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700">Approve</button>
                                    <button @click="reject(v)" class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200">Reject</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!venues.data.length">
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No venues found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="venues.links" />
        </div>
    </AppLayout>
</template>
