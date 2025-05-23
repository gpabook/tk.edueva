<script setup>
    import { Head, Link } from '@inertiajs/vue3';
    import Pagination from '@/Components/Pagination.vue';

    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    defineOptions({ layout: AuthenticatedLayout});

    defineProps({
        users: Object
       // users: Array
    });
</script>
<template>
   <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold">Bank Accounts</h1>
      </div>

      <div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    ID
                </th>
                <th scope="col" class="px-6 py-3">
                    UserID
                </th>
                <th scope="col" class="px-6 py-3">
                    Name
                </th>
                <th scope="col" class="px-6 py-3">
                    Action
                </th>
                <th scope="col" class="px-6 py-3">
                    Balance
                </th>
                <th scope="col" class="px-6 py-3">
                    Created At
                </th>
            </tr>
        </thead>
        <tbody>

            <tr v-for="item in users.data" :key="item.id" class="border-b border-gray-200 odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ item.id }}
                </th>
                <td class="px-6 py-4">
                    {{ item.user_id }}
                </td>
                <td class="px-6 py-4">
                    {{ item.user.name }}
                </td>
                <td class="px-6 py-4">
                <Link class="px-3 py-2 text-xs font-medium text-center text-gray-700 bg-blue-100 rounded-lg hover:bg-blue-200 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                :href="route('bank.account', item.user_id)">
              ฝาก-ถอน
              </Link>


                </td>
                <td class="px-6 py-4">
                    {{ item.balance }}
                </td>
                <td class="px-6 py-4">
                    {{ new Date(item.created_at).toLocaleDateString('th-TH') }} {{ new Date(item.created_at).toLocaleTimeString('th-TH') }}
                </td>
            </tr>

        </tbody>
    </table>
    <Pagination :links="users.links" />
    <div class="mt-6">

    </div>

</div>
</div>
</div>
</div>
</div>

</template>


