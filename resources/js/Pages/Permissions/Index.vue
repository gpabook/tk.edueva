<template>
    <div class="py-12 bg-white shadow-sm dark:bg-gray-800">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Permissions</h1>
                        <Link
                            v-if="can('create permissions')"
                            :href="route('permissions.create')"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-offset-gray-800"
                        >
                            Create Permission
                        </Link>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-md dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Id</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Name</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <tr
                                    v-for="perm in permissions.data"
                                    :key="perm.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50"
                                >
                                    <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap dark:text-gray-300">{{ perm.id }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap dark:text-gray-300">{{ perm.name }}</td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        <Link
                                            v-if="can('edit permissions')"
                                            :href="route('permissions.edit', perm.id)"
                                            class="font-medium text-blue-600 underline dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            v-if="can('delete permissions')"
                                            @click="destroy(perm.id)"
                                            class="ml-3 font-medium text-red-600 underline dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="permissions.data.length === 0">
                                    <td colspan="3" class="px-6 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                        No permissions found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <Pagination :links="permissions.links" class="mt-6" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue' // Ensure Pagination also supports dark mode
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
defineOptions({ layout: AuthenticatedLayout });

const { permissions, auth } = defineProps({
    permissions: Object,
    auth: Object,
})

// helper ตรวจสิทธิ์ฝั่ง client
const can = (permission) =>
    Array.isArray(auth.user.permissions) &&
    auth.user.permissions.includes(permission)

const form = useForm()

function destroy(id) {
    if (!can('delete permissions')) return
    if (confirm('ลบ permission นี้จริงหรือไม่?')) {
        form.delete(route('permissions.destroy', id))
    }
}
</script>
