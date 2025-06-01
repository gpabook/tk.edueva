<template>
    <Head :title="$t('new_class_level')" />
    <AuthenticatedLayout>
        <template #header>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
        {{ $t('new_class_level') }}
      </h2>
    </template>

      <div class="p-4 mx-auto mt-6 max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
          <div>
            </div>
          <Link
            :href="route('class-levels.create')"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
          >
            <PlusIcon class="w-5 h-5 mr-2 -ml-1" aria-hidden="true" />
            {{ $t('new_class_level') }}
          </Link>
        </div>

        <div v-if="$page.props.flash && $page.props.flash.success" class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
          {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash && $page.props.flash.error" class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
          {{ $page.props.flash.error }}
        </div>

        <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <div v-if="props.classLevels.data.length > 0" class="overflow-x-auto">
              <table class="min-w-full border-collapse table-auto">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">#</th>
                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Code</th>
                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">English Name</th>
                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Thai Name</th>
                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase dark:text-gray-300">Rooms</th>
                    <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase dark:text-gray-300">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                  <tr
                    v-for="(item, idx) in props.classLevels.data"
                    :key="item.id"
                    class="hover:bg-gray-100 dark:hover:bg-gray-700"
                  >
                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap dark:text-gray-100">
                      {{ idx + 1 + (props.classLevels.current_page - 1) * props.classLevels.per_page }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap dark:text-gray-300">{{ item.code }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap dark:text-gray-300">{{ item.name_en }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap dark:text-gray-300">{{ item.name_th }}</td>
                    <td class="px-4 py-3 text-sm text-center text-gray-500 whitespace-nowrap dark:text-gray-300">{{ item.rooms_count }}</td>
                    <td class="px-4 py-3 space-x-2 text-sm font-medium text-center whitespace-nowrap">
                      <Link
                        :href="route('class-levels.edit', item.id)"
                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600"
                      >
                        Edit
                      </Link>
                      <button
                        @click="destroy(item.id)"
                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div v-if="props.classLevels.links && props.classLevels.data.length > 0" class="mt-4">
                <Pagination :links="props.classLevels.links" />
              </div>
            </div>

            <div v-else class="py-12 text-center text-gray-500 dark:text-gray-400">
              <BuildingOffice2Icon class="w-12 h-12 mx-auto text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No class levels</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">ยังไม่มีระดับชั้นใดๆ</p>
              <div class="mt-6">
                 <Link
                  :href="route('class-levels.create')"
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <PlusIcon class="w-5 h-5 mr-2 -ml-1" aria-hidden="true" />
                  สร้างระดับชั้นใหม่
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>

  <script setup>
  import { defineProps } from 'vue';
  import { Head, Link, router, usePage } from '@inertiajs/vue3';
  // Assuming AuthenticatedLayout is your main layout component, adjust path if needed.
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  // Assuming Pagination is a shared component, adjust path if needed.
  import Pagination from '@/Components/Pagination.vue';
  // Flowbite or Heroicons for icons
  import { PlusIcon, BuildingOffice2Icon } from '@heroicons/vue/24/outline'; // Or solid, adjust as needed

  // It's good practice to make `route` available globally via app.js or a plugin.
  // If not, you might need to import it:
  // import { route } from 'ziggy-js';
  // Or if you pass ziggy object via props, then: const page = usePage(); const route = page.props.ziggy.route;

  const props = defineProps({
    classLevels: {
      type: Object,
      required: true,
    },
    // errors: Object, // If you pass validation errors
    // auth: Object,   // If you pass auth info
    // flash: Object, // Handled by $page.props.flash
  });

  const page = usePage(); // Still useful for accessing flash messages or other shared props

  function destroy(id) {
    if (confirm('Are you sure you want to delete this class level? This action cannot be undone.')) {
      router.delete(window.route('class-levels.destroy', id), { // Using window.route assuming Ziggy is globally available
        preserveScroll: true,
        onSuccess: () => {
          // Optionally, you can add a success notification here if not relying solely on flash messages
          // For example, if you use a toast notification library
        },
        onError: (errors) => {
          // Handle any errors during deletion, e.g., display a toast notification
          if (errors.message) {
            alert('Error deleting class level: ' + errors.message);
          } else {
            alert('An unexpected error occurred while deleting the class level.');
          }
          console.error("Deletion error:", errors);
        }
      });
    }
  }
  </script>
