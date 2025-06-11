<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { onMounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useToast } from 'vue-toast-notification'

const toast = useToast()
const page = usePage() //  รับ props flash จาก Inertia


onMounted(() => {
  const successMessage = page.props.flash?.success
  if (successMessage) {
    toast.success(successMessage, {
      position: 'top-right',
      duration: 3000,
    })
  }
})

defineProps({
  users: Object,
  success: String,
  error: String
})

const deleteUser = (id) => {
  if (confirm('Are you sure you want to delete this user?')) {
    router.delete(route('users.destroy', id), {
      preserveScroll: true
    })
  }
}
</script>

<template>
  <Head :title="$t('menu.user_management')" />

  <AuthenticatedLayout>
    <!-- Header slot -->
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
          {{ $t('menu.user_management') }}
        </h2>
        <Link
          :href="route('users.create')"
          class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
        >
          + {{ $t('users.create') }}
        </Link>
      </div>
    </template>


    <div class="mx-auto mt-6 max-w-7xl sm:px-6 lg:px-8">
            <!-- Flash messages
      <div v-if="success" class="p-4 mb-4 text-green-800 bg-green-100 border border-green-300 rounded dark:text-green-200 dark:bg-green-900 dark:border-green-700">
        {{ success }}
      </div>
      <div v-if="error" class="p-4 mb-4 text-red-800 bg-red-100 border border-red-300 rounded dark:text-red-200 dark:bg-red-900 dark:border-red-700">
        {{ error }}
      </div>
      -->

      <!-- Users table -->
      <div class="overflow-hidden bg-white shadow sm:rounded-lg dark:bg-gray-800">
        <div class="p-6 overflow-x-auto">
          <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
              <tr>
                <th class="px-4 py-2 text-left">{{ $t('id') }}</th>
                <th class="px-4 py-2 text-left">{{ $t('name') }}</th>
                <th class="px-4 py-2 text-left">{{ $t('email_label') }}</th>
                <th class="px-4 py-2 text-left">{{ $t('roles') }}</th>
                <th class="px-4 py-2 text-left">{{ $t('action') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
              <tr
                v-for="user in users.data"
                :key="user.id"
                class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800"
              >
                <td class="px-4 py-2">{{ user.id }}</td>
                <td class="px-4 py-2">{{ user.name }}</td>
                <td class="px-4 py-2">{{ user.email }}</td>
                <td class="px-4 py-2">
                    {{ $te(`roles.${user.role_key}`) ? $t(`roles.${user.role_key}`) : user.role_key }}
                </td>
                <td class="px-4 py-2 space-x-2">
                  <Link
                    :href="route('users.edit', user.id)"
                    class="text-indigo-600 hover:underline dark:text-indigo-400"
                  >
                    {{ $t('edit') }}
                  </Link>
                  <button
                    @click="deleteUser(user.id)"
                    class="text-red-600 hover:underline dark:text-red-400"
                  >
                    {{ $t('delete') }}
                  </button>
                </td>
              </tr>

              <tr v-if="users.data.length === 0">
                <td colspan="5" class="px-4 py-4 text-center text-gray-400 dark:text-gray-500">
                  {{ $t('users.empty') }}
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <div v-if="users.links.length > 3" class="mt-6">
            <nav class="flex flex-wrap justify-center gap-2">
              <template v-for="(link, index) in users.links" :key="index">
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
  </AuthenticatedLayout>
</template>
