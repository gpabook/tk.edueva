<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
  type: String,
  required: true,
});

const input = ref(null);

onMounted(() => {
  if (input.value.hasAttribute('autofocus')) {
    input.value.focus();
  }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
  <input
    ref="input"
    v-model="model"
    v-bind="$attrs"
    :class="[
      'w-full px-3 py-2 text-sm rounded-md border shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
      'border-gray-300 dark:border-gray-600',
      'bg-white dark:bg-gray-700',
      'text-gray-900 dark:text-white',
      'placeholder-gray-400 dark:placeholder-gray-400'
    ]"
  />
</template>

