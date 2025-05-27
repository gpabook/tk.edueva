<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // Assuming you have an auth layout

defineProps({
  rooms: Object, // Paginated rooms data from RoomController
  success: String, // Flash message
});

const deleteRoom = (roomId) => {
  if (confirm('Are you sure you want to delete this room?')) {
    router.delete(route('rooms.destroy', roomId), {
      preserveScroll: true,
      onSuccess: () => {
        // Optionally handle success, like a toast notification if not using flash messages
      },
    });
  }
};
</script>

<template>
  <Head title="Manage Rooms" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">Manage Rooms</h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div v-if="success" class="p-4 mb-4 text-green-700 bg-green-100 border border-green-300 rounded">
          {{ success }}
        </div>

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="mb-4">
              <Link :href="route('rooms.create')" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                Create New Room
              </Link>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">ID</th>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Class Level</th>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Room Name (EN)</th>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Room Name (TH)</th>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Advisors</th>
                  <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="room in rooms.data" :key="room.id">
                  <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ room.id }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ room.class_level.name_en }} ({{ room.class_level.code }})</td>
                  <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ room.name_room_en }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ room.name_room_th }}</td>
                  <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
  <span v-if="room.advisors && room.advisors.length > 0">
    {{ room.advisors.map(adv => adv.name).join(', ') }}
  </span>
  <span v-else class="text-gray-400">N/A</span>
</td>
                  <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                    <Link :href="route('rooms.edit', room.id)" class="mr-3 text-indigo-600 hover:text-indigo-900">Edit</Link>
                    <button @click="deleteRoom(room.id)" class="text-red-600 hover:text-red-900">Delete</button>
                  </td>
                </tr>
                <tr v-if="rooms.data.length === 0">
                    <td colspan="6" class="px-6 py-4 text-sm text-center text-gray-500 whitespace-nowrap">No rooms found.</td>
                </tr>
              </tbody>
            </table>

            <div v-if="rooms.links.length > 3" class="flex items-center justify-between mt-4">
               <div class="flex flex-wrap -mb-1">
                    <template v-for="(link, key) in rooms.links" :key="key">
                        <div v-if="link.url === null" class="px-4 py-3 mb-1 mr-1 text-sm leading-4 text-gray-400 border rounded" v-html="link.label" />
                        <Link v-else class="px-4 py-3 mb-1 mr-1 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500" :class="{ 'bg-blue-500 text-white': link.active }" :href="link.url" v-html="link.label" preserve-scroll />
                    </template>
                </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
