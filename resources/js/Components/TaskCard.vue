<!-- Save this as components/TaskCard.vue -->
<template>
    <div class="bg-white p-4 rounded-xl shadow hover:shadow-md transition">
        <h3 class="font-semibold text-lg">{{ title }}</h3>
        <p class="text-gray-500 text-sm">{{ description }}</p>
        <div class="mt-3 text-sm text-gray-400">Due: {{ date }}</div>
    </div>
</template>

<script>
export default {
    name: "TaskCard",
    props: {
        title: String,
        description: String,
        date: String,
    },
};
</script>
