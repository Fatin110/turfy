<script setup>
const props = defineProps({
    variant: { type: String, default: 'gray' }, // gray | green | blue | amber | red | purple
});

const variants = {
    gray: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200',
    green: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
    blue: 'bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300',
    amber: 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
    red: 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300',
    purple: 'bg-violet-100 text-violet-700 dark:bg-violet-900/50 dark:text-violet-300',
};
</script>

<template>
    <span
        class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium"
        :class="variants[props.variant] ?? variants.gray"
    >
        <slot />
    </span>
</template>
