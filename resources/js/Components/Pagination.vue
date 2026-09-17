<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: { type: Array, required: true },
});
</script>

<template>
    <nav v-if="links.length > 3" class="mt-6 flex flex-wrap items-center justify-center gap-1">
        <template v-for="(link, i) in links" :key="i">
            <span
                v-if="link.url === null"
                class="rounded-md px-3 py-1.5 text-sm text-gray-400 dark:text-gray-600"
                v-html="link.label"
            />
            <Link
                v-else
                :href="link.url"
                preserve-scroll
                class="rounded-md px-3 py-1.5 text-sm transition"
                :class="link.active
                    ? 'bg-emerald-600 text-white'
                    : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'"
                v-html="link.label"
            />
        </template>
    </nav>
</template>
