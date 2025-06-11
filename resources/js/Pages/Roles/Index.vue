<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineProps({
  roles: Object,       // Paginated list of roles
  success: String      // Flash message
})

const deleteRole = (id) => {
  if (confirm('Are you sure you want to delete this role?')) {
    router.delete(route('roles.destroy', id), {
      preserveScroll: true
    })
  }
}
</script>

<template>
  <Head :title="$t('roles.manage')" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
        {{ $t('roles.manage') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div v-if="success" class="p-4 mb-4 text-green-800 bg-green-100 border border-green-300 rounded dark:text-green-200 dark:bg-green-900 dark:border-green-700">
          {{ success }}
        </div>

        <div class="overflow-hidden bg-white shadow sm:rounded-lg dark:bg-gray-800">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <!-- Create Role Button -->
            <div class="mb-6 text-right">
              <Link
                :href="route('roles.create')"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                {{ $t('roles.create') }}
              </Link>
            </div>

            <!-- Roles Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                  <tr>
                    <th class="px-4 py-2 text-left">{{ $t('id') }}</th>
                    <th class="px-4 py-2 text-left">{{ $t('roles.name') }}</th>
                    <th class="px-4 py-2 text-left">{{ $t('roles.permissions_count') }}</th>
                    <th class="px-4 py-2 text-left">{{ $t('action') }}</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                  <tr
                    v-for="role in roles.data"
                    :key="role.id"
                    class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800"
                  >
                    <td class="px-4 py-2">{{ role.id }}</td>
                    <td class="px-4 py-2">
                      <Link
                        :href="route('roles.show', role.id)"
                        class="text-blue-600 hover:underline dark:text-blue-400"
                      >
                        {{ role.name }}
                      </Link>
                    </td>
                    <td class="px-4 py-2">{{ role.permissions_count }}</td>
                    <td class="px-4 py-2 space-x-2">
                      <Link
                        :href="route('roles.edit', role.id)"
                        class="text-indigo-600 hover:underline dark:text-indigo-400"
                      >
                        {{ $t('edit') }}
                      </Link>
                      <button
                        @click="deleteRole(role.id)"
                        class="text-red-600 hover:underline dark:text-red-400"
                      >
                        {{ $t('delete') }}
                      </button>
                    </td>
                  </tr>

                  <tr v-if="roles.data.length === 0">
                    <td colspan="4" class="px-4 py-4 text-center text-gray-400 dark:text-gray-500">
                      {{ $t('roles.empty') }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div v-if="roles.links.length > 3" class="mt-6">
              <nav class="flex flex-wrap justify-center gap-2">
                <template v-for="(link, index) in roles.links" :key="index">
                  <div
                    v-if="!link.url"
                    class="px-3 py-1 text-sm text-gray-400 border rounded dark:text-gray-500"
                    v-html="link.label"
                  />
                  <Link
                    v-else
                    :href="link.url"
                    v-html="link.label"
                    class="px-3 py-1 text-sm border rounded hover:bg-white dark:hover:bg-gray-700"
                    :class="{ 'bg-blue-500 text-white dark:bg-blue-600': link.active }"
                    preserve-scroll
                  />
                </template>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
