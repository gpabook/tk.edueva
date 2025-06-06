<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Props from backend
const props = defineProps({
  account: Object, // { id, user_id, balance, transactions: [] }
  acc_name: String
})

// Deposit form
const depositForm = useForm({
  student_id: props.account.student_id,
  amount: '',
  description: ''
})

function submitDeposit() {
  depositForm.post(route('bank.deposit'), {
    onSuccess: () => depositForm.reset()
  })
}

// Withdraw form
const withdrawForm = useForm({
  student_id: props.account.student_id,
  amount: '',
  description: ''
})

function submitWithdraw() {
  withdrawForm.post(route('bank.withdraw'), {
    onSuccess: () => withdrawForm.reset()
  })
}

// Format balance
const formattedBalance = computed(() => {
  return Number(props.account.balance).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }) + ' บาท'
})
</script>

<template>
  <Head :title="$t('bank.my_account')" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
        {{ $t('my_bank_account') }} |{{ $t('name') }}: {{ props.acc_name }} {{ $t('user_id') }}: {{ props.account.student_id }}
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow dark:bg-gray-800 sm:rounded-lg">
          <div class="p-6 text-gray-800 dark:text-gray-100">
            <!-- 💰 Current Balance -->
            <div class="mb-8">
              <h3 class="text-lg font-medium">
                {{ $t('bank.balance') }}: {{ formattedBalance }}
              </h3>
            </div>

            <!-- Grid for Deposit & Withdraw -->
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
              <!-- 📥 Deposit -->
              <div>
                <h4 class="mb-2 font-semibold">{{ $t('bank.deposit') }}</h4>
                <form @submit.prevent="submitDeposit" class="flex flex-col gap-3">
                  <input type="hidden" v-model="depositForm.student_id" />
                  <input
                    v-model="depositForm.amount"
                    type="number"
                    step="0.01"
                    class="p-2 border rounded dark:bg-gray-700 dark:border-gray-600"
                    :placeholder="$t('bank.amount')"
                  />
                  <input
                    v-model="depositForm.description"
                    type="text"
                    class="p-2 border rounded dark:bg-gray-700 dark:border-gray-600"
                    :placeholder="$t('bank.description_optional')"
                  />
                  <button
                    type="submit"
                    class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700"
                    :disabled="depositForm.processing"
                  >
                    {{ $t('bank.submit_deposit') }}
                  </button>
                </form>
              </div>

              <!-- 📤 Withdraw -->
              <div>
                <h4 class="mb-2 font-semibold">{{ $t('bank.withdraw') }}</h4>
                <form @submit.prevent="submitWithdraw" class="flex flex-col gap-3">
                  <input type="hidden" v-model="withdrawForm.student_id" />
                  <input
                    v-model="withdrawForm.amount"
                    type="number"
                    step="0.01"
                    class="p-2 border rounded dark:bg-gray-700 dark:border-gray-600"
                    :placeholder="$t('bank.amount')"
                  />
                  <input
                    v-model="withdrawForm.description"
                    type="text"
                    class="p-2 border rounded dark:bg-gray-700 dark:border-gray-600"
                    :placeholder="$t('bank.description_optional')"
                  />
                  <button
                    type="submit"
                    class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700"
                    :disabled="withdrawForm.processing"
                  >
                    {{ $t('bank.submit_withdraw') }}
                  </button>
                </form>
              </div>
            </div>

            <!-- 📄 Transactions -->
            <div class="mt-10">
              <h4 class="mb-2 font-semibold">{{ $t('bank.transaction_history') }}</h4>
              <div class="overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                  <thead>
                    <tr class="border-b dark:border-gray-600">
                      <th class="p-2">{{ $t('bank.date') }}</th>
                      <th class="p-2">{{ $t('bank.type') }}</th>
                      <th class="p-2">{{ $t('bank.amount') }}</th>
                      <th class="p-2">{{ $t('bank.description') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="tx in props.account.transactions"
                      :key="tx.id"
                      class="border-t dark:border-gray-700"
                    >
                      <td class="p-2">{{ tx.created_at }}</td>
                      <td class="p-2 capitalize">{{ tx.type }}</td>
                      <td class="p-2">{{ Number(tx.amount).toFixed(2) }}</td>
                      <td class="p-2">{{ tx.description }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- 🧾 Print Passbook -->
            <div class="mt-6">
              <a
                :href="route('bank.passbook.pdf', props.account.student_id)"
                target="_blank"
                class="inline-block px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700"
              >
                {{ $t('bank.print_passbook') }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
