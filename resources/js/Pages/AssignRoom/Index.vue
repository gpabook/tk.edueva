<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { Head, useForm, router, Link } from '@inertiajs/vue3'
import { ref, reactive, watch } from 'vue'
import { debounce } from 'lodash'


const props = defineProps({
    students: Object,
    rooms: Array,
    classLevels: Array,
    filters: Object,
})

// ฟอร์มสำหรับกำหนดห้อง
const form = useForm({
    user_id: '',
    room_id: '',
})

// ฟอร์มสำหรับกรอง/ค้นหา
const filters = reactive({
  search: props.filters.search || '',
  class_level_id: props.filters.class_level_id || ''
})

// นักเรียนที่ถูกเลือก
const selectedStudent = ref(null)

const selectStudent = (student) => {
    selectedStudent.value = student
    form.user_id = student.id
    form.room_id = ''
}

// ส่งคำขอกำหนดห้อง
const assignRoom = () => {
    if (!form.user_id || !form.room_id) {
        alert('กรุณาเลือกนักเรียนและห้องเรียน')
        return
    }

    form.post(route('assign-room.assign'), {
        preserveScroll: true,
        onSuccess: () => {
            selectedStudent.value = null
            form.reset()
        },
    })
}

// ฟังก์ชัน debounced สำหรับการค้นหาอัตโนมัติ
// debounce ฟังก์ชันค้นหา
const searchStudents = debounce(() => {
    router.get(route('assign-room.index'), filters, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}, 400)

// watch ทั้ง object filters แบบ deep
watch(
    () => ({ ...filters }), // ใช้ spread เพื่อให้ Vue ตรวจทุก key
    () => {
        searchStudents()
    },
    { deep: true }
)
</script>

<template>
    <Head title="Assign Room to Students" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                Assign Room to Students
            </h2>
        </template>

        <div class="max-w-6xl px-4 py-8 mx-auto">
            <!-- Filters -->
            <div class="flex flex-col items-center gap-4 mb-4 md:flex-row">
                <input
                    type="text"
                    v-model="filters.search"
                    placeholder="Search by name or student ID"
                    class="w-full p-2 border rounded md:w-1/2 dark:bg-gray-700 dark:text-white"
                />

                <select
                    v-model="filters.class_level_id"
                    class="w-full p-2 border rounded md:w-1/4 dark:bg-gray-700 dark:text-white"
                >
                    <option value="">All Class Levels</option>
                    <option
                        v-for="level in props.classLevels"
                        :key="level.id"
                        :value="level.id"
                    >
                        {{ level.name_th }}
                    </option>
                </select>
            </div>

            <!-- Layout: Student list + Assign form -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Left: List of students -->
                <div class="p-4 bg-white rounded shadow dark:bg-gray-800">
                    <h3 class="mb-2 font-semibold text-gray-700 dark:text-gray-200">Students</h3>
                    <ul class="divide-y dark:divide-gray-600 max-h-[600px] overflow-y-auto">
                        <li
                            v-for="student in props.students.data"
                            :key="student.id"
                            class="px-2 py-2 rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                            @click="selectStudent(student)"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    {{ student.name_th+" "+student.surname_th || student.name_en+" "+student.surname_en }}
                                    <span class="text-xs text-gray-400">({{ student.student_id }})</span>
                                </div>
                                <div class="text-xs text-right text-gray-500 dark:text-gray-300">
                                    <template v-if="student.current_room?.length > 0">
                                        {{ student.current_room[0]?.class_level?.name_th }} {{ student.current_room[0]?.name_room_th }}
                                    </template>
                                    <template v-else>
                                        <span class="text-red-500">ยังไม่กำหนดห้อง</span>
                                    </template>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <!-- Pagination -->
                    <div class="mt-4 overflow-x-auto">
                        <div class="flex flex-wrap justify-center gap-2">
                            <template v-if="props.students.links">
                                <Link
                                    v-for="link in props.students.links"
                                    :key="link.label"
                                    :href="link.url || '#'"
                                    class="px-3 py-1 text-sm border rounded min-w-[40px] text-center"
                                    :class="{
                                        'bg-blue-500 text-white': link.active,
                                        'text-gray-700 dark:text-gray-300': !link.active,
                                        'cursor-not-allowed': !link.url,
                                    }"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Right: Assign Room Form -->
                <div class="p-4 bg-white rounded shadow dark:bg-gray-800">
                    <h3 class="mb-2 font-semibold text-gray-700 dark:text-gray-200">Assign Room</h3>

                    <div v-if="selectedStudent">
                        <p class="mb-2 text-gray-800 dark:text-gray-100">
                            Assign room to: <strong>{{ selectedStudent.name_th || selectedStudent.name_en }}</strong>
                        </p>

                        <select
                            v-model="form.room_id"
                            class="w-full p-2 border rounded dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">เลือกห้อง</option>
                            <option
                                v-for="room in props.rooms"
                                :key="room.id"
                                :value="room.id"
                            >
                                {{ room.class_level?.name_th }} - {{ room.name_room_th }}
                            </option>
                        </select>

                        <PrimaryButton class="mt-4" @click="assignRoom">
                            Assign
                        </PrimaryButton>
                    </div>

                    <p v-else class="text-sm text-gray-500 dark:text-gray-400">เลือกนักเรียนจากฝั่งซ้ายก่อน</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
