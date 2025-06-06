<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Pie } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const props = defineProps({
  roleChart: {
    type: Array,
    default: () => [],
  },
})

const chartData = {
  labels: props.roleChart.map(item => item.label),
  datasets: [
    {
      label: 'Users by Role',
      backgroundColor: ['#6366f1', '#3b82f6', '#f59e0b', '#10b981'], // ใส่ให้พอจำนวน labels
      data: props.roleChart.map(item => item.count),
    },
  ],
}

</script>


<template>
  <Head :title="$t('dashboard')" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
        {{ $t('dashboard') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto space-y-6 sm:px-6 lg:px-8">

        <!-- Logged in & action buttons -->
        <div class="overflow-hidden bg-gray-200 shadow-sm dark:bg-gray-800 sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            {{ $t('message.loggedIn') }}

            <div class="flex flex-wrap gap-4 mt-4">
              <Link
                :href="route('teachers.bulk-edit.form')"
                class="px-4 py-2 text-white bg-green-600 rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
              >
                {{ $t('buttons.teachersBulkEdit') }}
              </Link>

              <Link
                :href="route('users.import.create')"
                class="px-4 py-2 text-white rounded-md shadow-sm bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
              >
                {{ $t('buttons.importUsers') }}
              </Link>

              <a
                :href="route('users.export.excel')"
                class="px-4 py-2 text-white bg-teal-600 rounded-md shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
              >
                {{ $t('buttons.exportExcel') }}
              </a>

              <a
                :href="route('users.export.word')"
                class="px-4 py-2 text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
              >
                {{ $t('buttons.exportWord') }}
              </a>
            </div>
          </div>
        </div>

        <!-- Chart: Users by Role -->
        <div class="p-6 bg-white rounded shadow-sm dark:bg-gray-900">
          <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">
            {{ $t('chart.usersByRole') }}
          </h3>
          <div class="max-w-md mx-auto">
            <Pie :data="chartData" />
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
