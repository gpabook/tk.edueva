<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  classLevels: Array,
  rooms: Array,
})

const form = useForm({
  student_id: '',
  name_th: '',
  surname_th: '',
  name_en: '',
  surname_en: '',
  gender: '',
  birthday: '',
  email: '',
  phone: '',
  room_id: '',
})

const submit = () => {
  form.post(route('students.store'), {
    onSuccess: () => form.reset(),
  })
}
</script>

<template>
  <Head title="เพิ่มนักเรียน" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">เพิ่มนักเรียน</h2>
    </template>

    <div class="max-w-4xl px-4 py-8 mx-auto">
      <form @submit.prevent="submit" class="space-y-6">
        <!-- ชื่อภาษาไทย -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <InputLabel for="name_th" value="ชื่อ (ภาษาไทย)" />
            <TextInput id="name_th" v-model="form.name_th" type="text" class="w-full" required />
            <InputError :message="form.errors.name_th" class="mt-1" />
          </div>
          <div>
            <InputLabel for="surname_th" value="นามสกุล (ภาษาไทย)" />
            <TextInput id="surname_th" v-model="form.surname_th" type="text" class="w-full" required />
            <InputError :message="form.errors.surname_th" class="mt-1" />
          </div>
        </div>

        <!-- ชื่อภาษาอังกฤษ -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <InputLabel for="name_en" value="ชื่อ (ภาษาอังกฤษ)" />
            <TextInput id="name_en" v-model="form.name_en" type="text" class="w-full" />
            <InputError :message="form.errors.name_en" class="mt-1" />
          </div>
          <div>
            <InputLabel for="surname_en" value="นามสกุล (ภาษาอังกฤษ)" />
            <TextInput id="surname_en" v-model="form.surname_en" type="text" class="w-full" />
            <InputError :message="form.errors.surname_en" class="mt-1" />
          </div>
        </div>

        <!-- student_id / email -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <InputLabel for="student_id" value="รหัสนักเรียน" />
            <TextInput id="student_id" v-model="form.student_id" type="text" class="w-full" />
            <InputError :message="form.errors.student_id" class="mt-1" />
          </div>
          <div>
            <InputLabel for="email" value="อีเมล" />
            <TextInput id="email" v-model="form.email" type="email" class="w-full" />
            <InputError :message="form.errors.email" class="mt-1" />
          </div>
        </div>

        <!-- เบอร์โทร / เพศ -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <InputLabel for="phone" value="เบอร์โทร" />
            <TextInput id="phone" v-model="form.phone" type="text" class="w-full" />
            <InputError :message="form.errors.phone" class="mt-1" />
          </div>
          <div>
            <InputLabel for="gender" value="เพศ" />
            <select v-model="form.gender" id="gender"
              class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:text-white dark:border-gray-600">
              <option value="">เลือกเพศ</option>
              <option value="male">ชาย</option>
              <option value="female">หญิง</option>
              <option value="other">อื่น ๆ</option>
            </select>
            <InputError :message="form.errors.gender" class="mt-1" />
          </div>
        </div>

        <!-- วันเกิด -->
        <div>
          <InputLabel for="birthday" value="วันเกิด" />
          <TextInput id="birthday" v-model="form.birthday" type="date" class="w-full" />
          <InputError :message="form.errors.birthday" class="mt-1" />
        </div>

        <!-- ห้องเรียน -->
        <div>
          <InputLabel for="room_id" value="ห้องเรียน" />
          <select v-model="form.room_id" id="room_id"
            class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:text-white dark:border-gray-600">
            <option value="">เลือกห้องเรียน</option>
            <option
              v-for="room in props.rooms"
              :key="room.id"
              :value="room.id"
            >
              {{ room.class_level?.name_th }} - {{ room.name_room_th }}
            </option>
          </select>
          <InputError :message="form.errors.room_id" class="mt-1" />
        </div>

        <!-- ปุ่ม -->
        <div class="flex justify-start gap-4">
          <PrimaryButton :disabled="form.processing">บันทึก</PrimaryButton>
          <Link
            :href="route('students.index')"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md shadow-sm hover:bg-gray-200 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500"
          >
            ยกเลิก
          </Link>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
