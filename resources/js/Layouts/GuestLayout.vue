<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue'; // 1. Import the component
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex flex-col items-center min-h-screen pt-6 bg-gray-100 sm:justify-center sm:pt-0"
    >
    <div class="absolute top-0 right-0 mt-4 me-4 sm:mt-6 sm:me-6">
            <LanguageSwitcher />
        </div>
        <div>
            <Link href="/">
               <!--  ///* <ApplicationLogo class="w-20 h-20 text-gray-500 fill-current" />  */// --->
                <img src="/images/EDUEVA_main_logo.png" alt="Application Logo" class="w-20 h-20" />
            </Link>
        </div>

        <div
            class="w-full px-6 py-4 mt-6 overflow-hidden bg-white shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
