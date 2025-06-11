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
<div class="pt-4 mt-6 border-b border-gray-200 dark:border-gray-700">
  <div class="px-3">
    <div class="relative flex flex-col items-center group">
      <!-- Avatar (always shown) -->
      <Link
        v-if="can('update avatar')"
        :href="route('avatar.update')"
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
        <p class="text-xs text-red-500 truncate dark:text-green-400">
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
            {{ t('change_password') }}
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
            <span>{{ t('appName') }}</span>
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

<!-- 📌 Sidebar Profile Dropdown Menu -->
<div class="px-2 py-2 border-gray-200 dark:border-gray-700">
  <button
    @click="toggleDropdown('profile')"
    class="flex items-center w-full p-2 text-left rounded hover:bg-gray-200 dark:hover:bg-gray-700"
  >
    <span class="mr-3 material-icons">person</span>
    <span v-if="!isSidebarCollapsed">{{ $t('menu.profile') }}</span>
    <svg
      v-if="!isSidebarCollapsed"
      :class="dropdowns.profile ? 'rotate-90' : ''"
      class="w-4 h-4 ml-auto transition-transform"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
    </svg>
  </button>

  <ul v-show="dropdowns.profile" class="pl-10 mt-1 space-y-1" v-if="!isSidebarCollapsed">
    <li>
      <Link
        :href="route('profile.edit')"
        class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300"
      >
      <span class="text-base text-gray-500">></span>
        {{ $t('edit_profile') }}
      </Link>
    </li>
    <li>
      <form method="POST" :action="route('logout')" @submit.prevent="$event.target.submit()">
        <input type="hidden" name="_token" :value="csrfToken" />
        <button
          type="submit"
          class="block py-1 text-sm text-left text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
        >
        <span class="text-base text-gray-500">></span>
          {{ $t('logout') }}
        </button>
      </form>
    </li>
  </ul>
</div>

        <!-- Sidebar Menu -->
        <nav class="flex-1 overflow-y-auto">
          <ul class="px-2 space-y-2">
            <li>
                <Link :href="route('dashboard')" class="flex items-center p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                <span class="mr-3 material-icons">dashboard</span>
                <span v-if="!isSidebarCollapsed">{{ t('menu.dashboard') }}</span>
                </Link>
            </li>

<!-- Dropdown setting-->
            <li>
              <button
                @click="toggleDropdown('settings')"
                class="flex items-center w-full p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
              >
                <span class="mr-3 material-icons">settings</span>
                <span v-if="!isSidebarCollapsed">{{ t('menu.settings') }}</span>
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
                <li><a href="#" class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
                    <span class="text-base text-gray-500">></span>
                    {{ t('menu.profile') }}</a></li>
                <li><a href="#" class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
                    <span class="text-base text-gray-500">></span>
                    {{ t('menu.account') }}</a></li>
              </ul>
            </li>
<!-- Dropdown: Manage Users จัดการผู้ใช้ -->
<li>
  <button
    @click="toggleDropdown('userManagement')"
    class="flex items-center w-full p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
  >
    <span class="mr-3 material-icons">manage_accounts</span>
    <span v-if="!isSidebarCollapsed">{{ $t('menu.user_management') || 'จัดการผู้ใช้' }}</span>
    <svg
      v-if="!isSidebarCollapsed"
      :class="dropdowns.userManagement ? 'rotate-90' : ''"
      class="w-4 h-4 ml-auto transition-transform"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
    </svg>
  </button>

  <ul v-show="dropdowns.userManagement" class="pl-10 mt-1 space-y-1" v-if="!isSidebarCollapsed">
    <li v-if="route().has('users.index')">
      <Link :href="route('users.index')" class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
        <span class="text-base text-gray-500">></span>
        {{ $t('menu.user_management') || 'จัดการผู้ใช้'}}
      </Link>
    </li>
    <li v-if="route().has('roles.index')">
      <Link :href="route('roles.index')" class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
        <span class="text-base text-gray-500">></span>
        {{ $t('roles') }}
      </Link>
    </li>
    <li v-if="route().has('permissions.index')">
      <Link :href="route('permissions.index')" class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
        <span class="text-base text-gray-500">></span>
        {{ $t('permissions') }}
      </Link>
    </li>
  </ul>
</li>

<!-- Dropdown: Manage Classroom -->
<li>
  <button
    @click="toggleDropdown('classroom')"
    class="flex items-center w-full p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
  >
    <span class="mr-3 material-icons">school</span>
    <span v-if="!isSidebarCollapsed">{{ t('menu.classroom') }}</span>
    <svg
      v-if="!isSidebarCollapsed"
      :class="dropdowns.classroom ? 'rotate-90' : ''"
      class="w-4 h-4 ml-auto transition-transform"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
    </svg>
  </button>

  <ul v-show="dropdowns.classroom" class="pl-10 mt-1 space-y-1" v-if="!isSidebarCollapsed">
    <li>
      <Link
      v-if="can('manage classes')"
      :href="route('class-levels.index')" class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
      <span class="text-base text-gray-500">></span>
      {{ t('menu.class_levels') }}
      </Link>
    </li>
    <li>
      <Link :href="route('rooms.index')" class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
        <span class="text-base text-gray-500">></span>
        {{ t('menu.rooms') }}
      </Link>
    </li>

    <li v-if="route().has('assign-room.index')">
  <Link :href="route('assign-room.index')" class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
    <span class="text-base text-gray-500">></span>
    {{ $t('menu.assign_room') }}
  </Link>
</li>

    <li>
      <Link :href="'#'" class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
        <span class="text-base text-gray-500">></span>
        {{ t('menu.subjects') }}
      </Link>
    </li>
  </ul>
</li>

<!-- Dropdown: Manage Student -->
<li>
  <button
    @click="toggleDropdown('manageStudent')"
    class="flex items-center w-full p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
  >
    <span class="mr-3 material-icons">people</span>
    <span v-if="!isSidebarCollapsed">{{ $t('menu.manage_student') }}</span>
    <svg
      v-if="!isSidebarCollapsed"
      :class="dropdowns.manageStudent ? 'rotate-90' : ''"
      class="w-4 h-4 ml-auto transition-transform"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
    </svg>
  </button>

  <ul v-show="dropdowns.manageStudent" class="pl-10 mt-1 space-y-1" v-if="!isSidebarCollapsed">
    <li v-if="route().has('students.index')">
      <Link :href="route('students.index')" class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
        <span class="mr-2 text-xs align-middle material-icons">list</span>
        {{ $t('menu.all_students') }}
      </Link>
    </li>
    <li v-if="route().has('students.create')">
      <Link :href="route('students.create')" class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
        <span class="mr-2 text-xs align-middle material-icons">person_add</span>
        {{ $t('menu.add_student') }}
      </Link>
    </li>
    <li v-if="route().has('students.bulk_delete')">
      <Link :href="route('students.bulk_delete')" class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
        <span class="mr-2 text-xs align-middle material-icons">delete</span>
        {{ $t('menu.delete_students') }}
      </Link>
    </li>
  </ul>
</li>

<!-- Dropdown: School Bank -->
<li>
  <button
    @click="toggleDropdown('schoolBank')"
    class="flex items-center w-full p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
  >
    <span class="mr-3 material-icons">account_balance</span>
    <span v-if="!isSidebarCollapsed">{{ t('menu.schoolBank') }}</span>
    <svg
      v-if="!isSidebarCollapsed"
      :class="dropdowns.schoolBank ? 'rotate-90' : ''"
      class="w-4 h-4 ml-auto transition-transform"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
    </svg>
  </button>

  <ul v-show="dropdowns.schoolBank" class="pl-10 mt-1 space-y-1" v-if="!isSidebarCollapsed">
    <li>
      <Link
      v-if="can('view banks')"
      :href="route('bank.myaccount', $page.props.auth.user.student_id)" class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
      <span class="text-base text-gray-500">></span>
      {{ t('menu.myAccount') }}
      </Link>
    </li>
    <li>
      <Link
      v-if="can('read banks')"
      :href="route('bank.user')" class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
      <span class="text-base text-gray-500">></span>
      {{ t('menu.usersAccount') }}
      </Link>
    </li>
    <li>
      <Link
       v-if="route().has('bank.calculate-interest') && can('read banks')"
      :href="route('bank.calculate-interest')" class="block py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
      <span class="text-base text-gray-500">></span>
      {{ t('menu.calculateInterest') }}
      </Link>
    </li>
  </ul>
</li>

<!-- Dropdown: Import-Export -->
<li>
  <button
    @click="toggleDropdown('importExport')"
    class="flex items-center w-full p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700"
  >
    <span class="mr-3 material-icons">import_export</span>
    <span v-if="!isSidebarCollapsed">{{ $t('menu.import_export') }}</span>
    <svg
      v-if="!isSidebarCollapsed"
      :class="dropdowns.importExport ? 'rotate-90' : ''"
      class="w-4 h-4 ml-auto transition-transform"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
    </svg>
  </button>

  <ul v-show="dropdowns.importExport" class="pl-10 mt-1 space-y-1" v-if="!isSidebarCollapsed">
    <li v-if="route().has('users.import.create')">
  <Link :href="route('users.import.create')" class="flex items-center gap-1 py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
    <span class="text-base text-gray-500">></span>
    {{ $t('menu.import_users') }}
  </Link>
</li>

<li v-if="route().has('users.export.excel')">
  <a :href="route('users.export.excel')" class="flex items-center gap-1 py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
    <span class="text-base text-gray-500">></span>
    {{ $t('menu.export_users') }}
  </a>
</li>

<li v-if="route().has('users.export.word')">
  <a :href="route('users.export.word')" class="flex items-center gap-1 py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
    <span class="text-base text-gray-500">></span>
    {{ $t('menu.export_users_word') }}
  </a>
</li>

<li v-if="route().has('import.subjects')">
  <Link :href="route('import.subjects')" class="flex items-center gap-1 py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
    <span class="text-base text-gray-500">></span>
    {{ $t('menu.import_subjects') }}
  </Link>
</li>

<li v-if="route().has('import.bank_accounts')">
  <Link :href="route('import.bank_accounts')" class="flex items-center gap-1 py-1 text-sm hover:text-gray-800 dark:hover:text-gray-300">
    <span class="text-base text-gray-500">></span>
    {{ $t('menu.import_bank_accounts') }}
  </Link>
</li>

  </ul>
</li>


            <li>
              <a href="#" class="flex items-center p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
                <span class="mr-3 material-icons">info</span>
                <span v-if="!isSidebarCollapsed">{{ t('menu.about') }}</span>
              </a>
            </li>
          </ul>
        </nav>

        <!-- Language & Theme Switch -->
        <div class="flex flex-col p-4 mt-auto space-y-3" v-if="!isSidebarCollapsed">
          <!-- Language Switcher -->
          <div class="flex items-center justify-between">
        <!----<label for="language" class="text-sm">{{ t('language') }}</label>---->
            <select
              id="language"
              v-model="currentLocale"
              @change="changeLanguage"
              class="p-1 text-sm bg-gray-100 rounded dark:bg-gray-700"
            >
              <option value="th">ไทย</option>
              <option value="en">Eng</option>
            </select>
          </div>

          <!-- Theme Switcher -->
          <button @click="toggleDarkMode" class="flex items-center space-x-2 text-sm hover:text-blue-500">
            <span class="material-icons">
              {{ isDark ? 'light_mode' : 'dark_mode' }}
            </span>
            <span>
              {{ isDark ? t('light') : t('dark') }}
            </span>
          </button>
        </div>


      </aside>
    <!-- Main Content with footer -->
    <div class="flex flex-col flex-1">
    <!-- ✅ Header slot support -->
  <header v-if="$slots.header" class="px-6 py-4 bg-white shadow dark:bg-gray-800">
    <slot name="header" />
  </header>
      <!-- Main Content slot  -->
      <main class="flex-1 p-6 overflow-auto bg-gray-50 dark:bg-gray-900">
        <slot />
      </main>
      <!-- ✅ Footer -->
      <footer
  class="px-6 py-4 text-sm text-gray-600 bg-white border-t border-gray-200 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700"
>
  <div class="flex flex-col items-center justify-between gap-2 sm:flex-row">
    <span>&copy; {{ new Date().getFullYear() }} {{ t('appName') }}. All rights reserved.</span>

    <div class="flex flex-col items-center gap-2 sm:flex-row sm:gap-4">
      <span class="text-xs text-gray-500 sm:text-sm dark:text-gray-400">
        v{{ appVersion }}
      </span>
      <div class="flex gap-3">
        <a href="#" class="hover:underline">{{ t('footer.privacy') }}</a>
        <a href="#" class="hover:underline">{{ t('footer.terms') }}</a>
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


const { locale, t } = useI18n()
const page = usePage()
const appVersion = import.meta.env.VITE_APP_VERSION || 'dev'
const can = (permissionName) => {
    return page.props.auth.user && page.props.auth.user.permissions ? page.props.auth.user.permissions.includes(permissionName) : false;
};

const userProfilePic = computed(() =>
  page.props.auth?.user?.avatar_url ?? '/images/default-avatar.png'
)
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';



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
const dropdowns = reactive({
    settings: false, // drop down menu Settings
    classroom: false,  // drop down menu ClassRoom
    schoolBank: false,  // drop down menu SchoolBank
    profile: false,
    userManagement: false,
})
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
