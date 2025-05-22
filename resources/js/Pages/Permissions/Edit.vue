<!-- resources/js/Pages/Permissions/Edit.vue -->
<template>
    <div class="max-w-md p-4 mx-auto">
      <h1 class="mb-4 text-xl font-bold">Edit Permission</h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label for="name" class="block mb-1 font-medium">Name</label>
          <input
            v-model="form.name"
            id="name"
            type="text"
            class="w-full px-3 py-2 border rounded"
          />
          <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
            {{ form.errors.name }}
          </p>
        </div>

        <button
          v-if="can('edit permissions')"
          type="submit"
          class="w-full btn"
        >
          Update
        </button>
      </form>
    </div>
  </template>

  <script setup>
  import { useForm, usePage } from '@inertiajs/vue3'
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  defineOptions({ layout: AuthenticatedLayout});

  const { permission, auth } = defineProps({
    permission: Object,
    auth: Object,
  })

  const can = (permissionName) =>
    Array.isArray(auth.user.permissions) &&
    auth.user.permissions.includes(permissionName)

  const form = useForm({
    name: permission.name,
  })

  function submit() {
    if (!can('edit permissions')) return
    form.put(route('permissions.update', permission.id))
  }
  </script>
