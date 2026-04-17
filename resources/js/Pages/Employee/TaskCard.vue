<!-- TaskCard.vue -->
<template>
  <div class="bg-white p-4 rounded-xl shadow cursor-move">
    <h4 class="font-semibold mb-2">{{ title }}</h4>

    <div class="w-full bg-gray-200 h-2 rounded-full">
      <div
        class="bg-blue-500 h-2 rounded-full"
        :style="{ width: progress + '%' }"
      ></div>
    </div>

    <div class="text-right text-xs mt-1 text-gray-500">
      {{ progress }} 65%
    </div>
  </div>
</template>

<script setup>
defineProps({ title: String, progress: Number })
</script>