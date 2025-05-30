<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                {{ $t('dashboard') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        You're logged in!
                        <div class="flex gap-4">

                <Link :href="route('teachers.bulk-edit.form')" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">
                    TeachersBulkEdit
                </Link>

                <Link :href="route('users.import.create')" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700">
                  Import CSV Users
                </Link>

                <a
        :href="route('users.export.excel')"
        class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-700"
      >
        Export Excel Users
      </a>
      <a :href="route('users.export.word')"
   class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-700">
   Export Ms-word Users
</a>
                </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
