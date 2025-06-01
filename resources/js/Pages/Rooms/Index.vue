<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineProps({
  rooms: Object,      // Paginated list of rooms
  success: String     // Flash message
})

const deleteRoom = (id) => {
  if (confirm('Are you sure you want to delete this room?')) {
    router.delete(route('rooms.destroy', id), {
      preserveScroll: true
    })
  }
}
</script>

<template>
  <Head :title="$t('rooms.manage')" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
        {{ $t('rooms.manage') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div v-if="success" class="p-4 mb-4 text-green-800 bg-green-100 border border-green-300 rounded dark:text-green-200 dark:bg-green-900 dark:border-green-700">
          {{ success }}
        </div>

        <div class="overflow-hidden bg-white shadow sm:rounded-lg dark:bg-gray-800">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <!-- Create New Room -->
            <div class="mb-6 text-right">
              <Link
                :href="route('rooms.create')"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
          >
                {{ $t('rooms.create') }}
              </Link>
            </div>

            <!-- Rooms Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                  <tr>
                    <th class="px-4 py-2 text-left">{{ $t('rooms.id') }}</th>
                    <th class="px-4 py-2 text-left">{{ $t('rooms.class_level') }}</th>
                    <th class="px-4 py-2 text-left">{{ $t('rooms.name_en') }}</th>
                    <th class="px-4 py-2 text-left">{{ $t('rooms.name_th') }}</th>
                    <th class="px-4 py-2 text-left">{{ $t('rooms.advisors') }}</th>
                    <th class="px-4 py-2 text-left">{{ $t('rooms.actions') }}</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                  <tr
                    v-for="room in rooms.data"
                    :key="room.id"
                    class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800"
                  >
                    <td class="px-4 py-2">{{ room.id }}</td>
                    <td class="px-4 py-2">
                      {{ room.class_level.name_en }} ({{ room.class_level.code }})
                    </td>
                    <td class="px-4 py-2">{{ room.name_room_en }}</td>
                    <td class="px-4 py-2">{{ room.name_room_th }}</td>
                    <td class="px-4 py-2">
                      <span v-if="room.advisors?.length">
                        {{ room.advisors.map(a => a.name).join(', ') }}
                      </span>
                      <span v-else class="text-gray-400 dark:text-gray-500">N/A</span>
                    </td>
                    <td class="px-4 py-2 space-x-2">
                      <Link
                        :href="route('rooms.edit', room.id)"
                        class="text-indigo-600 hover:underline dark:text-indigo-400"
                      >
                        {{ $t('edit') }}
                      </Link>
                      <button
                        @click="deleteRoom(room.id)"
                        class="text-red-600 hover:underline dark:text-red-400"
                      >
                        {{ $t('delete') }}
                      </button>
                    </td>
                  </tr>

                  <tr v-if="rooms.data.length === 0">
                    <td colspan="6" class="px-4 py-4 text-center text-gray-400 dark:text-gray-500">
                      {{ $t('rooms.empty') }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div v-if="rooms.links.length > 3" class="mt-6">
              <nav class="flex flex-wrap justify-center gap-2">
                <template v-for="(link, index) in rooms.links" :key="index">
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
