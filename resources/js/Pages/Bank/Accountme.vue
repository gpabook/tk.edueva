<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  account: Object, // { id, user_id, balance, transactions: [] }
  acc_name: String,
})

const { t } = useI18n()

// Deposit form
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

// Withdraw form
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
  <Head :title="t('bank.my_account')" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ t('bank.my_account') }} - {{ props.acc_name }} | {{ t('id') }}: {{ props.account.student_id }}
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="mb-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ t('bank.balance') }}:
                {{
                  Number(props.account.balance).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                  })
                }} {{ t('bank.currency') }}
              </h3>
            </div>

            <div>
              <h4 class="mb-4 font-semibold text-gray-700 text-md dark:text-gray-300">
                {{ t('bank.transaction_history') }}
              </h4>

              <div class="overflow-x-auto border border-gray-200 rounded-md dark:border-gray-700">
                <table class="min-w-full text-left table-auto">
                  <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                      <th class="px-4 py-2 text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                        {{ t('bank.date') }}
                      </th>
                      <th class="px-4 py-2 text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                        {{ t('bank.type') }}
                      </th>
                      <th class="px-4 py-2 text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                        {{ t('bank.amount') }}
                      </th>
                      <th class="px-4 py-2 text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                        {{ t('bank.description') }}
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    <tr
                      v-if="props.account.transactions?.length"
                      v-for="tx in props.account.transactions"
                      :key="tx.id"
                      class="hover:bg-gray-50 dark:hover:bg-gray-700/50"
                    >
                      <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap dark:text-gray-300">
                        {{ new Date(tx.created_at).toLocaleString() }}
                      </td>
                      <td class="px-4 py-2 text-sm text-gray-700 capitalize dark:text-gray-300">
                        {{ t(`bank.${tx.type}`) }}
                      </td>
                      <td
                        class="px-4 py-2 text-sm font-medium whitespace-nowrap"
                        :class="tx.type === 'deposit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                      >
                        {{ tx.type === 'deposit' ? '+' : '-' }}
                        {{
                          Number(tx.amount).toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                          })
                        }}
                      </td>
                      <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap dark:text-gray-300">
                        {{ tx.description }}
                      </td>
                    </tr>
                    <tr v-else>
                      <td colspan="4" class="px-4 py-3 text-sm text-center text-gray-500 dark:text-gray-400">
                        {{ t('bank.no_transactions') }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="mt-8 text-right">
              <a
                :href="route('bank.passbook.pdf', props.account.student_id)"
                target="_blank"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-offset-gray-800"
              >
                {{ t('bank.print_passbook') }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
