<script setup>
import { ref, onMounted, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'; // For the mobile user dropdown
import { Link, usePage } from '@inertiajs/vue3';
import { initFlowbite } from 'flowbite'; // For Flowbite JS components
import { PlusIcon, BuildingOffice2Icon } from '@heroicons/vue/24/outline';
import navigation from '@/navigation' // @ alias → resources/js

const page = usePage();

const userProfilePic = computed(() =>
  page.props.auth?.user?.avatar_url
  ?? '/images/default-avatar.png'
)

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

onMounted(() => {
    initFlowbite();
});

// --- Navigation Items ---


const sidebarNavItems = computed(() => {
    const currentAuthUser = page.props.auth.user;
    const P_AUTH_USER_ID = currentAuthUser?.id;

    const allPossibleItems = [
        {
            textKey: 'dashboard', iconPath: 'M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z', routeName: 'dashboard', permission: null
        },
        // Make sure 'users.index' is a defined and named route in your Laravel routes
        // and you have run `php artisan ziggy:generate`
        { textKey: 'users', iconPath: 'M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z', routeName: 'users.index', permission: 'view users' /* Example permission */ },
        (P_AUTH_USER_ID && route().has('bank.myaccount') // Check if route exists before trying to define item
            ? { textKey: 'my_bank_account', iconPath: 'M4 4a2 2 0 00-2 2v4a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 6a2 2 0 00-2 2v4a2 2 0 002 2h12a2 2 0 002-2v-4a2 2 0 00-2-2H4z',
              routeName: 'bank.myaccount',
              params: { user_id: P_AUTH_USER_ID }, // Ensure param name matches Laravel route {user_id} or {user}
              permission: 'view banks' }
            : null),
        { textKey: 'settings', iconPath: 'M10.325 4.317a1.724 1.724 0 00-1.066-2.573 1.724 1.724 0 00-2.572 1.065 1.724 1.724 0 00-2.37-.94 1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31.826-2.37 2.37a1.724 1.724 0 00-1.065 2.572c-1.756.426-1.756 2.924 0 3.35a1.724 1.724 0 001.066 2.573c.94 1.543.826 3.31 2.37 2.37.996-.608 2.296-.07 2.572 1.065.426 1.756 2.924 1.756 3.35 0a1.724 1.724 0 002.573-1.066c1.543-.94 3.31.826 2.37-2.37a1.724 1.724 0 001.065-2.572c1.756-.426 1.756-2.924 0 3.35a1.724 1.724 0 00-1.066-2.573c-.94-1.543-.826-3.31-2.37-2.37a1.724 1.724 0 00-2.572-1.065zM10 13a3 3 0 100-6 3 3 0 000 6z', routeName: 'profile.edit', permission: null },
        // Add your other menu items from the original AuthenticatedLayout.vue here,
        // ensuring routeName exists and params are handled if needed. For example:
        { textKey: 'permissions', routeName: 'permissions.index', permission: 'read permissions', iconPath: 'M15 7a1 1 0 011-1h2a1 1 0 011 1v10a1 1 0 01-1 1h-2a1 1 0 01-1-1V7zM6 3a1 1 0 000 2h5a1 1 0 000-2H6zM2 7a1 1 0 011-1h2a1 1 0 011 1v10a1 1 0 01-1 1H3a1 1 0 01-1-1V7z' },
        { textKey: 'roles', routeName: 'roles.index', permission: 'read roles', iconPath: 'M17 20h5v-2h-5v2zm-2-4H5V4h10v12zm0-4H7v-2h8v2zm0-4H7V6h8v2z' },
        // ... and so on for class-levels, rooms, import, bulk-edit ...
    ];

    return allPossibleItems
        .filter(item => !!item) // Filter out any null items (like bank.myaccount if conditions weren't met)
        .map(item => {
            let isActive = false;
            // Only attempt to check active state if the route actually exists in Ziggy's list
            if (route().has(item.routeName)) {
                const routeParams = item.params || {};
                isActive = route().current(item.routeName, routeParams);
                // More specific active check for parent.index routes
                if (!isActive && item.routeName.endsWith('.index')) {
                    isActive = route().current(item.routeName.split('.')[0] + '.*');
                }
            } else {
                // console.warn(`Ziggy warning: Route '${item.routeName}' for active check not found.`);
            }
            return { ...item, active: isActive };
        })
        .filter(item => { // Then filter by permission/role
            if (!route().has(item.routeName)) return false; // Do not render if route doesn't exist
            if (item.permission === null) return true;
            if (item.isRole) return hasRole(item.permission); // Ensure hasRole handles array of roles
            return can(item.permission);
        });
});

const userDropdownItems = [
    { textKey: 'profile', routeName: 'profile.edit' },
    { textKey: 'logout', routeName: 'logout', method: 'post', as: 'button' },
];

const appLogoUrl = computed(() => page.props.appLogo || "/images/EDUEVA_logo.png"); // Get from props or default
const appName = computed(() => page.props.appName || 'AppSchool');
//const userProfilePic = computed(() => page.props.auth.user?.profile_photo_url || "/images/default-avatar.png");

</script>

<template>
    <div>
        <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <div class="px-3 py-3 lg:px-5 lg:pl-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center justify-start rtl:justify-end">
                        <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                            <span class="sr-only">Open sidebar</span>
                            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                            </svg>
                        </button>
                        <button @click="toggleDesktopSidebar" type="button" class="items-center hidden p-2 text-sm text-gray-500 rounded-lg sm:inline-flex hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600 ms-3">
                            <span class="sr-only">Toggle desktop sidebar</span>
                            <svg v-if="!isDesktopSidebarCollapsed" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            <svg v-else class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
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
                                        {{ page.props.auth.user?.name }} ({{ page.props.auth.user?.roles[0] }})
                                    </p>
                                    <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                        {{ page.props.auth.user?.email }}
                                    </p>
                                </div>
                                <ul class="py-1" role="none">
                                    <li v-for="item in userDropdownItems" :key="item.routeName">
                                        <Link v-if="route().has(item.routeName)" :href="route(item.routeName)" :method="item.method" :as="item.as" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">
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
                       <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" :d="item.iconPath" />
                       </svg>
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
           <div class="pt-14"> <header class="py-6 bg-white shadow dark:bg-gray-800" v-if="$slots.header">
                  <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                      <slot name="header" />
                  </div>
              </header>
              <main class="py-6">
                  <slot /> </main>
              <footer class="p-4 mt-auto bg-white border-t dark:bg-gray-800 dark:border-gray-700">
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
#sidebar, .transition-margin-left {
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
/* Hide scrollbar for sidebar visually but allow scrolling */
#sidebar::-webkit-scrollbar { display: none; }
#sidebar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
