<script setup>
import { Head, usePage, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { reactive, watch, onMounted } from 'vue'
import { debounce } from 'lodash'

import { useToast } from 'vue-toast-notification'

const page = usePage()
const toast = useToast()

onMounted(() => {
  const successMessage = page.props.flash?.success
  if (successMessage) {
    toast.success(successMessage, {
      position: 'top-right',
      duration: 3000,
    })
  }
})

const props = defineProps({
  students: Object,
  rooms: Array,
  classLevels: Array,
  filters: Object,
})

// ✅ ใช้ reactive เพื่อให้ watch ทำงานกับ object
const filters = reactive({
  search: props.filters.search || '',
  class_level_id: props.filters.class_level_id || '',
  room_id: props.filters.room_id || '',
})

// ✅ debounce router.get สำหรับค้นหา
const searchStudents = debounce(() => {
  router.get(route('students.index'), filters, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
}, 400)

// ✅ ใช้ deep watch
watch(
  () => ({ ...filters }),
  () => {
    searchStudents()
  },
  { deep: true }
)
</script>

<template>
  <Head title="All Students" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
        {{ $t('menu.all_students') }}
      </h2>
    </template>

    <div class="px-4 py-8 mx-auto max-w-7xl">
      <!-- Filters -->
      <div class="flex flex-wrap gap-4 mb-6">
        <input
          v-model="filters.search"
          type="text"
          placeholder="Search..."
          class="p-2 border rounded dark:bg-gray-700 dark:text-white"
        />

        <select v-model="filters.class_level_id" class="p-2 border rounded dark:bg-gray-700 dark:text-white">
          <option value="">All Class Levels</option>
          <option v-for="level in props.classLevels" :key="level.id" :value="level.id">
            {{ level.name_th }}
          </option>
        </select>

        <select v-model="filters.room_id" class="p-2 border rounded dark:bg-gray-700 dark:text-white">
          <option value="">All Rooms</option>
          <option v-for="room in props.rooms" :key="room.id" :value="room.id">
            {{ room.class_level?.name_th }} {{ room.name_room_th }}
          </option>
        </select>

        <a
          :href="route('students.export.excel', filters)"
          class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700"
        >
          Export Excel
        </a>
        <a
  :href="route('students.export.excel', filters)"
  class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700"
>
  Export Excel guardians
</a>

        <a
          :href="route('students.export.pdf', filters)"
          class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700"
        >
          Export PDF
        </a>
      </div>

      <!-- Student Table -->
      <div class="overflow-x-auto bg-white rounded shadow dark:bg-gray-800">
        <table class="min-w-full text-sm text-left">
          <thead class="bg-gray-100 dark:bg-gray-700">
            <tr>
              <th class="px-4 py-2">ID</th>
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Room</th>
              <th class="px-4 py-2">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="student in props.students.data"
              :key="student.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <td class="px-4 py-2">{{ student.student_id }}</td>
              <td class="px-4 py-2">{{ student.name_th }} {{ student.surname_th }}</td>
              <td class="px-4 py-2">
                {{
                  student.current_room?.[0]?.class_level?.name_th
                }} {{ student.current_room?.[0]?.name_room_th }}
              </td>
              <td class="px-4 py-2">
                <Link
                  :href="route('students.edit', student.id)"
                  class="text-blue-600 hover:underline"
                >
                  Edit
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex justify-center mt-6">
        <template v-for="link in props.students.links" :key="link.label">
          <Link
            :href="link.url || '#'"
            class="px-3 py-1 mx-1 border rounded"
            :class="{ 'bg-blue-500 text-white': link.active }"
            v-html="link.label"
          />
        </template>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
