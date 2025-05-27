<script setup>
import { Head, useForm} from '@inertiajs/vue3'
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'



// Props passed from controller
const props = defineProps({
    account: Object, // { id, user_id, balance, transactions: [] }
})

// Deposit form
const depositForm = useForm({
    user_id: props.account.user_id,    // ← initialize it here
    amount: '',
    description: '' })
function submitDeposit() {
    depositForm.post(route('bank.deposit'), {
        onSuccess: () => depositForm.reset()
    })
}

// Withdraw form
const withdrawForm = useForm({
    user_id: props.account.user_id,    // ← initialize it here
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
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                My Bank Account {{ $attrs.acc_name}} | user_id: {{ props.account.user_id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Current Balance -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium">Balance: {{ Number(props.account.balance)
       .toLocaleString('en-US', {
         minimumFractionDigits: 2,
         maximumFractionDigits: 2
       })
     + ' บาท'
  }}</h3>

                        </div>

                        <!-- Deposit Form -->
                        <div class="mb-4">
                            <h4 class="font-semibold">Deposit Funds</h4>
                            <form @submit.prevent="submitDeposit" class="flex flex-col space-y-2">
                                <input type="hidden" v-model="depositForm.user_id" />
                                <input v-model="depositForm.amount" type="number" step="0.01" placeholder="Amount"
                                    class="px-3 py-2 border rounded" />
                                <input v-model="depositForm.description" type="text"
                                    placeholder="Description (optional)" class="px-3 py-2 border rounded" />
                                <button type="submit" :disabled="depositForm.processing"
                                    class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">
                                    Deposit
                                </button>
                            </form>
                        </div>

                        <!-- Withdraw Form -->
                        <div class="mb-6">
                            <h4 class="font-semibold">Withdraw Funds</h4>
                            <form @submit.prevent="submitWithdraw" class="flex flex-col space-y-2">
                                <input type="hidden" v-model="withdrawForm.user_id" />
                                <input v-model="withdrawForm.amount" type="number" step="0.01" placeholder="Amount"
                                    class="px-3 py-2 border rounded" />
                                <input v-model="withdrawForm.description" type="text"
                                    placeholder="Description (optional)" class="px-3 py-2 border rounded" />
                                <button type="submit" :disabled="withdrawForm.processing"
                                    class="px-4 py-2 text-white bg-red-500 rounded hover:bg-red-600">
                                    Withdraw
                                </button>
                            </form>
                        </div>

                        <!-- Transactions List -->
                        <div>
                            <h4 class="mb-2 font-semibold">Transaction History</h4>
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
                            <a :href="route('bank.passbook.pdf', props.account.user_id)" target="_blank"
                                class="inline-block px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">
                                Print Passbook
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
