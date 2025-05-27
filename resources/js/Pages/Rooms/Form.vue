<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // Or your layout

const props = defineProps({
  mode: String,
  room: {
    type: Object,
    default: () => ({}),
  },
  classLevels: Array,
  errors: Object,
  potentialAdvisors: Array, // รายชื่อครูที่สามารถเป็นที่ปรึกษาได้
  currentAdvisorIds: {      // ID ของครูที่ปรึกษาปัจจุบัน (สำหรับ mode 'edit')
    type: Array,
    default: () => [],
  }
});

const form = useForm({
  _method: props.mode === 'edit' ? 'PUT' : 'POST', // สำหรับ Laravel เมื่อใช้ POST สำหรับ update
  class_level_id: props.room?.class_level_id || (props.classLevels.length > 0 ? props.classLevels[0].id : ''),
  name_room_en: props.room?.name_room_en || '',
  name_room_th: props.room?.name_room_th || '',
  advisor_ids: props.currentAdvisorIds || [], // initial selected advisors
});

const pageTitle = props.mode === 'create' ? 'Create New Room' : 'Edit Room';
const submitButtonText = props.mode === 'create' ? 'Create Room' : 'Update Room';

const submitForm = () => {
  const options = {
    preserveScroll: true,
    onSuccess: () => {
      if (props.mode === 'create') form.reset('name_room_en', 'name_room_th', 'advisor_ids');
    },
  };

  if (props.mode === 'create') {
    form.post(route('rooms.store'), options);
  } else {
    // Inertia's PUT/PATCH doesn't natively send array of IDs well with PHP's default request parsing for PUT.
    // A common workaround is to use `router.post` with `_method: 'PUT'` or ensure your middleware handles it.
    // The useForm helper with `_method` usually handles this.
    form.post(route('rooms.update', props.room.id), options); // useForm handles the _method: 'PUT'
  }
};
</script>

<template>
  <Head :title="pageTitle" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ pageTitle }}</h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="submitForm">
              <div class="mb-4">
                <label for="class_level_id" class="block text-sm font-medium text-gray-700">Class Level</label>
                <select
                  id="class_level_id"
                  v-model="form.class_level_id"
                  class="block w-full py-2 pl-3 pr-10 mt-1 text-base border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                  <option v-if="classLevels.length === 0" disabled value="">No class levels available</option>
                  <option v-for="level in classLevels" :key="level.id" :value="level.id">
                    {{ level.name_en }} ({{ level.code }})
                  </option>
                </select>
                <div v-if="errors.class_level_id" class="mt-1 text-xs text-red-500">{{ errors.class_level_id }}</div>
              </div>

              <div class="mb-4">
                <label for="name_room_en" class="block text-sm font-medium text-gray-700">Room Name (English)</label>
                <input
                  type="text"
                  id="name_room_en"
                  v-model="form.name_room_en"
                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="e.g., A, B, 1, 2"
                />
                <div v-if="errors.name_room_en" class="mt-1 text-xs text-red-500">{{ errors.name_room_en }}</div>
              </div>

              <div class="mb-4">
                <label for="name_room_th" class="block text-sm font-medium text-gray-700">Room Name (Thai)</label>
                <input
                  type="text"
                  id="name_room_th"
                  v-model="form.name_room_th"
                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                   placeholder="e.g., เอ, บี, 1, 2"
                />
                <div v-if="errors.name_room_th" class="mt-1 text-xs text-red-500">{{ errors.name_room_th }}</div>
              </div>

              <div class="mb-4">
                <label for="advisor_ids" class="block text-sm font-medium text-gray-700">Advisors</label>
                <select
                  multiple
                  id="advisor_ids"
                  v-model="form.advisor_ids"
                  class="block w-full h-32 py-2 pl-3 pr-10 mt-1 text-base border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                  <option v-for="advisor in potentialAdvisors" :key="advisor.id" :value="advisor.id">
                    {{ advisor.name }}
                  </option>
                </select>
                <p class="mt-2 text-sm text-gray-500">Hold down the Ctrl (windows) / Command (Mac) button to select multiple options.</p>
                <div v-if="errors.advisor_ids" class="mt-1 text-xs text-red-500">{{ errors.advisor_ids }}</div>
                <div v-if="errors['advisor_ids.*']" class="mt-1 text-xs text-red-500">{{ errors['advisor_ids.*'] }}</div>
              </div>


              <div class="flex items-center justify-end mt-6">
                <Link :href="route('rooms.index')" class="mr-4 text-gray-600 hover:text-gray-900">
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                  :class="{ 'opacity-50': form.processing }"
                >
                  {{ submitButtonText }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
