<template>
    <Head :title="pageTitle" />

    <AuthenticatedLayout>
      <template #header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
          {{ pageTitle }}
        </h2>
      </template>

      <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
          <div class="p-6 overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
            <div class="mb-6">
              <Link
                :href="route('class-levels.index')"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <ArrowLeftIcon class="w-5 h-5 mr-2 -ml-1" />
                Back to Class Levels
              </Link>
            </div>

            <form @submit.prevent="submitForm">
              <div class="mb-4">
                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Code <span class="text-red-500">*</span></label>
                <input
                  type="text"
                  v-model="form.code"
                  id="code"
                  name="code"
                  maxlength="5"
                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  :class="{ 'border-red-500 dark:border-red-400': form.errors.code }"
                  required
                />
                <p v-if="form.errors.code" class="mt-1 text-xs text-red-500 dark:text-red-400">{{ form.errors.code }}</p>
              </div>

              <div class="mb-4">
                <label for="name_en" class="block text-sm font-medium text-gray-700 dark:text-gray-300">English Name <span class="text-red-500">*</span></label>
                <input
                  type="text"
                  v-model="form.name_en"
                  id="name_en"
                  name="name_en"
                  maxlength="50"
                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  :class="{ 'border-red-500 dark:border-red-400': form.errors.name_en }"
                  required
                />
                <p v-if="form.errors.name_en" class="mt-1 text-xs text-red-500 dark:text-red-400">{{ form.errors.name_en }}</p>
              </div>

              <div class="mb-6">
                <label for="name_th" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Thai Name <span class="text-red-500">*</span></label>
                <input
                  type="text"
                  v-model="form.name_th"
                  id="name_th"
                  name="name_th"
                  maxlength="50"
                  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  :class="{ 'border-red-500 dark:border-red-400': form.errors.name_th }"
                  required
                />
                <p v-if="form.errors.name_th" class="mt-1 text-xs text-red-500 dark:text-red-400">{{ form.errors.name_th }}</p>
              </div>

              <div class="flex items-center justify-end pt-4 mt-6 border-t border-gray-200 dark:border-gray-700">
                <button
                  type="submit"
                  class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                  :disabled="form.processing"
                >
                  <span v-if="form.processing">Saving...</span>
                  <span v-else>{{ isEditMode ? 'Update Class Level' : 'Create Class Level' }}</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>

  <script setup>
  import { computed } from 'vue';
  import { Head, Link, useForm } from '@inertiajs/vue3';
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'; // Adjust path if needed
  import { ArrowLeftIcon } from '@heroicons/vue/24/outline'; // Or your preferred icon library

  // Define props passed from the controller
  const props = defineProps({
    mode: {
      type: String,
      required: true, // 'create' or 'edit'
    },
    classLevel: {
      type: Object,
      default: () => ({}), // Default to empty object for create mode
    },
  });

  // Determine if we are in edit mode
  const isEditMode = computed(() => props.mode === 'edit');

  // Page title based on mode
  const pageTitle = computed(() => (isEditMode.value ? 'Edit Class Level' : 'Create Class Level'));

  // Initialize the form with useForm
  const form = useForm({
    code: isEditMode.value ? props.classLevel.code : '',
    name_en: isEditMode.value ? props.classLevel.name_en : '',
    name_th: isEditMode.value ? props.classLevel.name_th : '',
  });

  // Handle form submission
  const submitForm = () => {
    if (isEditMode.value) {
      form.put(window.route('class-levels.update', props.classLevel.id), {
        preserveScroll: true,
        // onSuccess: () => { form.reset(); // Or handle success with toast },
        // onError: () => { // Handle specific errors with toast if needed }
      });
    } else {
      form.post(window.route('class-levels.store'), {
        preserveScroll: true,
        // onSuccess: () => { form.reset(); },
      });
    }
  };
  </script>
