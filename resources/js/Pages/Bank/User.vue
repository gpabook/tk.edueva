<script setup>
import { Inertia } from '@inertiajs/inertia'
import { Head, Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  users: Object,
  filters: Object
})

const search = ref(props.filters.search || '')

function doSearch() {
  Inertia.get(
    route('bank.user'),
    { search: search.value },
    { preserveState: true, replace: true }
  )
}

const formatMoney = (value) =>
  Number(value).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
</script>

<template>
  <Head :title="$t('manage_bank_user')" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
        {{ $t('manage_bank_user') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
          <div class="p-6 bg-white dark:bg-gray-800">
            <!-- 🔍 Search -->
            <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
              <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                {{ $t('bank.my_account') }}
              </h3>
              <div class="flex">
                <input
                  v-model="search"
                  @keyup.enter="doSearch"
                  type="text"
                  :placeholder="$t('search_user')"
                  class="px-3 py-2 text-sm border border-gray-300 rounded-l-md focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
                <button
                  @click="doSearch"
                  class="px-4 py-2 text-sm text-white bg-blue-600 rounded-r-md hover:bg-blue-700 focus:outline-none focus:ring-1 focus:ring-blue-500"
                >
                  {{ $t('search') }}
                </button>
              </div>
            </div>

            <!-- 📄 Table -->
            <div class="overflow-x-auto">
              <table class="w-full text-sm text-left text-gray-600 rtl:text-right dark:text-gray-300">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                  <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">UserID</th>
                    <th class="px-4 py-2">{{ $t('name') }}</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">{{ $t('action') }}</th>
                    <th class="px-4 py-2">{{ $t('bank.balance') }}</th>
                    <th class="px-4 py-2">{{ $t('created_at') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="item in users.data"
                    :key="item.id"
                    class="transition-colors duration-200 border-b hover:bg-blue-50 dark:hover:bg-gray-700 dark:border-gray-700"
                  >
                    <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ item.id }}</td>
                    <td class="px-4 py-2">{{ item.user_id }}</td>
                    <td class="px-4 py-2">{{ item.user?.name }}</td>
                    <td class="px-4 py-2">{{ item.user?.email }}</td>
                    <td class="px-4 py-2">
                      <Link
                        :href="route('bank.account', item.user_id)"
                        class="inline-block px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700"
                      >
                        {{ $t('bank.deposit') }} / {{ $t('bank.withdraw') }}
                      </Link>
                    </td>
                    <td class="px-4 py-2">{{ formatMoney(item.balance) }}</td>
                    <td class="px-4 py-2">
                      {{ new Date(item.created_at).toLocaleDateString('th-TH') }}
                      {{ new Date(item.created_at).toLocaleTimeString('th-TH') }}
                    </td>
                  </tr>
                </tbody>
              </table>

              <!-- Pagination -->
              <div class="mt-6">
                <Pagination :links="users.links" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
