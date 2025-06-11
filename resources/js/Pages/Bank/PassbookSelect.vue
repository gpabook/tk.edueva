<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'

const toast = useToast()

const props = defineProps({
  user: Object,
  printed: Number,
  pending: Number,
  total: Number,
  info: String
})

const form = useForm({
  student_id: props.user.student_id,
  start_line: props.printed + 1,
  line_count: props.pending
})

const submit = () => {
  const query = new URLSearchParams({
    student_id: form.student_id,
    start_line: form.start_line,
    line_count: form.line_count,
  }).toString()

  const pdfRoute = `/passbook/custom/preview?${query}`
  window.open(pdfRoute, '_blank')
}

</script>

<template>
  <AuthenticatedLayout>
    <Head title="พิมพ์สมุดบัญชี" />

    <template #header>
      <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">พิมพ์สมุดบัญชี</h2>
    </template>

    <div class="max-w-3xl p-6 mx-auto mt-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
      <!-- Info alert -->
      <div
        v-if="info"
        class="flex items-center p-4 mb-4 text-sm text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50 dark:bg-yellow-100 dark:text-yellow-900"
        role="alert"
      >
        <svg class="flex-shrink-0 inline w-5 h-5 mr-3 text-yellow-800" fill="currentColor" viewBox="0 0 20 20">
          <path
            d="M8.257 3.099c.366-.446.957-.447 1.323 0l6.518 7.955c.392.478.073 1.181-.528 1.181H2.267c-.601 0-.92-.703-.528-1.181l6.518-7.955zM11 13a1 1 0 10-2 0 1 1 0 002 0z"
          />
        </svg>
        <div>{{ info }}</div>
      </div>
<!-- ปุ่มย้อนกลับ -->
<div class="mb-4">
  <Link
    :href="route('bank.account', props.user.student_id)"
    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded hover:bg-gray-300 dark:text-white dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600"
  >
    <svg
      class="w-4 h-4 mr-2"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      viewBox="0 0 24 24"
    >
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    ย้อนกลับไปหน้าบัญชี
  </Link>
</div>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-6">
        <input type="hidden" v-model="form.student_id" />

        <div class="space-y-1">
          <p class="text-gray-700 dark:text-gray-200">
            <span class="font-semibold">รายการที่พิมพ์ไปแล้ว:</span> {{ printed }}
          </p>
          <p class="text-gray-700 dark:text-gray-200">
            <span class="font-semibold">รายการที่ยังไม่พิมพ์:</span> {{ pending }}
          </p>
          <p class="text-gray-700 dark:text-gray-200">
            <span class="font-semibold">รวมทั้งหมด:</span> {{ total }}
          </p>
        </div>

        <div>
          <label for="start_line" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">
            เริ่มพิมพ์จากบรรทัดที่
          </label>
          <input
            type="number"
            id="start_line"
            v-model="form.start_line"
            min="1"
            class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:placeholder-gray-400"
          />
        </div>

        <div>
          <label for="line_count" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-200">
            จำนวนบรรทัดที่ต้องการพิมพ์
          </label>
          <input
            type="number"
            id="line_count"
            v-model="form.line_count"
            min="1"
            class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:placeholder-gray-400"
          />
        </div>

        <button
          type="submit"
          class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800"
        >
          <svg
            class="w-5 h-5 mr-2 -ml-1"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
          </svg>
          พิมพ์สมุดบัญชี
        </button>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
