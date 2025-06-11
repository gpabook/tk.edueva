<script setup>
import { Head, useForm, usePage, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import { ArrowLeftIcon } from '@heroicons/vue/20/solid'
import { computed, ref, watch, onMounted } from 'vue'
import { useToast } from 'vue-toast-notification'

const props = defineProps({
  student: Object,
  rooms: Array,
})

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

// ✅ ป้องกัน error ถ้าไม่มี guardian
const guardian = props.student.guardian || {}

// แสดงชื่อห้อง
const displayCurrentRoom = computed(() => {
  const room = props.student.current_room?.[0]
  return room
    ? `${room.class_level?.name_th || ''} - ${room.name_room_th || ''}`
    : 'ยังไม่ได้กำหนดห้อง'
})

// ฟังก์ชันแปลง national_id เป็นรูปแบบมีขีด
const formatWithDashes = (raw) => {
  if (!raw || raw.length !== 13) return raw
  return `${raw[0]}-${raw.slice(1, 5)}-${raw.slice(5, 10)}-${raw.slice(10, 12)}-${raw[12]}`
}

const autoFormatNationalId = (event) => {
  let value = event.target.value.replace(/\D/g, '')
  if (value.length > 13) value = value.slice(0, 13)

  let formatted = ''
  if (value.length >= 1) formatted += value[0]
  if (value.length >= 2) formatted += '-' + value.slice(1, 5)
  if (value.length >= 6) formatted += '-' + value.slice(5, 10)
  if (value.length >= 11) formatted += '-' + value.slice(10, 12)
  if (value.length === 13) formatted += '-' + value[12]

  nationalIdFormatted.value = formatted
}

const form = useForm({
  // 🧒 ข้อมูลนักเรียน
  prefix_th: props.student.prefix_th || '',
  name_th: props.student.name_th || '',
  surname_th: props.student.surname_th || '',
  name_en: props.student.name_en || '',
  surname_en: props.student.surname_en || '',
  student_id: props.student.student_id || '',
  national_id: props.student.national_id || '',
  email: props.student.email || '',
  gender: props.student.gender || '',
  birthday: props.student.birthday || '',

  // ผู้ปกครอง
  guardian_prefix_th: guardian.prefix_th || '',
  guardian_name_th: guardian.name_th || '',
  guardian_surname_th: guardian.surname_th || '',
  guardian_phone_1: guardian.phone_1 || '',
  guardian_phone_2: guardian.phone_2 || '',
  guardian_relationship: guardian.relationship || '',
})

// 📦 ref สำหรับแสดง national_id แบบมีขีด
const nationalIdFormatted = ref(formatWithDashes(form.national_id))

// ⏎ เมื่อกรอกใหม่ แปลงเป็นตัวเลขล้วนสำหรับส่งไป backend
watch(nationalIdFormatted, (val) => {
  form.national_id = val.replace(/\D/g, '')
})

const submit = () => {
  form.put(route('students.update', props.student.id))
}
</script>

<template>
  <Head title="Edit Student" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Edit Student</h2>
    </template>

    <div class="max-w-4xl py-12 mx-auto">
      <form @submit.prevent="submit" class="p-6 space-y-10 bg-white rounded shadow dark:bg-gray-800 dark:text-white">
        <!-- 👦 Section นักเรียน -->
        <section>
          <h3 class="mb-4 text-lg font-bold text-gray-700 dark:text-white">ข้อมูลนักเรียน</h3>

          <div class="grid grid-cols-3 gap-4">
            <div>
              <InputLabel for="prefix_th" value="คำนำหน้า (TH)" />
              <select
                id="prefix_th"
                v-model="form.prefix_th"
                class="w-full p-2 mt-1 border rounded dark:bg-gray-700 dark:text-white"
              >
                <option value="">-- เลือก --</option>
                <option value="ด.ช.">ด.ช.</option>
                <option value="ด.ญ.">ด.ญ.</option>
                <option value="นาย">นาย</option>
                <option value="นางสาว">นางสาว</option>
              </select>
              <InputError :message="form.errors.prefix_th" />
            </div>

            <div>
              <InputLabel for="name_th" value="ชื่อ (TH)" />
              <TextInput id="name_th" v-model="form.name_th" class="block w-full mt-1" />
              <InputError :message="form.errors.name_th" />
            </div>

            <div>
              <InputLabel for="surname_th" value="นามสกุล (TH)" />
              <TextInput id="surname_th" v-model="form.surname_th" class="block w-full mt-1" />
              <InputError :message="form.errors.surname_th" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
              <InputLabel for="name_en" value="ชื่อ (EN)" />
              <TextInput id="name_en" v-model="form.name_en" class="block w-full mt-1" />
            </div>

            <div>
              <InputLabel for="surname_en" value="นามสกุล (EN)" />
              <TextInput id="surname_en" v-model="form.surname_en" class="block w-full mt-1" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
              <InputLabel for="student_id" value="รหัสนักเรียน" />
              <TextInput id="student_id" v-model="form.student_id" class="block w-full mt-1" />
              <InputError :message="form.errors.student_id" />
            </div>

            <div>
              <InputLabel for="national_id" value="เลขบัตรประชาชน" />
              <TextInput
                id="national_id"
                v-model="nationalIdFormatted"
                @input="autoFormatNationalId"
                class="block w-full mt-1"
                maxlength="17"
                inputmode="numeric"
                placeholder="x-xxxx-xxxxx-xx-x"
              />
              <InputError :message="form.errors.national_id" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
              <InputLabel for="email" value="Email" />
              <TextInput id="email" v-model="form.email" type="email" class="block w-full mt-1" />
              <InputError :message="form.errors.email" />
            </div>

            <div>
              <InputLabel for="birthday" value="วันเกิด" />
              <TextInput id="birthday" v-model="form.birthday" type="date" class="block w-full mt-1" />
              <InputError :message="form.errors.birthday" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
              <InputLabel for="gender" value="เพศ" />
              <select
                id="gender"
                v-model="form.gender"
                class="w-full p-2 mt-1 border rounded dark:bg-gray-700 dark:text-white"
              >
                <option value="">-- เลือกเพศ --</option>
                <option value="male">ชาย</option>
                <option value="female">หญิง</option>
              </select>
              <InputError :message="form.errors.gender" />
            </div>

            <div>
              <InputLabel for="room_id" value="ห้องเรียน (ปัจจุบัน)" />
              <input
                id="room_id"
                type="text"
                :value="displayCurrentRoom"
                disabled
                readonly
                class="w-full p-2 mt-1 bg-gray-100 border rounded cursor-not-allowed dark:bg-gray-700 dark:text-white"
              />
            </div>
          </div>
        </section>

        <!-- 👨‍👩‍👧 Section ผู้ปกครอง -->
        <section class="pt-6 mt-6 border-t dark:border-gray-700">
          <h3 class="mb-4 text-lg font-bold text-gray-700 dark:text-white">ข้อมูลผู้ปกครอง</h3>

          <div class="grid grid-cols-3 gap-4">
            <div>
              <InputLabel for="guardian_prefix_th" value="คำนำหน้า" />
              <select
                id="guardian_prefix_th"
                v-model="form.guardian_prefix_th"
                class="w-full p-2 mt-1 border rounded dark:bg-gray-700 dark:text-white"
              >
                <option value="">-- เลือก --</option>
                <option value="นาย">นาย</option>
                <option value="นาง">นาง</option>
                <option value="นางสาว">นางสาว</option>
              </select>
              <InputError :message="form.errors.guardian_prefix_th" />
            </div>

            <div>
              <InputLabel for="guardian_name_th" value="ชื่อ" />
              <TextInput id="guardian_name_th" v-model="form.guardian_name_th" class="block w-full mt-1" />
              <InputError :message="form.errors.guardian_name_th" />
            </div>

            <div>
              <InputLabel for="guardian_surname_th" value="นามสกุล" />
              <TextInput id="guardian_surname_th" v-model="form.guardian_surname_th" class="block w-full mt-1" />
              <InputError :message="form.errors.guardian_surname_th" />
            </div>
          </div>

          <div class="grid grid-cols-3 gap-4 mt-4">
            <div>
              <InputLabel for="guardian_phone_1" value="เบอร์โทรศัพท์ 1" />
              <TextInput id="guardian_phone_1" v-model="form.guardian_phone_1" class="block w-full mt-1" />
              <InputError :message="form.errors.guardian_phone_1" />
            </div>

            <div>
              <InputLabel for="guardian_phone_2" value="เบอร์โทรศัพท์ 2" />
              <TextInput id="guardian_phone_2" v-model="form.guardian_phone_2" class="block w-full mt-1" />
              <InputError :message="form.errors.guardian_phone_2" />
            </div>

            <div>
              <InputLabel for="guardian_relationship" value="ความสัมพันธ์" />
              <TextInput
                id="guardian_relationship"
                v-model="form.guardian_relationship"
                placeholder="เช่น บิดา, มารดา"
                class="block w-full mt-1"
              />
              <InputError :message="form.errors.guardian_relationship" />
            </div>
          </div>
        </section>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-6 border-t dark:border-gray-700">
          <Link
            :href="route('students.index')"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-500 rounded-lg hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-500"
          >
            <ArrowLeftIcon class="w-5 h-5 mr-2" />
            ย้อนกลับ
          </Link>

          <PrimaryButton :disabled="form.processing">บันทึก</PrimaryButton>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
