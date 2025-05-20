<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref, watch } from 'vue'

defineOptions({ layout: AuthenticatedLayout });

const user = usePage().props.user

const form = useForm({
  avatar: null,
})

//const preview = ref(null)
const preview = ref(user.avatar_url)

function onFileChange(e) {
  form.avatar = e.target.files[0]
  preview.value = URL.createObjectURL(form.avatar)
}

function submit() {
  form.post(route('avatar.store'), {
    onFinish: () => {
      // ล้าง preview ถ้าต้องการ
      preview.value = null
    },
  })
}
</script>
<template>
    <div class="p-4">
      <h1 class="mb-4 space-x-8 text-xl text-gray-800 sm:-my-px">อัปโหลด Avatar</h1>

      <form @submit.prevent="submit">
        <input type="file" @change="onFileChange" accept="image/*" />

        <div v-if="preview" class="mb-4 space-x-8 text-xl text-gray-800 sm:-my-px">
          <p>Preview:</p>
          <img :src="preview" class="object-cover w-24 h-24 rounded-full" />
        </div>

        <button
          type="submit"
          class="px-4 py-2 mt-4 text-white bg-blue-500 rounded"
          :disabled="form.processing"
        >
          {{ form.processing ? 'กำลังอัปโหลด...' : 'อัปโหลด' }}
        </button>
      </form>
    </div>
  </template>

