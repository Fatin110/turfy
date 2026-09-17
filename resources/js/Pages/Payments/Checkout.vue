<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    payment: Object,
});

const form = useForm({});

function pay() {
    form.post(route('payments.pay', props.payment.id));
}
</script>

<template>
    <Head title="bKash Checkout" />

    <AppLayout>
        <div class="mx-auto max-w-md px-4 py-16 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800">
                <div class="bg-[#E2136E] px-6 py-5 text-center text-white">
                    <p class="text-lg font-bold tracking-wide">bKash</p>
                    <p class="text-xs opacity-90">Tokenized Checkout (Sandbox)</p>
                </div>

                <div class="space-y-4 p-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Tournament</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ payment.registration.tournament.name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Team</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ payment.registration.team.name }}</span>
                    </div>
                    <div class="flex justify-between border-t border-dashed border-gray-200 pt-4 text-lg dark:border-gray-700">
                        <span class="font-medium text-gray-700 dark:text-gray-200">Amount</span>
                        <span class="font-bold text-gray-900 dark:text-white">৳{{ payment.amount }}</span>
                    </div>

                    <button
                        @click="pay"
                        :disabled="form.processing"
                        class="mt-4 w-full rounded-lg bg-[#E2136E] px-4 py-3 font-semibold text-white transition hover:bg-[#c1105e] disabled:opacity-50"
                    >
                        {{ form.processing ? 'Processing...' : 'Pay with bKash' }}
                    </button>
                    <p class="text-center text-xs text-gray-400">
                        This is a sandbox simulation of the bKash Payment Gateway — no real money moves.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
