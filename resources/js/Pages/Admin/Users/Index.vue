<script setup>
import { Head, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    users: Object,
    roles: Array,
    filters: Object,
});

const form = reactive({
    role: props.filters.role ?? '',
    search: props.filters.search ?? '',
});

function applyFilters() {
    router.get(route('admin.users.index'), form, { preserveState: true, replace: true });
}

function updateRole(user, role) {
    router.patch(route('admin.users.role', user.id), { role }, { preserveScroll: true });
}
</script>

<template>
    <Head title="Admin — Users" />

    <AppLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Users</h1>
        </template>

        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-wrap gap-3 rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
                <input v-model="form.search" @keyup.enter="applyFilters" type="text" placeholder="Search name or email..." class="min-w-[200px] flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                <select v-model="form.role" @change="applyFilters" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All roles</option>
                    <option v-for="r in roles" :key="r" :value="r">{{ r }}</option>
                </select>
                <button @click="applyFilters" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Search</button>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr class="text-xs font-semibold uppercase text-gray-500">
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">City</th>
                            <th class="px-4 py-3 text-left">Role</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="u in users.data" :key="u.id">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ u.name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ u.email }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ u.city ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <select :value="u.role" @change="updateRole(u, $event.target.value)" class="rounded-md border-gray-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    <option v-for="r in roles" :key="r" :value="r">{{ r }}</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <Pagination :links="users.links" />
        </div>
    </AppLayout>
</template>
