<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref } from 'vue'

// Props passed from controller
const props = defineProps({
    account: Object, // { id, user_id, balance, transactions: [] }
})

// Deposit form
const depositForm = useForm({ amount: '', description: '' })
function submitDeposit() {
    depositForm.post(route('bank.deposit'), {
        onSuccess: () => depositForm.reset()
    })
}

// Withdraw form
const withdrawForm = useForm({ amount: '', description: '' })
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
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                My Bank Account
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Current Balance -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium">Balance: {{ props.account.balance }}</h3>
                        </div>

                        <!-- Deposit Form -->
                        <div class="mb-4">
                            <h4 class="font-semibold">Deposit Funds</h4>
                            <form @submit.prevent="submitDeposit" class="flex flex-col space-y-2">
                                <input v-model="depositForm.amount" type="number" step="0.01" placeholder="Amount"
                                    class="border px-3 py-2 rounded" />
                                <input v-model="depositForm.description" type="text"
                                    placeholder="Description (optional)" class="border px-3 py-2 rounded" />
                                <button type="submit" :disabled="depositForm.processing"
                                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                    Deposit
                                </button>
                            </form>
                        </div>

                        <!-- Withdraw Form -->
                        <div class="mb-6">
                            <h4 class="font-semibold">Withdraw Funds</h4>
                            <form @submit.prevent="submitWithdraw" class="flex flex-col space-y-2">
                                <input v-model="withdrawForm.amount" type="number" step="0.01" placeholder="Amount"
                                    class="border px-3 py-2 rounded" />
                                <input v-model="withdrawForm.description" type="text"
                                    placeholder="Description (optional)" class="border px-3 py-2 rounded" />
                                <button type="submit" :disabled="withdrawForm.processing"
                                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                    Withdraw
                                </button>
                            </form>
                        </div>

                        <!-- Transactions List -->
                        <div>
                            <h4 class="font-semibold mb-2">Transaction History</h4>
                            <table class="w-full text-left table-auto">
                                <thead>
                                    <tr>
                                        <th class="px-2 py-1">Date</th>
                                        <th class="px-2 py-1">Type</th>
                                        <th class="px-2 py-1">Amount</th>
                                        <th class="px-2 py-1">Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="tx in props.account.transactions" :key="tx.id" class="border-t">
                                        <td class="px-2 py-1">{{ tx.created_at }}</td>
                                        <td class="px-2 py-1 capitalize">{{ tx.type }}</td>
                                        <td class="px-2 py-1">{{ tx.amount }}</td>
                                        <td class="px-2 py-1">{{ tx.description }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Print Passbook -->
                        <div class="mt-6">
                            <a :href="route('bank.passbook.pdf')" target="_blank"
                                class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Print Passbook
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
