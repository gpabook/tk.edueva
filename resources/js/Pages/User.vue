<script setup>
    import { Head } from '@inertiajs/vue3';
    import SiteLayout from '@/Layouts/SiteLayout.vue';
    import Pagination from '@/Components/Pagination.vue';

    defineOptions({ layout: SiteLayout});

    defineProps({
        users: Object
       // users: Array
    });
</script>
<template>
    <Head title="ข้อมูลผู้ใช้" />
    <h1>Users</h1>


    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    ID
                </th>
                <th scope="col" class="px-6 py-3">
                    Name
                </th>
                <th scope="col" class="px-6 py-3">
                    Email
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
                    {{ item.name }}
                </td>
                <td class="px-6 py-4">
                    {{ item.email }}
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

</template>


