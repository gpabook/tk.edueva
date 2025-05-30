<script setup>
import { Head, useForm } from '@inertiajs/vue3'
// import { ref } from 'vue' // ref was imported but not used, can be removed if not needed for other things
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Props passed from controller
const props = defineProps({
    account: Object, // { id, user_id, balance, transactions: [] }
})

// Deposit form (logic remains the same)
const depositForm = useForm({
    user_id: props.account.user_id,
    amount: '',
    description: ''
})
function submitDeposit() {
    depositForm.post(route('bank.deposit'), {
        onSuccess: () => depositForm.reset()
    })
}

// Withdraw form (logic remains the same)
const withdrawForm = useForm({
    user_id: props.account.user_id,
    amount: '',
    description: ''
})
function submitWithdraw() {
    withdrawForm.post(route('bank.withdraw'), {
        onSuccess: () => withdrawForm.reset()
    })
}

</script>

<template>

    <Head title="My Bank Account" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                My Bank Account {{ $attrs.acc_name }} | user_id: {{ props.account.user_id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100"> Balance: {{
                                    Number(props.account.balance)
                                        .toLocaleString('en-US', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        })
                                    + ' บาท'
                                }}
                            </h3>
                        </div>

                        <div>
                            <h4 class="mb-4 font-semibold text-gray-700 text-md dark:text-gray-300">Transaction History</h4>
                            <div class="overflow-x-auto border border-gray-200 rounded-md dark:border-gray-700">
                                <table class="min-w-full text-left table-auto">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Date</th>
                                            <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Type</th>
                                            <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Amount</th>
                                            <th class="px-4 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                        <tr v-if="props.account.transactions && props.account.transactions.length > 0" v-for="tx in props.account.transactions" :key="tx.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap dark:text-gray-300">{{ new Date(tx.created_at).toLocaleString() }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-700 capitalize whitespace-nowrap dark:text-gray-300">{{ tx.type }}</td>
                                            <td class="px-4 py-2 text-sm font-medium whitespace-nowrap" :class="tx.type === 'deposit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                                                {{ tx.type === 'deposit' ? '+' : '-' }}
                                                {{ Number(tx.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap dark:text-gray-300">{{ tx.description }}</td>
                                        </tr>
                                        <tr v-else>
                                            <td colspan="4" class="px-4 py-3 text-sm text-center text-gray-500 dark:text-gray-400">
                                                No transactions found.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-8 text-right">
                            <a :href="route('bank.passbook.pdf', props.account.user_id)" target="_blank"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-offset-gray-800">
                                Print Passbook
                            </a>
                        </div>

                        </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
