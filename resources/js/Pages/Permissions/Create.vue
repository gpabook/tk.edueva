<!-- resources/js/Pages/Permissions/Create.vue -->
<template>
    <div class="max-w-md p-4 mx-auto">
      <h1 class="mb-4 text-xl font-bold">Create Permission</h1>

      <!-- ตรวจสอบสิทธิ์ก่อนแสดงฟอร์ม -->
      <div v-if="can('create permissions')">
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

          <button type="submit" class="w-full btn">
            Create
          </button>
        </form>
      </div>
      <div v-else>
        <p class="text-red-600">You do not have permission to create permissions.</p>
      </div>
    </div>
  </template>

  <script setup>
  import { useForm, usePage } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  defineOptions({ layout: AuthenticatedLayout});

  // ดึง auth จาก Inertia props
  const { auth } = usePage().props;

  // helper ตรวจสิทธิ์
  const can = (permission) => {
    return Array.isArray(auth.user.permissions) && auth.user.permissions.includes(permission);
  };

  const form = useForm({
    name: '',
  });

  function submit() {
    if (!can('create permissions')) {
      return;
    }
    form.post(route('permissions.store'), {
      onSuccess: () => {
        // optional: สามารถ redirect หรือแสดง toast
      },
    });
  }
  </script>

  <style scoped>
  /* ปรับสไตล์เพิ่มเติมตามต้องการ */
  </style>
