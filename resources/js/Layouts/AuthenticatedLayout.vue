<script setup>
import { ref, onMounted, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { initFlowbite } from 'flowbite';

// Import Heroicons for Theme Switcher and other existing icons
import {
    HomeIcon,
    UsersIcon,
    CreditCardIcon,
    Cog6ToothIcon,
    ShieldCheckIcon,
    UserCircleIcon,
    Bars3Icon,
    XMarkIcon,
    SunIcon,      // For Theme Switcher (Light Mode)
    MoonIcon      // For Theme Switcher (Dark Mode)
} from '@heroicons/vue/24/outline';

const page = usePage();

const userProfilePic = computed(() =>
  page.props.auth?.user?.avatar_url
  ?? '/images/default-avatar.png'
);

// --- Sidebar State ---
const isDesktopSidebarCollapsed = ref(localStorage.getItem('desktopSidebarCollapsed') === 'true');
const toggleDesktopSidebar = () => {
    isDesktopSidebarCollapsed.value = !isDesktopSidebarCollapsed.value;
    localStorage.setItem('desktopSidebarCollapsed', isDesktopSidebarCollapsed.value);
};

// --- Permission Helper ---
const can = (permissionName) => {
    const authUser = page.props.auth.user;
    return authUser && authUser.permissions ? authUser.permissions.includes(permissionName) : false;
};
const hasRole = (roleNameInput) => {
    const authUser = page.props.auth.user;
    if (!(authUser && authUser.roles)) return false;
    const rolesToCheck = Array.isArray(roleNameInput) ? roleNameInput : [roleNameInput];
    return rolesToCheck.some(role => authUser.roles.includes(role));
};

// --- Theme Switcher Logic ---
const isDarkMode = ref(false);

const applyTheme = (theme) => {
  if (theme === 'dark') {
    document.documentElement.classList.add('dark');
    isDarkMode.value = true;
  } else {
    document.documentElement.classList.remove('dark');
    isDarkMode.value = false;
  }
};

const toggleDarkMode = () => {
  const newTheme = !isDarkMode.value ? 'dark' : 'light';
  localStorage.setItem('theme', newTheme);
  applyTheme(newTheme);
};

onMounted(() => {
    initFlowbite(); // Initialize Flowbite components

    // Initialize Theme
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        applyTheme(savedTheme);
    } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        applyTheme('dark');
    } else {
        applyTheme('light'); // Default to light theme
    }
});

// --- Navigation Items ---
const sidebarNavItems = computed(() => {
    const currentAuthUser = page.props.auth.user;
    const P_AUTH_USER_ID = currentAuthUser?.id;

    const allPossibleItems = [
        { textKey: 'dashboard', iconComponent: HomeIcon, routeName: 'dashboard', permission: null },
        { textKey: 'users', iconComponent: UsersIcon, routeName: 'users.index', permission: 'view users' },
        (P_AUTH_USER_ID && route().has('bank.myaccount')
            ? { textKey: 'my_bank_account', iconComponent: CreditCardIcon, routeName: 'bank.myaccount', params: { user_id: P_AUTH_USER_ID }, permission: 'view banks' }
            : null),
        { textKey: 'settings', iconComponent: Cog6ToothIcon, routeName: 'profile.edit', permission: null },
        { textKey: 'permissions', iconComponent: ShieldCheckIcon, routeName: 'permissions.index', permission: 'read permissions' },
        { textKey: 'roles', iconComponent: UserCircleIcon, routeName: 'roles.index', permission: 'read roles' },
    ];

    return allPossibleItems
        .filter(item => !!item)
        .map(item => {
            let isActive = false;
            if (route().has(item.routeName)) {
                const routeParams = item.params || {};
                isActive = route().current(item.routeName, routeParams);
                if (!isActive && item.routeName.endsWith('.index')) {
                    isActive = route().current(item.routeName.split('.')[0] + '.*');
                }
            }
            return { ...item, active: isActive };
        })
        .filter(item => {
            if (!route().has(item.routeName)) return false;
            if (item.permission === null) return true;
            if (item.isRole) return hasRole(item.permission);
            return can(item.permission);
        });
});

const userDropdownItems = [
    { textKey: 'profile', routeName: 'profile.edit' },
    { textKey: 'logout', routeName: 'logout', method: 'post', as: 'button' },
];

const appLogoUrl = computed(() => page.props.appLogo || "/images/appschool_logo_o.svg");
const appName = computed(() => page.props.appName || 'AppSchool');

</script>

<template>
        <div class="min-h-screen bg-gray-100 dark:bg-gray-800">
        <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <div class="px-3 py-3 lg:px-5 lg:pl-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center justify-start rtl:justify-end">
                        <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                            <span class="sr-only">Open sidebar</span>
                            <Bars3Icon class="w-6 h-6" aria-hidden="true" />
                        </button>
                        <button @click="toggleDesktopSidebar" type="button" class="items-center hidden p-2 text-sm text-gray-500 rounded-lg sm:inline-flex hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600 ms-3">
                            <span class="sr-only">Toggle desktop sidebar</span>
                            <XMarkIcon v-if="!isDesktopSidebarCollapsed" class="w-6 h-6" aria-hidden="true" />
                            <Bars3Icon v-else class="w-6 h-6" aria-hidden="true" />
                        </button>
                        <Link :href="route('dashboard')" class="flex ms-2 md:me-24">
                            <img :src="appLogoUrl" class="h-8 me-3" alt="App Logo" />
                            <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">{{ appName }}</span>
                        </Link>
                    </div>
                    <div class="flex items-center">
                        <div class="me-3">
                            <LanguageSwitcher />
                        </div>

                        <button @click="toggleDarkMode" type="button" class="p-2 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                            <span class="sr-only">Toggle dark mode</span>
                            <MoonIcon v-if="!isDarkMode" class="w-5 h-5" aria-hidden="true" />
                            <SunIcon v-else class="w-5 h-5" aria-hidden="true" />
                        </button>

                        <div class="flex items-center ms-3">
                            <div>
                                <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                                    <span class="sr-only">Open user menu</span>
                                    <img class="w-8 h-8 rounded-full" :src="userProfilePic" alt="user photo">
                                </button>
                            </div>
                            <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600" id="dropdown-user">
                                <div class="px-4 py-3" role="none">
                                    <p class="text-sm text-gray-900 dark:text-white" role="none">
                                        {{ page.props.auth.user?.name }}
                                        <span v-if="page.props.auth.user?.roles && page.props.auth.user?.roles.length > 0">
                                            ({{ page.props.auth.user?.roles[0] }})
                                        </span>
                                    </p>
                                    <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                        {{ page.props.auth.user?.email }}
                                    </p>
                                </div>
                                <ul class="py-1" role="none">
                                    <li v-for="item in userDropdownItems" :key="item.routeName">
                                        <Link v-if="route().has(item.routeName)" :href="route(item.routeName, item.params || {})" :method="item.method" :as="item.as" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">
                                            {{ $t(item.textKey) }}
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <aside id="logo-sidebar"
            class="fixed top-0 left-0 z-40 h-screen pt-20 transition-transform duration-300 ease-in-out -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700 transition-width"
            :class="{ 'w-64': !isDesktopSidebarCollapsed, 'w-20': isDesktopSidebarCollapsed, 'translate-x-0': true }"
            aria-label="Sidebar">
           <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
              <ul class="space-y-2 font-medium">
                 <li v-for="item in sidebarNavItems" :key="item.routeName">
                    <Link :href="route(item.routeName, item.params || {})"
                       class="relative flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                       :class="{ 'bg-gray-100 dark:bg-gray-700 font-semibold': item.active }">
                       <component :is="item.iconComponent"
                            class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" />
                       <span class="transition-opacity duration-300 ms-3 whitespace-nowrap"
                             :class="{
                                'opacity-0 group-hover:opacity-100 absolute left-full ml-2 px-2 py-1 text-xs bg-gray-900 dark:bg-gray-700 text-white rounded-md shadow-lg z-10': isDesktopSidebarCollapsed,
                                'inline-block': !isDesktopSidebarCollapsed
                             }">{{ $t(item.textKey) }}</span>
                    </Link>
                 </li>
              </ul>
           </div>
        </aside>

        <div class="p-4 duration-300 ease-in-out sm:ml-64 transition-margin-left"
             :class="{ 'sm:ml-64': !isDesktopSidebarCollapsed, 'sm:ml-20': isDesktopSidebarCollapsed }">
           <div class="pt-14">
              <header class="py-6 bg-gray-100 dark:bg-gray-800" v-if="$slots.header">
                  <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                      <slot name="header" />
                  </div>
              </header>
              <main class="py-6 bg-gray-100 dark:bg-gray-900"> <slot />
              </main>
              <footer class="p-4 mt-auto bg-gray-100 border-t dark:bg-gray-800 dark:border-gray-700">
                <div class="text-sm text-center text-gray-500 dark:text-gray-400">
                    © {{ new Date().getFullYear() }} Prompt System Ltd. All Rights Reserved.
                </div>
              </footer>
           </div>
        </div>
    </div>
</template>

<style scoped>
/* Smooth transitions */
#logo-sidebar, .transition-margin-left {
    transition-property: width, margin-left;
    transition-timing-function: ease-in-out;
    transition-duration: 300ms;
}
/* Tooltip for collapsed sidebar items */
.group .absolute {
    visibility: hidden;
    opacity: 0;
    transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out;
    pointer-events: none;
}
.group:hover .absolute {
    visibility: visible;
    opacity: 1;
    pointer-events: auto;
}

#logo-sidebar .overflow-y-auto::-webkit-scrollbar { display: none; }
#logo-sidebar .overflow-y-auto { -ms-overflow-style: none; scrollbar-width: none; }
</style>
