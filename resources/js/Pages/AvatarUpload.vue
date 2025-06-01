<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref } from 'vue'

defineOptions({ layout: AuthenticatedLayout })

const user = usePage().props.user

const form = useForm({
  avatar: null,
})

const preview = ref(user.avatar_url || null)

function onFileChange(e) {
  const file = e.target.files[0]
  if (file) {
    form.avatar = file
    preview.value = URL.createObjectURL(file)
  }
}

function submit() {
  form.post(route('avatar.store'), {
    onFinish: () => {
      // เคลียร์ preview ถ้าต้องการ
      preview.value = null
    },
  })
}
</script>

<template>
  <div class="py-6">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
          <h1 class="text-xl font-bold text-gray-800 dark:text-white">
            {{ $t('upload_avatar') || 'อัปโหลดรูปโปรไฟล์' }}
          </h1>
        </div>

        <div class="p-6">
          <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-6">
            <div>
              <label for="avatar" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                {{ $t('select_image') || 'เลือกรูปภาพ' }}
              </label>
              <input
                id="avatar"
                type="file"
                @change="onFileChange"
                accept="image/*"
                class="block w-full text-sm text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm cursor-pointer dark:text-gray-200 dark:bg-gray-700 dark:border-gray-600"
              />
            </div>

            <div v-if="preview" class="flex items-center space-x-4">
              <div class="text-gray-700 dark:text-gray-200">
                {{ $t('preview') || 'ตัวอย่าง:' }}
              </div>
              <img :src="preview" class="object-cover w-24 h-24 border border-gray-300 rounded-full dark:border-gray-600" />
            </div>

            <div>
              <button
                type="submit"
                class="px-5 py-2 text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800"
                :disabled="form.processing"
              >
                {{ form.processing ? ($t('uploading') || 'กำลังอัปโหลด...') : ($t('upload') || 'อัปโหลด') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
