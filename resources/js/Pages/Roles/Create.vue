<template layout="Layouts/AuthenticatedLayout">
    <Head title="Create Role" />

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="mb-6 text-2xl font-semibold">Create New Role</h1>

                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Role Name</label>
                            <input type="text" v-model="form.name" id="name" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                            <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
                        </div>

                        <div class="mb-6">
                            <h3 class="mb-2 text-lg font-medium text-gray-900">Assign Permissions</h3>
                            <div class="p-3 space-y-2 overflow-y-auto border border-gray-300 rounded-md max-h-60">
                                <div v-for="permission in permissions" :key="permission.id" class="flex items-center">
                                    <input type="checkbox" :id="'permission_' + permission.id" :value="permission.id" v-model="form.permissions" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <label :for="'permission_' + permission.id" class="block ml-2 text-sm text-gray-900">{{ permission.name }}</label>
                                </div>
                                <div v-if="!permissions || permissions.length === 0" class="text-sm text-gray-500">
                                    No permissions available. Make sure to seed them.
                                </div>
                            </div>
                             <div v-if="form.errors.permissions" class="mt-1 text-sm text-red-600">{{ form.errors.permissions }}</div>
                        </div>


                        <div class="flex items-center justify-end space-x-3">
                            <Link :href="route('roles.index')" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">
                                Cancel
                            </Link>
                            <button type="submit" :disabled="form.processing" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" :class="{ 'opacity-50': form.processing }">
                                Create Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
// import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
defineOptions({ layout: AuthenticatedLayout});
defineProps({
    permissions: Array, // Passed from RoleController:create
    errors: Object // Automatically provided by Inertia for validation errors
});

const form = useForm({
    name: '',
    permissions: [] // Will hold an array of selected permission IDs
});

const submit = () => {
    form.post(route('roles.store'), {
        // onSuccess: () => form.reset(), // Optional: Reset form on success
    });
};
</script>
