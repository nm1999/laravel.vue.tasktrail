
<!-- KanbanColumn.vue -->
<template>
  <div>
    <h3 class="font-semibold mb-3">{{ title }}</h3>

    <draggable
      :model-value="modelValue"
      @update:modelValue="updateValue"
      group="tasks"
      item-key="id"
      class="space-y-4 min-h-[200px]"
    >
      <template #item="{ element }">
        <TaskCard :title="element.title" :progress="element.progress" />
      </template>
    </draggable>
  </div>
</template>

<script setup>
import draggable from 'vuedraggable'
import TaskCard from './TaskCard.vue'

const props = defineProps({
  title: String,
  modelValue: Array
})

const emit = defineEmits(['update:modelValue'])
function updateValue(value) {
  emit('update:modelValue', value)
}
</script>