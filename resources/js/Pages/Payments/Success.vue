<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    payment: Object,
});
</script>

<template>
    <Head title="Payment Successful" />

    <AppLayout>
        <div class="mx-auto max-w-md px-4 py-16 text-center sm:px-6 lg:px-8">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-3xl dark:bg-emerald-900/40">✅</div>
            <h1 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">Payment Successful!</h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">
                {{ payment.registration.team.name }} is confirmed for {{ payment.registration.tournament.name }}.
            </p>
            <p class="mt-1 text-xs text-gray-400">Transaction ID: {{ payment.transaction_id }}</p>

            <Link :href="route('tournaments.show', payment.registration.tournament.slug)" class="mt-6 inline-block rounded-md bg-emerald-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-emerald-700">
                View Tournament
            </Link>
        </div>
    </AppLayout>
</template>
