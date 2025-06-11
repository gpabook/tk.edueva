<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref } from 'vue'

const { user, roles } = defineProps({
  user: Object,
  roles: Object,
})

const form = useForm({
  name_th: user.name_th ?? '',
  surname_th: user.surname_th ?? '',
  name_en: user.name_en ?? '',
  surname_en: user.surname_en ?? '',
  email: user.email ?? '',
  gender: user.gender ?? '',
  birthday: user.birthday ?? '',
  room_id: user.room_id ?? '',
  role: user.role,
  status: user.status ?? true,
  avatar: user.avatar ?? '',
  password: '',
  password_confirmation: '',
})

const showPassword = ref(false)
const showPasswordConfirm = ref(false)

const generatePassword = () => {
  const random = Math.random().toString(36).slice(-8)
  form.password = random
  form.password_confirmation = random
  alert(`Generated password: ${random}`)
}
</script>

<template>
  <Head :title="$t('edit')" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
          {{ $t('edit') }} - {{ user.name }}
        </h2>
        <Link
          :href="route('users.index')"
          class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-600 rounded hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600"
        >
          ← {{ $t('menu.user_management') }}
        </Link>
      </div>
    </template>

    <div class="max-w-3xl mx-auto mt-6 sm:px-6 lg:px-8">
      <div class="overflow-hidden bg-white shadow sm:rounded-lg dark:bg-gray-800">
        <div class="p-6 space-y-6">
          <form @submit.prevent="form.put(route('users.update', user.id))" class="space-y-4">
            <!-- Text Fields -->
            <div v-for="(label, field) in {
              name_th: $t('name_th'),
              surname_th: $t('surname_th'),
              name_en: $t('name_en'),
              surname_en: $t('surname_en'),
              email: $t('email_label')
            }" :key="field">
              <label :for="field" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ label }}
              </label>
              <input
                :id="field"
                v-model="form[field]"
                type="text"
                class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
              <div v-if="form.errors[field]" class="mt-1 text-sm text-red-600">
                {{ form.errors[field] }}
              </div>
            </div>

            <!-- Birthday -->
            <div>
              <label for="birthday" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ $t('birthday') }}
              </label>
              <input
                id="birthday"
                v-model="form.birthday"
                type="date"
                class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
              <div v-if="form.errors.birthday" class="mt-1 text-sm text-red-600">{{ form.errors.birthday }}</div>
            </div>

            <!-- Gender -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $t('gender') }}</label>
              <select
                v-model="form.gender"
                class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option value="male">{{ $t('gender_male') }}</option>
                <option value="female">{{ $t('gender_female') }}</option>
              </select>
              <div v-if="form.errors.gender" class="mt-1 text-sm text-red-600">{{ form.errors.gender }}</div>
            </div>

            <!-- Role -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $t('roles_label') }}</label>
              <select
                v-model="form.role"
                class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option v-for="(label, key) in roles" :key="key" :value="parseInt(key)">
                  {{ $t(`roles.${label}`) }}
                </option>
              </select>
              <div v-if="form.errors.role" class="mt-1 text-sm text-red-600">{{ form.errors.role }}</div>
            </div>

            <!-- Status -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $t('status') }}</label>
              <select
                v-model="form.status"
                class="block w-full mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              >
                <option :value="1">{{ $t('active') }}</option>
                <option :value="0">{{ $t('inactive') }}</option>
              </select>
            </div>

            <!-- Password -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $t('password') }}</label>
              <div class="relative">
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  class="block w-full pr-10 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute text-sm text-gray-500 top-2 right-2 dark:text-gray-300"
                >
                  {{ showPassword ? $t('hide') : $t('show') }}
                </button>
              </div>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $t('password_note') }}
              </p>

              <label class="block mt-4 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $t('confirm_password') }}</label>
              <div class="relative">
                <input
                  v-model="form.password_confirmation"
                  :type="showPasswordConfirm ? 'text' : 'password'"
                  class="block w-full pr-10 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
                <button
                  type="button"
                  @click="showPasswordConfirm = !showPasswordConfirm"
                  class="absolute text-sm text-gray-500 top-2 right-2 dark:text-gray-300"
                >
                  {{ showPasswordConfirm ? $t('hide') : $t('show') }}
                </button>
              </div>

              <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</div>
              <div v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ form.errors.password_confirmation }}</div>

              <button
                type="button"
                @click="generatePassword"
                class="inline-flex items-center px-3 py-1 mt-3 text-sm text-white bg-indigo-600 rounded hover:bg-indigo-700"
              >
                {{ $t('generate_password') }}
              </button>
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
                :disabled="form.processing"
              >
                {{ $t('save') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
