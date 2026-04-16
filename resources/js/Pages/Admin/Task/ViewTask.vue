<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md p-4">
            <SideBar />
        </aside>

        <main class="flex-1 p-6 overflow-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <Link href="/admin/tasks" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-block">
                        ← Back to Tasks
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900">{{ task?.title || 'Task Details' }}</h1>
                </div>
                <div class="flex gap-3">
                    <button
                        @click="editTask"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                    >
                        Edit
                    </button>
                    <button
                        @click="deleteTask"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                    >
                        Delete
                    </button>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-3 gap-6">
                <!-- Left Column - Task Details -->
                <div class="col-span-2 space-y-6">
                    <!-- Task Description Card -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold mb-4 text-gray-900">Description</h2>
                        <p class="text-gray-700 leading-relaxed">
                            {{ task?.description || 'No description provided' }}
                        </p>
                    </div>

                    <!-- Progress Card -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold mb-4 text-gray-900">Progress</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700 font-medium">Completion</span>
                                <span class="text-2xl font-bold text-blue-600">{{ task?.progress || 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div
                                    class="bg-blue-600 h-3 rounded-full transition-all"
                                    :style="{ width: (task?.progress || 0) + '%' }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- Assigned Team Members Card -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold mb-4 text-gray-900">Assigned To</h2>
                        <div v-if="task?.assignedEmployees && task.assignedEmployees.length > 0" class="space-y-3">
                            <div
                                v-for="employee in task.assignedEmployees"
                                :key="employee.id"
                                class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg"
                            >
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ getInitials(employee.name) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ employee.name }}</p>
                                    <p class="text-sm text-gray-500">Email: {{ employee.email }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-gray-500 text-center py-6">
                            No team members assigned yet
                        </div>
                    </div>

                    <!-- Activities/Timeline -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold mb-4 text-gray-900">Timeline</h2>
                        <div class="space-y-4">
                            <div class="flex gap-4">
                                <div class="text-sm text-gray-500">Created:</div>
                                <div class="text-sm font-medium text-gray-900">{{ formatDate(task?.created_at) }}</div>
                            </div>
                            <div class="flex gap-4">
                                <div class="text-sm text-gray-500">Last Updated:</div>
                                <div class="text-sm font-medium text-gray-900">{{ formatDate(task?.updated_at) }}</div>
                            </div>
                            <div v-if="task?.creator" class="flex gap-4">
                                <div class="text-sm text-gray-500">Created By:</div>
                                <div class="text-sm font-medium text-gray-900">{{ task.creator.name }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Metadata -->
                <div class="space-y-6">
                    <!-- Status Card -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-sm font-semibold text-gray-600 uppercase mb-4">Status</h3>
                        <div class="flex items-center gap-2">
                            <span
                                class="w-3 h-3 rounded-full"
                                :class="getStatusColor(task?.status)"
                            ></span>
                            <span class="text-lg font-semibold text-gray-900 capitalize">
                                {{ task?.status || 'Unknown' }}
                            </span>
                        </div>
                    </div>

                    <!-- Priority Card -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-sm font-semibold text-gray-600 uppercase mb-4">Priority</h3>
                        <div class="flex items-center gap-2">
                            <span
                                class="px-3 py-1 rounded-full text-sm font-semibold"
                                :class="getPriorityClass(task?.priority)"
                            >
                                {{ task?.priority ? task.priority.charAt(0).toUpperCase() + task.priority.slice(1) : 'Not Set' }}
                            </span>
                        </div>
                    </div>

                    <!-- Deadline Card -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-sm font-semibold text-gray-600 uppercase mb-4">Deadline</h3>
                        <div v-if="task?.deadline" class="space-y-2">
                            <p class="text-lg font-semibold text-gray-900">
                                {{ formatDate(task.deadline) }}
                            </p>
                            <p
                                class="text-sm"
                                :class="isOverdue(task.deadline) ? 'text-red-600' : 'text-green-600'"
                            >
                                {{ getDeadlineStatus(task.deadline) }}
                            </p>
                        </div>
                        <p v-else class="text-gray-500">No deadline set</p>
                    </div>

                    <!-- Quick Stats -->
                    <div class="bg-blue-50 rounded-lg shadow-md p-6 border border-blue-200">
                        <h3 class="text-sm font-semibold text-gray-600 uppercase mb-4">Summary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Task ID:</span>
                                <span class="font-medium text-gray-900">#{{ task?.id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Assigned:</span>
                                <span class="font-medium text-gray-900">
                                    {{ task?.assignedEmployees?.length || 0 }} member{{
                                        task?.assignedEmployees?.length !== 1 ? 's' : ''
                                    }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="font-medium text-gray-900 capitalize">{{ task?.status }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import SideBar from '../SideBar.vue'

const props = defineProps({
    task: {
        type: Object,
        required: true,
    },
})

const getInitials = (name) => {
    return name
        .split(' ')
        .map(n => n.charAt(0))
        .join('')
        .toUpperCase()
}

const formatDate = (date) => {
    if (!date) return 'N/A'
    const d = new Date(date)
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }
    return d.toLocaleDateString('en-US', options)
}

const getStatusColor = (status) => {
    const colors = {
        'todo': 'bg-gray-400',
        'in-progress': 'bg-yellow-400',
        'completed': 'bg-green-400',
        'pending': 'bg-blue-400',
        'cancelled': 'bg-red-400',
    }
    return colors[status] || 'bg-gray-400'
}

const getPriorityClass = (priority) => {
    const classes = {
        'low': 'bg-green-100 text-green-800',
        'medium': 'bg-yellow-100 text-yellow-800',
        'high': 'bg-orange-100 text-orange-800',
        'critical': 'bg-red-100 text-red-800',
    }
    return classes[priority] || 'bg-gray-100 text-gray-800'
}

const isOverdue = (deadline) => {
    const now = new Date()
    return new Date(deadline) < now
}

const getDeadlineStatus = (deadline) => {
    const now = new Date()
    const deadlineDate = new Date(deadline)
    const diffTime = deadlineDate - now
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

    if (diffDays < 0) {
        return `Overdue by ${Math.abs(diffDays)} day${Math.abs(diffDays) !== 1 ? 's' : ''}`
    } else if (diffDays === 0) {
        return 'Due today'
    } else if (diffDays === 1) {
        return 'Due tomorrow'
    } else {
        return `Due in ${diffDays} days`
    }
}

const editTask = () => {
    // Navigate to edit page
    // TODO: Implement edit functionality
    window.location.href = `/admin/tasks/${props.task.id}/edit`
}

const deleteTask = () => {
    if (confirm('Are you sure you want to delete this task? This action cannot be undone.')) {
        // TODO: Implement delete functionality via API
        window.location.href = `/admin/tasks/${props.task.id}/delete`
    }
}
</script>

<style scoped>
/* Add any component-specific styles here */
</style>
