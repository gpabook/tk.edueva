<script setup>
import { ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { Link } from '@inertiajs/vue3'

const { locale, t } = useI18n()

// Language switching
const currentLocale = ref(locale.value)
function changeLanguage() {
  locale.value = currentLocale.value
  localStorage.setItem('lang', currentLocale.value)
}

// Dark Mode toggle
const isDark = ref(false)
function toggleDarkMode() {
  isDark.value = !isDark.value
  const html = document.documentElement
  html.classList.toggle('dark', isDark.value)
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
}

onMounted(() => {
  // Detect saved theme or system preference
  const savedTheme = localStorage.getItem('theme')
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches

  isDark.value = savedTheme === 'dark' || (!savedTheme && prefersDark)
  document.documentElement.classList.toggle('dark', isDark.value)

  // Load saved language
  const savedLang = localStorage.getItem('lang') || 'th'
  currentLocale.value = savedLang
  locale.value = savedLang
})
</script>

<template>
  <div class="flex flex-col items-center justify-center min-h-screen text-gray-800 bg-gray-100 dark:bg-gray-900 dark:text-gray-100">

    <!-- Top Right Controls -->
    <div class="absolute top-0 right-0 flex items-center gap-4 p-4 sm:p-6">
      <!-- 🌐 Language -->
      <select
        v-model="currentLocale"
        @change="changeLanguage"
        class="p-1 text-sm bg-gray-100 border rounded dark:bg-gray-800 dark:text-white dark:border-gray-600"
      >
        <option value="th">ไทย</option>
        <option value="en">Eng</option>
      </select>

      <!-- 🌙 Theme toggle -->
      <button
        @click="toggleDarkMode"
        class="p-1 text-xl transition rounded-full hover:text-blue-600 dark:hover:text-yellow-300"
        :title="isDark ? t('light') : t('dark')"
      >
        <span class="material-icons">
          {{ isDark ? 'light_mode' : 'dark_mode' }}
        </span>
      </button>
    </div>

    <!-- Logo -->
    <div class="mb-6">
      <Link href="/">
        <img src="/images/appschool_logo.svg" alt="App Logo" class="w-20 h-20" />
      </Link>
    </div>

    <!-- Card -->
    <div class="w-full px-6 py-6 bg-white shadow sm:max-w-md sm:rounded-lg dark:bg-gray-800">
      <slot />
    </div>

    <!-- Footer -->
    <footer class="mt-6 text-xs text-gray-500 dark:text-gray-400">
      &copy; {{ new Date().getFullYear() }} {{ t('appName') }}
    </footer>
  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/icon?family=Material+Icons');
</style>
