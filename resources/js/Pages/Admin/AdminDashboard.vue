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
                        @click="$router.push('/admin/tasks/create')"
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

                <!-- Task Assignments -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Task Assignments</h3>
                        <button
                            @click="$router.push('/admin/tasks')"
                            class="text-blue-600 dark:text-blue-400 text-sm hover:underline"
                        >
                            View all
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-300">Task</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-300">Assigned</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-300">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                <tr v-for="task in recentTasks" :key="task.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-3 py-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ task.title }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatDeadline(task.deadline) }}</p>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div v-if="task.assignedEmployees.length" class="flex flex-wrap gap-1">
                                            <span
                                                v-for="employee in task.assignedEmployees.slice(0, 2)"
                                                :key="`${task.id}-${employee.id}`"
                                                class="inline-flex rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900/40 dark:text-blue-200"
                                            >
                                                {{ employee.firstname }} {{ employee.surname }}
                                            </span>
                                            <span
                                                v-if="task.assignedEmployees.length > 2"
                                                class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-200"
                                            >
                                                +{{ task.assignedEmployees.length - 2 }}
                                            </span>
                                        </div>
                                        <span v-else class="text-xs text-gray-500 dark:text-gray-400">Unassigned</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <span
                                            class="inline-flex rounded-full px-2 py-1 text-xs font-semibold uppercase tracking-wide"
                                            :class="getStatusBadgeClass(task.status)"
                                        >
                                            {{ formatStatus(task.status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="recentTasks.length === 0">
                                    <td colspan="3" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No tasks available yet.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">NM</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 dark:text-white text-sm">{{ employee.firstname }} {{ employee.surname }}</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Software developer</p>
                            </div>
                            <!-- <div :class="['w-2 h-2 rounded-full', employee.status === 'active' ? 'bg-green-500' : 'bg-gray-400']"></div> -->
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
                            @click="$router.push('/admin/tasks/create')"
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
import { usePage } from "@inertiajs/vue3";
const page = usePage();

export default {
    name: "AdminDashboard",
    components: {
        SideBar,
    },
    setup() {
        // Metrics data
        const metrics = ref({
            totalTasks: page.props.stats.total_tasks,
            activeEmployees: page.props.stats.total_employees,
            completedTasks: page.props.stats.completed_tasks,
            overdueTasks: (page.props.stats.total_tasks - page.props.stats.completed_tasks),
        });

        // Task status distribution
        const taskStatuses = ref([
            { name: 'To Do', count: 45, percentage: 31, color: 'bg-gray-400' },
            { name: 'In Progress', count: 38, percentage: 26, color: 'bg-blue-500' },
            { name: 'Review', count: 22, percentage: 15, color: 'bg-yellow-500' },
            { name: 'Completed', count: 42, percentage: 28, color: 'bg-green-500' },
        ]);

        // Recent tasks with assignments
        const recentTasks = ref(
            (page.props.recentTasks || []).map((task) => ({
                ...task,
                assignedEmployees: task.assigned_employees || task.assignedEmployees || [],
            })),
        );

        // Recent employees
        const recentEmployees = ref([]);

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

        const formatStatus = (status) => {
            const labels = {
                todo: 'To do',
                in_progress: 'In progress',
                completed: 'Completed',
            };

            return labels[status] || status || 'Unknown';
        };

        const getStatusBadgeClass = (status) => {
            const classes = {
                todo: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                in_progress: 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
                completed: 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200',
            };

            return classes[status] || 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-200';
        };

        const formatDeadline = (value) => {
            if (!value) {
                return 'No deadline';
            }

            const date = new Date(value);
            if (Number.isNaN(date.getTime())) {
                return value;
            }

            return date.toLocaleString();
        };

        const getEmployees = ()=>{
            recentEmployees.value = page.props.employee;
        }

        getEmployees();
        

        return {
            metrics,
            taskStatuses,
            recentTasks,
            recentEmployees,
            recentActivities,
            getStatusColor,
            formatStatus,
            getStatusBadgeClass,
            formatDeadline,
        };
    },
};
</script>
