<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3'; // Added Link import
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
<h2
    class="px-4 py-2 text-xl font-semibold leading-tight text-gray-800 bg-gray-200 rounded-lg shadow-sm dark:text-gray-100 dark:bg-gray-700"
    >
    {{ $t('dashboard') }}
</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-gray-200 shadow-sm dark:bg-gray-800 sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        {{ $t("You're logged in!") }}
                        <div class="flex flex-wrap gap-4 mt-4">
                            <Link
                                :href="route('teachers.bulk-edit.form')"
                                class="px-4 py-2 text-white bg-green-600 rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                            >
                                {{ $t('TeachersBulkEdit') }}
                            </Link>

                            <Link
                                :href="route('users.import.create')"
                                class="px-4 py-2 text-white rounded-md shadow-sm bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                            >
                                {{ $t('Import CSV Users') }}
                            </Link>

                            <a
                                :href="route('users.export.excel')"
                                class="px-4 py-2 text-white bg-teal-600 rounded-md shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                            >
                                {{ $t('Export Excel Users') }}
                            </a>

                            <a
                                :href="route('users.export.word')"
                                class="px-4 py-2 text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                            >
                                {{ $t('Export Ms-word Users') }}
                            </a>
                        </div>
                    </div>
                </div>

                </div>
        </div>
    </AuthenticatedLayout>
</template>
