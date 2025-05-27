<template>
    <div class="max-w-xl p-4 mx-auto">
      <h1 class="mb-4 text-2xl font-semibold">Create Class Level</h1>
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block mb-1 font-medium">Code</label>
          <input
            v-model="form.code"
            type="text"
            class="w-full p-2 border rounded"
            :class="{ 'border-red-500': form.errors.code }"
          />
          <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">
            {{ form.errors.code }}
          </p>
        </div>
        <div>
          <label class="block mb-1 font-medium">English Name</label>
          <input
            v-model="form.name_en"
            type="text"
            class="w-full p-2 border rounded"
            :class="{ 'border-red-500': form.errors.name_en }"
          />
          <p v-if="form.errors.name_en" class="mt-1 text-sm text-red-600">
            {{ form.errors.name_en }}
          </p>
        </div>
        <div>
          <label class="block mb-1 font-medium">Thai Name</label>
          <input
            v-model="form.name_th"
            type="text"
            class="w-full p-2 border rounded"
            :class="{ 'border-red-500': form.errors.name_th }"
          />
          <p v-if="form.errors.name_th" class="mt-1 text-sm text-red-600">
            {{ form.errors.name_th }}
          </p>
        </div>
        <div class="flex items-center space-x-2">
          <button
            type="submit"
            class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700"
            :disabled="form.processing"
          >
            Save
          </button>
          <Link
            :href="route('class-levels.index')"
            class="text-gray-600 hover:underline"
          >
            Cancel
          </Link>
        </div>
      </form>
    </div>
  </template>

  <script setup>
  import { useForm, Link, Head } from '@inertiajs/vue3'

  const form = useForm({
    code: '',
    name_en: '',
    name_th: '',
  })

  function submit() {
    form.post(route('class-levels.store'))
  }
  </script>
