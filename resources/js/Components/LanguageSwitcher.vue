<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

// Flowbite (or any other JS library that needs initialization for dropdowns)
// If using Flowbite's Dropdown via data attributes, ensure Flowbite JS is initialized.
// Or use a Vue-specific dropdown component.

const page = usePage();
const currentLocale = computed(() => page.props.locale);
const availableLocales = computed(() => page.props.available_locales);

const showDropdown = ref(false);
</script>

<template>
    <div class="relative">
        <button
            @click="showDropdown = !showDropdown"
            type="button"
            class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline-none"
            aria-expanded="false"
        >
            <span>{{ availableLocales[currentLocale] }}</span>
            <svg class="w-5 h-5 ml-1 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

        <div
            v-show="showDropdown"
            @click.away="showDropdown = false"
            class="absolute right-0 z-50 w-40 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-700"
            role="menu"
            aria-orientation="vertical"
            tabindex="-1"
        >
            <Link
                v-for="(name, code) in availableLocales"
                :key="code"
                :href="route('language.switch', code)"
                preserve-scroll
                method="get"
                as="button"
                type="button"
                @click="showDropdown = false"
                class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600"
                :class="{ 'font-semibold bg-gray-100 dark:bg-gray-600': code === currentLocale }"
                role="menuitem"
                tabindex="-1"
            >
                {{ name }}
            </Link>
        </div>
    </div>
</template>
