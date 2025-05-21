<template layout="Layouts/AuthenticatedLayout">
    <Head :title="'Role: ' + role.name" />

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                         <h1 class="text-2xl font-semibold">Role Details</h1>
                         <Link :href="route('roles.edit', role.id)" class="px-4 py-2 text-white bg-indigo-500 rounded hover:bg-indigo-600">
                            Edit Role
                        </Link>
                    </div>


                    <div class="mb-4">
                        <strong class="block text-sm font-medium text-gray-700">ID:</strong>
                        <p class="mt-1 text-sm text-gray-900">{{ role.id }}</p>
                    </div>

                    <div class="mb-4">
                        <strong class="block text-sm font-medium text-gray-700">Name:</strong>
                        <p class="mt-1 text-sm text-gray-900">{{ role.name }}</p>
                    </div>

                    <div class="mb-6">
                        <h3 class="mb-2 text-lg font-medium text-gray-900">Assigned Permissions:</h3>
                        <ul v-if="role.permissions && role.permissions.length > 0" class="space-y-1 list-disc list-inside">
                            <li v-for="permission in role.permissions" :key="permission.id" class="text-sm text-gray-700">
                                {{ permission.name }}
                            </li>
                        </ul>
                        <p v-else class="text-sm text-gray-500">No permissions assigned to this role.</p>
                    </div>

                    <div class="mt-6">
                        <Link :href="route('roles.index')" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">
                            Back to Roles List
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
// import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
defineOptions({ layout: AuthenticatedLayout});

defineProps({
    role: Object // Passed from RoleController:show, includes 'permissions'
});
</script>
