<!-- Full Kanban Dashboard with Drag & Drop (Vue 3 + Tailwind + vuedraggable) -->
<template>
    <div class="flex h-screen bg-gradient-to-br from-gray-100 to-purple-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white p-5 shadow-lg">
            <h2 class="text-xl font-bold mb-6">🌍 World</h2>
            <p class="bg-gray-100 p-2 rounded">My Tasks</p>
        </aside>

        <!-- Main -->
        <div class="flex-1 p-6 overflow-auto">
            <div class="grid grid-cols-4 gap-6">
                <KanbanColumn title="To Do" v-model="columns.todo" />
                <KanbanColumn title="In Progress" v-model="columns.progress" />
                <KanbanColumn title="In Review" v-model="columns.review" />
                <KanbanColumn title="Done" v-model="columns.done" />
            </div>
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
