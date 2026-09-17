<script setup>
import { Head, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import Pagination from '@/Components/Pagination.vue';
import { paymentStatus } from '@/statusMeta';

const props = defineProps({
    payments: Object,
    filters: Object,
});

const form = reactive({ status: props.filters.status ?? '' });

function applyFilters() {
    router.get(route('admin.payments.index'), form, { preserveState: true, replace: true });
}
</script>

<template>
    <Head title="Admin — Payments" />

    <AppLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Payments</h1>
        </template>

        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex gap-3 rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                <select v-model="form.status" @change="applyFilters" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All statuses</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="failed">Failed</option>
                    <option value="refunded">Refunded</option>
                </select>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr class="text-xs font-semibold uppercase text-gray-500">
                            <th class="px-4 py-3 text-left">User</th>
                            <th class="px-4 py-3 text-left">Tournament</th>
                            <th class="px-4 py-3 text-left">Team</th>
                            <th class="px-4 py-3 text-left">Method</th>
                            <th class="px-4 py-3 text-left">Amount</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Transaction ID</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="p in payments.data" :key="p.id">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ p.user.name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ p.registration?.tournament?.name ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ p.registration?.team?.name ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm capitalize text-gray-600 dark:text-gray-300">{{ p.method }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">৳{{ p.amount }}</td>
                            <td class="px-4 py-3"><Badge :variant="paymentStatus(p.status).variant">{{ paymentStatus(p.status).label }}</Badge></td>
                            <td class="px-4 py-3 text-xs text-gray-400">{{ p.transaction_id ?? '—' }}</td>
                        </tr>
                        <tr v-if="!payments.data.length">
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">No payments found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="payments.links" />
        </div>
    </AppLayout>
</template>
