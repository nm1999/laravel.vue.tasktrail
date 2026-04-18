<!-- TaskCard.vue -->
<template>
  <div class="bg-white p-4 rounded-xl shadow cursor-move">
    <h4 class="font-semibold mb-2">{{ task.title }}</h4>

    <div class="w-full bg-gray-200 h-2 rounded-full">
      <div
        class="bg-blue-500 h-2 rounded-full"
        :style="{ width: task.progress + '%' }"
      ></div>
    </div>

    <div class="text-right text-xs mt-1 text-gray-500">
      {{ task.progress }}%
    </div>

    <div class="mt-3 flex justify-end">
      <Link
        :href="`/employee/tasks/${task.id}`"
        class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-blue-600"
      >
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8M8 14h5m9 1a3 3 0 01-3 3H8l-4 3V5a3 3 0 013-3h12a3 3 0 013 3v10z" />
        </svg>
        <span>{{ commentCount }}</span>
      </Link>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  task: {
    type: Object,
    required: true,
  },
})

const commentCount = computed(() => props.task.comments_count ?? props.task.comments?.length ?? 0)
</script>