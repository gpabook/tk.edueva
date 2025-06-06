<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  student: Object,
  rooms: Array,
});

const form = useForm({
  name_th: props.student.name_th || '',
  surname_th: props.student.surname_th || '',
  name_en: props.student.name_en || '',
  surname_en: props.student.surname_en || '',
  email: props.student.email || '',
  student_id: props.student.student_id || '',
  gender: props.student.gender || '',
  birthday: props.student.birthday || '',
  room_id: props.student.room_id || null,
});

const submit = () => {
  form.put(route('students.update', props.student.id));
};
</script>

<template>
  <Head title="Edit Student" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Edit Student</h2>
    </template>

    <div class="max-w-3xl py-12 mx-auto">
      <form @submit.prevent="submit" class="p-6 space-y-6 bg-white rounded shadow dark:bg-gray-800">
        <div class="grid grid-cols-2 gap-4">
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

          <div>
            <InputLabel for="name_en" value="ชื่อ (EN)" />
            <TextInput id="name_en" v-model="form.name_en" class="block w-full mt-1" />
          </div>

          <div>
            <InputLabel for="surname_en" value="นามสกุล (EN)" />
            <TextInput id="surname_en" v-model="form.surname_en" class="block w-full mt-1" />
          </div>
        </div>

        <div>
          <InputLabel for="student_id" value="รหัสนักเรียน" />
          <TextInput id="student_id" v-model="form.student_id" class="block w-full mt-1" />
          <InputError :message="form.errors.student_id" />
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <InputLabel for="email" value="Email" />
            <TextInput id="email" type="email" v-model="form.email" class="block w-full mt-1" />
          </div>

          <div>
            <InputLabel for="birthday" value="วันเกิด" />
            <TextInput id="birthday" type="date" v-model="form.birthday" class="block w-full mt-1" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <InputLabel for="gender" value="เพศ" />
            <select id="gender" v-model="form.gender" class="w-full p-2 border rounded">
              <option value="">-- เลือกเพศ --</option>
              <option value="M">ชาย</option>
              <option value="F">หญิง</option>
            </select>
          </div>

          <div>
            <InputLabel for="room_id" value="ห้องเรียน" />
            <select id="room_id" v-model="form.room_id" class="w-full p-2 border rounded">
              <option value="">-- เลือกห้อง --</option>
              <option v-for="room in props.rooms" :key="room.id" :value="room.id">
                {{ room.class_level?.name_th }} - {{ room.name_room_th }}
              </option>
            </select>
          </div>
        </div>

        <PrimaryButton :disabled="form.processing">บันทึก</PrimaryButton>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
