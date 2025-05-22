<template>
   <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold">Permissions</h1>
        <Link
          v-if="can('create permissions')"
          :href="route('permissions.create')" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">
          Create Permission
        </Link>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Id</th>
            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Name</th>
            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="perm in permissions.data"
            :key="perm.id"
            class="border-t"
          >
            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ perm.id }}</td>
            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ perm.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
              <Link
                v-if="can('edit permissions')"
                :href="route('permissions.edit', perm.id)"
                class="underline"
              >
                Edit
              </Link>
              <button
                v-if="can('delete permissions')"
                @click="destroy(perm.id)"
                class="text-red-600 underline"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      </div>

      <Pagination :links="permissions.links" class="mt-4" />
    </div>
    </div>
    </div>
    </div>
  </template>

  <script setup>
  import { Link, useForm, usePage } from '@inertiajs/vue3'
  import Pagination from '@/Components/Pagination.vue'
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  defineOptions({ layout: AuthenticatedLayout});

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
