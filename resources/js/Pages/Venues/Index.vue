<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    venues: Object,
    cities: Array,
    sports: Array,
    filters: Object,
});

const form = reactive({
    search: props.filters.search ?? '',
    city: props.filters.city ?? '',
    sport: props.filters.sport ?? '',
});

function applyFilters() {
    router.get(route('venues.index'), form, { preserveState: true, replace: true });
}
</script>

<template>
    <Head title="Venues" />

    <AppLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Indoor Venues</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Every approved indoor cricket & football turf in the city.</p>
        </template>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-wrap gap-3 rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                <input
                    v-model="form.search"
                    @keyup.enter="applyFilters"
                    type="text"
                    placeholder="Search venues..."
                    class="min-w-[200px] flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                />
                <select v-model="form.city" @change="applyFilters" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All cities</option>
                    <option v-for="c in cities" :key="c" :value="c">{{ c }}</option>
                </select>
                <select v-model="form.sport" @change="applyFilters" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All sports</option>
                    <option v-for="s in sports" :key="s" :value="s">{{ s === 'cricket' ? '🏏 Cricket' : '⚽ Football' }}</option>
                </select>
                <button @click="applyFilters" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Search</button>
            </div>

            <div v-if="venues.data.length" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Link
                    v-for="v in venues.data"
                    :key="v.id"
                    :href="route('venues.show', v.slug)"
                    class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="flex items-start justify-between">
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ v.name }}</h3>
                        <div class="flex items-center gap-1 text-sm text-amber-500">
                            <span>★ {{ Number(v.rating_avg).toFixed(1) }}</span>
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ v.city }}</p>
                    <div class="mt-3 flex gap-2">
                        <span v-for="s in v.sports" :key="s" class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                            {{ s === 'cricket' ? '🏏 Cricket' : '⚽ Football' }}
                        </span>
                    </div>
                </Link>
            </div>
            <div v-else class="rounded-xl border border-dashed border-gray-300 p-12 text-center text-gray-500 dark:border-gray-700">
                No venues match your filters yet.
            </div>

            <Pagination :links="venues.links" />
        </div>
    </AppLayout>
</template>
