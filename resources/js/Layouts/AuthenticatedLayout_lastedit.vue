<template>
    <div class="flex h-screen text-gray-800 bg-gray-100 dark:bg-gray-900 dark:text-gray-100">
      <!-- Sidebar -->
      <aside
        :class="[
          'bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300',
          isSidebarCollapsed ? 'w-16' : 'w-64'
        ]"
        class="flex flex-col"
      >
      <!-- User Profile Summary (Sidebar Footer) -->
<!-- Sidebar User Profile Section -->
<div class="pt-4 mt-6 border-t border-gray-200 dark:border-gray-700">
  <div class="px-3">
    <div class="relative flex flex-col items-center group">
      <!-- Avatar (always shown) -->
      <Link
        :href="route('profile.edit')"
        class="block rounded-full hover:ring-2 hover:ring-blue-400"
        title="View Profile"
      >
        <img
          :src="userProfilePic"
          alt="User Avatar"
          class="object-cover w-12 border border-gray-300 rounded-full shadow aspect-square dark:border-gray-600"
        />
      </Link>

      <!-- Expanded view only -->
      <div v-if="isSidebarExpanded" class="w-full mt-2 text-center">
        <p class="text-sm font-semibold text-gray-800 truncate dark:text-gray-100">
          {{ page.props.auth.user?.name }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          {{ page.props.auth.user.roles[0] }}
        </p>
        <p class="text-xs text-gray-500 truncate dark:text-gray-400">
          {{ page.props.auth.user?.email }}
        </p>

        <!-- Dropdown menu -->
        <div class="mt-2 space-y-1">
          <Link
            v-if="route().has('password.change')"
            :href="route('password.change')"
            class="block text-xs text-blue-600 hover:underline dark:text-blue-400"
          >
            {{ $t('change_password') }}
          </Link>
        </div>
      </div>
    </div>
  </div>
</div>


        <!-- Sidebar Header -->
        <div class="flex items-center justify-between p-4">
          <span v-if="!isSidebarCollapsed" class="flex items-center space-x-2 text-lg font-semibold">
            <img src="/images/appschool_logo_o.svg" alt="Logo" class="w-6 h-6" />
            <span>{{ $t('appName') }}</span>
          </span>
          <button @click="toggleSidebar" class="text-gray-500 dark:text-gray-300 hover:text-gray-700">
            <svg
              v-if="isSidebarCollapsed"
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <svg
              v-else
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>

        <!-- Sidebar Menu -->
        <nav class="flex-1 overflow-y-auto">
          <ul class="px-2 space-y-2">
            <li>
              <a href="#" class="flex items-center p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                <span class="mr-3 material-icons">dashboard</span>
                <span v-if="!isSidebarCollapsed">{{ $t('menu.dashboard') }}</span>
              </a>
            </li>

            <!-- Dropdown -->
            <li>
              <button
                @click="toggleDropdown('settings')"
                class="flex items-center w-full p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
              >
                <span class="mr-3 material-icons">settings</span>
                <span v-if="!isSidebarCollapsed">{{ $t('menu.settings') }}</span>
                <svg
                  v-if="!isSidebarCollapsed"
                  :class="dropdowns.settings ? 'rotate-90' : ''"
                  class="w-4 h-4 ml-auto transition-transform"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
              <ul v-show="dropdowns.settings" class="pl-10 mt-1 space-y-1" v-if="!isSidebarCollapsed">
                <li><a href="#" class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">{{ $t('menu.profile') }}</a></li>
                <li><a href="#" class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">{{ $t('menu.account') }}</a></li>
              </ul>
            </li>

            <li>
              <a href="#" class="flex items-center p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                <span class="mr-3 material-icons">info</span>
                <span v-if="!isSidebarCollapsed">{{ $t('menu.about') }}</span>
              </a>
            </li>
          </ul>
        </nav>

        <!-- Language & Theme Switch -->
        <div class="flex flex-col p-4 mt-auto space-y-3" v-if="!isSidebarCollapsed">
          <!-- Language Switcher -->
          <div class="flex items-center justify-between">
            <label for="language" class="text-sm">{{ $t('language') }}</label>
            <select
              id="language"
              v-model="currentLocale"
              @change="changeLanguage"
              class="p-1 text-sm bg-gray-100 rounded dark:bg-gray-700"
            >
              <option value="th">ไทย</option>
              <option value="en">English</option>
            </select>
          </div>

          <!-- Theme Switcher -->
          <button @click="toggleDarkMode" class="flex items-center space-x-2 text-sm hover:text-blue-500">
            <span class="material-icons">
              {{ isDark ? 'light_mode' : 'dark_mode' }}
            </span>
            <span>
              {{ isDark ? $t('light') : $t('dark') }}
            </span>
          </button>
        </div>


      </aside>
    <!-- Main Content with footer -->
    <div class="flex flex-col flex-1">
      <!-- Main Content slot  -->
      <main class="flex-1 p-6 overflow-auto bg-gray-50 dark:bg-gray-900">
        <slot />
      </main>
      <!-- ✅ Footer -->
      <footer
  class="px-6 py-4 text-sm text-gray-600 bg-white border-t border-gray-200 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700"
>
  <div class="flex flex-col items-center justify-between gap-2 sm:flex-row">
    <span>&copy; {{ new Date().getFullYear() }} {{ $t('appName') }}. All rights reserved.</span>

    <div class="flex flex-col items-center gap-2 sm:flex-row sm:gap-4">
      <span class="text-xs text-gray-500 sm:text-sm dark:text-gray-400">
        v{{ appVersion }}
      </span>
      <div class="flex gap-3">
        <a href="#" class="hover:underline">{{ $t('footer.privacy') }}</a>
        <a href="#" class="hover:underline">{{ $t('footer.terms') }}</a>
      </div>
    </div>
  </div>
</footer>

    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { Link, usePage, router as inertiaRouter } from '@inertiajs/vue3'

const page = usePage()
const appVersion = import.meta.env.VITE_APP_VERSION || 'dev'

const userProfilePic = computed(() =>
  page.props.auth?.user?.avatar_url ?? '/images/default-avatar.png'
)

const { locale, t } = useI18n()

// Sidebar state
const isSidebarCollapsed = ref(false)
function toggleSidebar() {
  isSidebarCollapsed.value = !isSidebarCollapsed.value
}

const isDesktopSidebarCollapsed = ref(localStorage.getItem('desktopSidebarCollapsed') === 'true')
const isSidebarExpanded = computed(() => !isDesktopSidebarCollapsed.value)


const toggleDesktopSidebar = () => {
  isDesktopSidebarCollapsed.value = !isDesktopSidebarCollapsed.value
  localStorage.setItem('desktopSidebarCollapsed', isDesktopSidebarCollapsed.value)
}

// Dropdown state
const dropdowns = reactive({ settings: false })
function toggleDropdown(name) {
  dropdowns[name] = !dropdowns[name]
}

// Dark mode toggle
const isDark = ref(false)
function toggleDarkMode() {
  isDark.value = !isDark.value
  const html = document.documentElement
  if (isDark.value) {
    html.classList.add('dark')
    localStorage.setItem('theme', 'dark')
  } else {
    html.classList.remove('dark')
    localStorage.setItem('theme', 'light')
  }
}

// Language switch
const currentLocale = ref('th')
function changeLanguage() {
  locale.value = currentLocale.value
  localStorage.setItem('lang', currentLocale.value)
}

onMounted(() => {
  const theme = localStorage.getItem('theme')
  if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    isDark.value = true
    document.documentElement.classList.add('dark')
  }

  const savedLang = localStorage.getItem('lang') || 'th'
  currentLocale.value = savedLang
  locale.value = savedLang
})
</script>


  <style>
  @import url('https://fonts.googleapis.com/icon?family=Material+Icons');
  </style>
