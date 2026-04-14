<!-- Full Kanban Dashboard with Drag & Drop (Vue 3 + Tailwind + vuedraggable) -->
<template>
    <div>
        <div class="grid grid-cols-4 gap-6">
            <KanbanColumn title="To Do" v-model="columns.todo" />
            <KanbanColumn title="In Progress" v-model="columns.progress" />
            <KanbanColumn title="In Review" v-model="columns.review" />
            <KanbanColumn title="Done" v-model="columns.done" />
        </div>
    </div>
</template>

<script setup>
import { reactive } from "vue";
import KanbanColumn from "./kanbanColumn.vue";

const columns = reactive({
    todo: [
        { id: 1, title: "Setup environment", progress: 40 },
        { id: 2, title: "Mobile app design", progress: 24 },
    ],
    progress: [{ id: 3, title: "Wireframes", progress: 24 }],
    review: [{ id: 4, title: "Booking UI", progress: 80 }],
    done: [{ id: 5, title: "Marketing", progress: 100 }],
});
</script>
