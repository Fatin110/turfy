<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

const showingNavigationDropdown = ref(false);

const navLinks = [
    { name: 'Venues', route: 'venues.index' },
    { name: 'Tournaments', route: 'tournaments.index' },
    { name: 'Leagues', route: 'leagues.index' },
];
</script>

<template>
    <div class="flex min-h-screen flex-col bg-gray-50 dark:bg-gray-900">
        <nav class="border-b border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <div class="flex shrink-0 items-center gap-2">
                            <Link :href="route('home')" class="flex items-center gap-2">
                                <ApplicationLogo class="block h-8 w-auto fill-current text-emerald-600" />
                                <span class="text-lg font-bold tracking-tight text-gray-900 dark:text-white">Turfy</span>
                            </Link>
                        </div>

                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <NavLink
                                v-for="link in navLinks"
                                :key="link.route"
                                :href="route(link.route)"
                                :active="route().current(link.route + '*')"
                            >
                                {{ link.name }}
                            </NavLink>
                        </div>
                    </div>

                    <div class="hidden sm:ms-6 sm:flex sm:items-center sm:gap-3">
                        <template v-if="user">
                            <Link
                                :href="route('dashboard')"
                                class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                            >
                                Dashboard
                            </Link>

                            <div class="relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
                                            >
                                                {{ user.name }}
                                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>
                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                        <DropdownLink v-if="user.role === 'admin'" :href="route('admin.venues.index')">Admin Panel</DropdownLink>
                                        <DropdownLink v-if="user.role === 'venue_owner'" :href="route('venues.create')">List a Venue</DropdownLink>
                                        <DropdownLink v-if="user.role === 'player'" :href="route('teams.create')">Create a Team</DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                                Log in
                            </Link>
                            <Link :href="route('register')" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700">
                                Sign up
                            </Link>
                        </template>
                    </div>

                    <div class="-me-2 flex items-center sm:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none dark:text-gray-500 dark:hover:bg-gray-900 dark:hover:text-gray-400"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                <div class="space-y-1 pb-3 pt-2">
                    <ResponsiveNavLink v-for="link in navLinks" :key="link.route" :href="route(link.route)" :active="route().current(link.route + '*')">
                        {{ link.name }}
                    </ResponsiveNavLink>
                </div>

                <div class="border-t border-gray-200 pb-1 pt-4 dark:border-gray-600">
                    <template v-if="user">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800 dark:text-gray-200">{{ user.name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ user.email }}</div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('dashboard')">Dashboard</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('profile.edit')">Profile</ResponsiveNavLink>
                            <ResponsiveNavLink v-if="user.role === 'admin'" :href="route('admin.venues.index')">Admin Panel</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">Log Out</ResponsiveNavLink>
                        </div>
                    </template>
                    <template v-else>
                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('login')">Log in</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('register')">Sign up</ResponsiveNavLink>
                        </div>
                    </template>
                </div>
            </div>
        </nav>

        <div v-if="flashSuccess || flashError" class="mx-auto w-full max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
            <div v-if="flashSuccess" class="rounded-md bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                {{ flashSuccess }}
            </div>
            <div v-if="flashError" class="rounded-md bg-red-50 px-4 py-3 text-sm font-medium text-red-800 dark:bg-red-900/40 dark:text-red-300">
                {{ flashError }}
            </div>
        </div>

        <header v-if="$slots.header" class="bg-white shadow-sm dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="border-t border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 py-8 text-sm text-gray-500 sm:px-6 lg:px-8 dark:text-gray-400">
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <p>&copy; {{ new Date().getFullYear() }} Turfy — every indoor turf, one platform.</p>
                    <div class="flex gap-6">
                        <Link :href="route('venues.index')" class="hover:text-emerald-600">Venues</Link>
                        <Link :href="route('tournaments.index')" class="hover:text-emerald-600">Tournaments</Link>
                        <Link :href="route('leagues.index')" class="hover:text-emerald-600">Leagues</Link>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
