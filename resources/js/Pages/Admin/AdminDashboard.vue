<template>
    <div class="flex h-screen bg-gray-100 dark:bg-gray-900 transition-colors">
        <!-- Sidebar -->
        <aside class="w-64 bg-white dark:bg-gray-800 shadow-md p-4">
            <SideBar />
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 overflow-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Welcome back! Here's what's happening with your tasks today.</p>
                </div>
                <div class="flex gap-3">
                    <button
                        @click="$router.push('/admin/tasks')"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                    >
                        + Create Task
                    </button>
                    <button
                        @click="$router.push('/admin/employees')"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                    >
                        + Add Employee
                    </button>
                </div>
            </div>

            <!-- Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Tasks -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Tasks</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ metrics.totalTasks }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <span class="text-2xl">📋</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center">
                        <span class="text-sm text-green-600 dark:text-green-400 font-medium">+12%</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">from last month</span>
                    </div>
                </div>

                <!-- Active Employees -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Employees</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ metrics.activeEmployees }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                            <span class="text-2xl">👥</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center">
                        <span class="text-sm text-green-600 dark:text-green-400 font-medium">+5%</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">from last month</span>
                    </div>
                </div>

                <!-- Completed Tasks -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completed Tasks</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ metrics.completedTasks }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                            <span class="text-2xl">✅</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center">
                        <span class="text-sm text-green-600 dark:text-green-400 font-medium">+8%</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">from last month</span>
                    </div>
                </div>

                <!-- Overdue Tasks -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Overdue Tasks</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ metrics.overdueTasks }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                            <span class="text-2xl">⚠️</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center">
                        <span class="text-sm text-red-600 dark:text-red-400 font-medium">-3%</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">from last month</span>
                    </div>
                </div>
            </div>

            <!-- Charts and Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Task Status Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Task Status Distribution</h3>
                    <div class="space-y-4">
                        <div v-for="status in taskStatuses" :key="status.name" class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div :class="['w-4 h-4 rounded', status.color]"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ status.name }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ status.count }}</span>
                                <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div :class="['h-2 rounded-full', status.color]" :style="{ width: status.percentage + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Tasks -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Tasks</h3>
                        <button
                            @click="$router.push('/admin/tasks')"
                            class="text-blue-600 dark:text-blue-400 text-sm hover:underline"
                        >
                            View all
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div v-for="task in recentTasks" :key="task.id" class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div :class="['w-3 h-3 rounded-full mt-2', getStatusColor(task.status)]"></div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 dark:text-white text-sm truncate">{{ task.title }}</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ task.assignedTo }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ task.dueDate }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Employees -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Employees</h3>
                        <button
                            @click="$router.push('/admin/employees')"
                            class="text-blue-600 dark:text-blue-400 text-sm hover:underline"
                        >
                            View all
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div v-for="employee in recentEmployees" :key="employee.id" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="w-10 h-10 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ employee.initials }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 dark:text-white text-sm">{{ employee.name }}</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ employee.role }}</p>
                            </div>
                            <div :class="['w-2 h-2 rounded-full', employee.status === 'active' ? 'bg-green-500' : 'bg-gray-400']"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Feed and Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Activity Feed -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h3>
                    <div class="space-y-4">
                        <div v-for="activity in recentActivities" :key="activity.id" class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-sm">{{ activity.icon }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-white">
                                    <span class="font-medium">{{ activity.user }}</span>
                                    {{ activity.action }}
                                    <span class="font-medium">{{ activity.target }}</span>
                                </p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ activity.time }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <button
                            @click="$router.push('/admin/tasks')"
                            class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors text-left"
                        >
                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mb-3">
                                <span class="text-white text-sm">📝</span>
                            </div>
                            <h4 class="font-medium text-gray-900 dark:text-white text-sm">Create Task</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Add a new task to the system</p>
                        </button>

                        <button
                            @click="$router.push('/admin/employees')"
                            class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors text-left"
                        >
                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center mb-3">
                                <span class="text-white text-sm">👤</span>
                            </div>
                            <h4 class="font-medium text-gray-900 dark:text-white text-sm">Add Employee</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Register a new team member</p>
                        </button>

                        <button
                            @click="$router.push('/admin/notifications')"
                            class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors text-left"
                        >
                            <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center mb-3">
                                <span class="text-white text-sm">🔔</span>
                            </div>
                            <h4 class="font-medium text-gray-900 dark:text-white text-sm">View Notifications</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Check recent updates</p>
                        </button>

                        <button
                            @click="$router.push('/admin/settings')"
                            class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors text-left"
                        >
                            <div class="w-8 h-8 bg-gray-500 rounded-lg flex items-center justify-center mb-3">
                                <span class="text-white text-sm">⚙️</span>
                            </div>
                            <h4 class="font-medium text-gray-900 dark:text-white text-sm">Settings</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Configure your preferences</p>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import { ref } from 'vue';
import SideBar from "./SideBar.vue";

export default {
    name: "AdminDashboard",
    components: {
        SideBar,
    },
    setup() {
        // Metrics data
        const metrics = ref({
            totalTasks: 147,
            activeEmployees: 23,
            completedTasks: 89,
            overdueTasks: 5,
        });

        // Task status distribution
        const taskStatuses = ref([
            { name: 'To Do', count: 45, percentage: 31, color: 'bg-gray-400' },
            { name: 'In Progress', count: 38, percentage: 26, color: 'bg-blue-500' },
            { name: 'Review', count: 22, percentage: 15, color: 'bg-yellow-500' },
            { name: 'Completed', count: 42, percentage: 28, color: 'bg-green-500' },
        ]);

        // Recent tasks
        const recentTasks = ref([
            {
                id: 1,
                title: 'Implement user authentication',
                assignedTo: 'John Doe',
                dueDate: 'Due in 2 days',
                status: 'in-progress'
            },
            {
                id: 2,
                title: 'Design new dashboard layout',
                assignedTo: 'Sarah Wilson',
                dueDate: 'Due tomorrow',
                status: 'review'
            },
            {
                id: 3,
                title: 'Fix mobile responsiveness',
                assignedTo: 'Mike Johnson',
                dueDate: 'Due in 5 days',
                status: 'to-do'
            },
            {
                id: 4,
                title: 'Update API documentation',
                assignedTo: 'Emily Davis',
                dueDate: 'Due in 1 week',
                status: 'completed'
            },
        ]);

        // Recent employees
        const recentEmployees = ref([
            {
                id: 1,
                name: 'Alex Thompson',
                role: 'Frontend Developer',
                initials: 'AT',
                status: 'active'
            },
            {
                id: 2,
                name: 'Lisa Chen',
                role: 'UI/UX Designer',
                initials: 'LC',
                status: 'active'
            },
            {
                id: 3,
                name: 'Robert Kim',
                role: 'Backend Developer',
                initials: 'RK',
                status: 'active'
            },
            {
                id: 4,
                name: 'Maria Garcia',
                role: 'Project Manager',
                initials: 'MG',
                status: 'active'
            },
        ]);

        // Recent activities
        const recentActivities = ref([
            {
                id: 1,
                user: 'John Doe',
                action: 'completed task',
                target: '"Implement user authentication"',
                time: '2 hours ago',
                icon: '✅'
            },
            {
                id: 2,
                user: 'Sarah Wilson',
                action: 'submitted for review',
                target: '"Design new dashboard"',
                time: '4 hours ago',
                icon: '👁️'
            },
            {
                id: 3,
                user: 'Mike Johnson',
                action: 'started working on',
                target: '"Fix mobile responsiveness"',
                time: '6 hours ago',
                icon: '🚀'
            },
            {
                id: 4,
                user: 'Emily Davis',
                action: 'commented on',
                target: '"API documentation"',
                time: '8 hours ago',
                icon: '💬'
            },
            {
                id: 5,
                user: 'Alex Thompson',
                action: 'joined the team',
                target: '',
                time: '1 day ago',
                icon: '🎉'
            },
        ]);

        // Helper function for status colors
        const getStatusColor = (status) => {
            const colors = {
                'to-do': 'bg-gray-400',
                'in-progress': 'bg-blue-500',
                'review': 'bg-yellow-500',
                'completed': 'bg-green-500',
            };
            return colors[status] || 'bg-gray-400';
        };

        return {
            metrics,
            taskStatuses,
            recentTasks,
            recentEmployees,
            recentActivities,
            getStatusColor,
        };
    },
};
</script>
