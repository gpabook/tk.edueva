<script setup>
import { ref, computed } from 'vue'; // Ensure 'computed' is imported here
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue'; // 1. Import the component
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();

const can = (permissionName) => {
    return page.props.auth.user && page.props.auth.user.permissions ? page.props.auth.user.permissions.includes(permissionName) : false;
};

</script>

<template>
    <div>

        <div class="min-h-screen bg-gray-100">

            <nav class="bg-white border-b border-gray-100">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="flex items-center shrink-0">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block w-auto text-gray-800 fill-current h-9"
                                    />
                                </Link>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                                   <NavLink :href="route('dashboard')">
      Dashboard
                                   </NavLink>

    <NavLink
      v-if="can('update avatar')"
      :href="route('avatar.update')"
      :active="route().current('avatar.update')">
      Update Avatar
    </NavLink>
    <NavLink
      v-if="can('view banks')"
      :href="route('bank.myaccount', $page.props.auth.user.id)"
      :active="route().current('bank.myaccount')">
      My BankAccount
    </NavLink>
    <NavLink
    v-if="can('read permissions')"
    :href="route('permissions.index')"
    :active="route().current('permission.index')">
      Permissions
    </NavLink>
    <NavLink
    v-if="can('read roles')"
    :href="route('roles.index')"
    :active="route().current('roles.index')">
      Roles
    </NavLink>
    <NavLink
    v-if="can('read banks')"
    :href="route('bank.user')"
    :active="route().current('bank.user')">
      Bank User
    </NavLink>
    <NavLink
    v-if="can('manage classes')"
    :href="route('class-levels.index')"
    :active="route().current('class-levels.index')">
      {{ $t('class_level_label') }}
    </NavLink>
    <NavLink
    v-if="can('manage classes')"
    :href="route('rooms.index')"
    :active="route().current('rooms.index')">
      Rooms
    </NavLink>

                            </div>



                            </div>



                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <div class="relative ms-3">
            <LanguageSwitcher />
            </div>
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <div class="flex items-center -me-2 sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="w-6 h-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                <div class="pt-2 pb-3 space-y-1">
  <ResponsiveNavLink
   v-if="can('read roles')"
    :href="route('dashboard')"
    :active="route().current('dashboard')"
  >
    Dashboard
  </ResponsiveNavLink>
  <ResponsiveNavLink
    :href="route('avatar.update')"
    :active="route().current('avatar.update')"
  >
    Update Avatar
  </ResponsiveNavLink>

  <ResponsiveNavLink
    v-if="can('read roles')"
    :href="route('roles.index')"
    :active="route().current('roles.*')"
  >
    Roles
  </ResponsiveNavLink>

  <ResponsiveNavLink
    v-if="can('view permissions')"
    :href="route('permissions.index')"
    :active="route().current('permissions.*')"
  >
    Permissions
  </ResponsiveNavLink>

  <ResponsiveNavLink
    :href="route('bank.user')"
    :active="route().current('bank.user')"
  >
    Bank User
  </ResponsiveNavLink>
</div>

                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink
                            :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <header class="bg-white shadow" v-if="$slots.header">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <slot />
            </main>
        </div>
    </div>
    <footer class="m-4 bg-white rounded-lg shadow-sm dark:bg-gray-900">
    <div class="w-full max-w-screen-xl p-4 mx-auto md:py-8">

        <hr class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8" />
        <span class="block text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2025 <a href="https://www.gpabook.net/" class="hover:underline">Prompt System Ltd.</a>. All Rights Reserved.</span>
    </div>
</footer>
</template>
