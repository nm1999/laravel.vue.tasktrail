<template>
    <div class="flex h-screen bg-gray-100 dark:bg-gray-900 transition-colors">
        <!-- Sidebar -->
        <aside class="w-64 bg-white dark:bg-gray-800 shadow-md p-4">
            <SideBar />
        </aside>
        <main class="flex-1 p-6 overflow-auto">
            <!-- Header -->
            <div class="mb-8">
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white">Settings</h3>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Manage your account preferences and application settings</p>
            </div>

            <!-- Settings Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Appearance Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="text-2xl">🎨</span>
                        Appearance
                    </h4>

                    <div class="space-y-6">
                        <!-- Dark Mode Toggle -->
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Dark Mode</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Toggle between light and dark themes</p>
                            </div>
                            <button
                                @click="toggleDarkMode"
                                :class="[
                                    'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                                    darkMode ? 'bg-blue-600' : 'bg-gray-200'
                                ]"
                            >
                                <span
                                    :class="[
                                        'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                                        darkMode ? 'translate-x-6' : 'translate-x-1'
                                    ]"
                                />
                            </button>
                        </div>

                        <!-- Theme Selection -->
                        <div>
                            <h5 class="font-medium text-gray-900 dark:text-white mb-3">Theme</h5>
                            <div class="grid grid-cols-3 gap-3">
                                <button
                                    v-for="theme in themes"
                                    :key="theme.id"
                                    @click="selectedTheme = theme.id"
                                    :class="[
                                        'p-3 rounded-lg border-2 transition-all',
                                        selectedTheme === theme.id
                                            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                            : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'
                                    ]"
                                >
                                    <div class="flex flex-col items-center gap-2">
                                        <div :class="theme.preview" class="w-8 h-8 rounded"></div>
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ theme.name }}</span>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Font Size -->
                        <div>
                            <h5 class="font-medium text-gray-900 dark:text-white mb-3">Font Size</h5>
                            <select
                                v-model="fontSize"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            >
                                <option value="small">Small</option>
                                <option value="medium">Medium</option>
                                <option value="large">Large</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Account Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="text-2xl">👤</span>
                        Account
                    </h4>

                    <div class="space-y-6">
                        <!-- Profile Picture -->
                        <div>
                            <h5 class="font-medium text-gray-900 dark:text-white mb-3">Profile Picture</h5>
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                    <span class="text-2xl">👤</span>
                                </div>
                                <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    Change Photo
                                </button>
                            </div>
                        </div>

                        <!-- Name -->
                        <div>
                            <InputLabel value="Full Name" />
                            <TextInput
                                v-model="page.props.user.firstname"
                                type="text"
                                placeholder="Enter your full name"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            />
                        </div>

                        <!-- Email -->
                        <div>
                            <InputLabel value="Email Address" />
                            <TextInput
                                v-model="page.props.user.email"
                                type="email"
                                placeholder="Enter your email"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            />
                        </div>

                        <!-- Save Account Changes -->
                        <button class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                            Save Changes
                        </button>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="text-2xl">🔔</span>
                        Notifications
                    </h4>

                    <div class="space-y-4">
                        <div v-for="notification in notificationSettings" :key="notification.id" class="flex items-center justify-between">
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">{{ notification.title }}</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ notification.description }}</p>
                            </div>
                            <button
                                @click="notification.enabled = !notification.enabled"
                                :class="[
                                    'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                                    notification.enabled ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-600'
                                ]"
                            >
                                <span
                                    :class="[
                                        'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                                        notification.enabled ? 'translate-x-6' : 'translate-x-1'
                                    ]"
                                />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="text-2xl">🔒</span>
                        Security
                    </h4>

                    <div class="space-y-6">
                        <!-- Change Password -->
                        <div>
                            <h5 class="font-medium text-gray-900 dark:text-white mb-3">Change Password</h5>
                            <div class="space-y-3">
                                <TextInput
                                    v-model="securitySettings.currentPassword"
                                    type="password"
                                    placeholder="Current password"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                />
                                <TextInput
                                    v-model="securitySettings.newPassword"
                                    type="password"
                                    placeholder="New password"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                />
                                <TextInput
                                    v-model="securitySettings.confirmPassword"
                                    type="password"
                                    placeholder="Confirm new password"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                />
                            </div>
                            <button class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                Update Password
                            </button>
                        </div>

                        <!-- Two-Factor Authentication -->
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Two-Factor Authentication</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Add an extra layer of security</p>
                            </div>
                            <button
                                @click="securitySettings.twoFactorEnabled = !securitySettings.twoFactorEnabled"
                                :class="[
                                    'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                                    securitySettings.twoFactorEnabled ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-600'
                                ]"
                            >
                                <span
                                    :class="[
                                        'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                                        securitySettings.twoFactorEnabled ? 'translate-x-6' : 'translate-x-1'
                                    ]"
                                />
                            </button>
                        </div>

                        <!-- Session Management -->
                        <div>
                            <h5 class="font-medium text-gray-900 dark:text-white mb-3">Active Sessions</h5>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Current Session</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Chrome on Windows • Active now</p>
                                    </div>
                                    <span class="text-xs bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded">Current</span>
                                </div>
                            </div>
                            <button class="mt-3 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                Revoke All Sessions
                            </button>
                        </div>
                    </div>
                </div>

                <!-- System Preferences -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="text-2xl">⚙️</span>
                        System
                    </h4>

                    <div class="space-y-6">
                        <!-- Language -->
                        <div>
                            <h5 class="font-medium text-gray-900 dark:text-white mb-3">Language</h5>
                            <select
                                v-model="systemSettings.language"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            >
                                <option value="en">English</option>
                                <option value="es">Spanish</option>
                                <option value="fr">French</option>
                                <option value="de">German</option>
                            </select>
                        </div>

                        <!-- Timezone -->
                        <div>
                            <h5 class="font-medium text-gray-900 dark:text-white mb-3">Timezone</h5>
                            <select
                                v-model="systemSettings.timezone"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            >
                                <option value="UTC">UTC</option>
                                <option value="EST">Eastern Time</option>
                                <option value="PST">Pacific Time</option>
                                <option value="GMT">GMT</option>
                            </select>
                        </div>

                        <!-- Auto-save -->
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="font-medium text-gray-900 dark:text-white">Auto-save</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Automatically save changes</p>
                            </div>
                            <button
                                @click="systemSettings.autoSave = !systemSettings.autoSave"
                                :class="[
                                    'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                                    systemSettings.autoSave ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-600'
                                ]"
                            >
                                <span
                                    :class="[
                                        'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                                        systemSettings.autoSave ? 'translate-x-6' : 'translate-x-1'
                                    ]"
                                />
                            </button>
                        </div>

                        <!-- Data Export -->
                        <div>
                            <h5 class="font-medium text-gray-900 dark:text-white mb-3">Data Management</h5>
                            <div class="space-y-2">
                                <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    Export Data
                                </button>
                                <button class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                    Delete Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- About & Support -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="text-2xl">ℹ️</span>
                        About & Support
                    </h4>

                    <div class="space-y-4">
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <h5 class="font-medium text-gray-900 dark:text-white">TaskTrail v1.0.0</h5>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Project Management Made Simple</p>
                        </div>

                        <div class="space-y-2">
                            <button class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                Help Center
                            </button>
                            <button class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                Contact Support
                            </button>
                            <button class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                Privacy Policy
                            </button>
                            <button class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                Terms of Service
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import SideBar from "../SideBar.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();
export default {
    name: "Settings",
    components: {
        SideBar,
        TextInput,
        InputLabel,
    },
    setup() {
        // Dark mode state
        const darkMode = ref(false);

        // Appearance settings
        const selectedTheme = ref('default');
        const fontSize = ref('medium');

        const themes = [
            { id: 'default', name: 'Default', preview: 'bg-blue-500' },
            { id: 'purple', name: 'Purple', preview: 'bg-purple-500' },
            { id: 'green', name: 'Green', preview: 'bg-green-500' },
        ];

        // Account settings
        const accountSettings = ref({
            name: 'John Doe',
            email: 'john.doe@example.com',
        });

        // Notification settings
        const notificationSettings = ref([
            {
                id: 'task_assignments',
                title: 'Task Assignments',
                description: 'Get notified when you\'re assigned to tasks',
                enabled: true,
            },
            {
                id: 'task_updates',
                title: 'Task Updates',
                description: 'Receive updates on task progress',
                enabled: true,
            },
            {
                id: 'deadline_reminders',
                title: 'Deadline Reminders',
                description: 'Get reminded about upcoming deadlines',
                enabled: false,
            },
            {
                id: 'comments',
                title: 'Comments & Mentions',
                description: 'Notifications for comments and mentions',
                enabled: true,
            },
            {
                id: 'system_updates',
                title: 'System Updates',
                description: 'Important system announcements',
                enabled: false,
            },
        ]);

        // Security settings
        const securitySettings = ref({
            currentPassword: '',
            newPassword: '',
            confirmPassword: '',
            twoFactorEnabled: false,
        });

        // System settings
        const systemSettings = ref({
            language: 'en',
            timezone: 'UTC',
            autoSave: true,
        });

        // Dark mode toggle function
        const toggleDarkMode = () => {
            darkMode.value = !darkMode.value;
            // Apply dark mode to document
            if (darkMode.value) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
            }
        };

        // Initialize dark mode from localStorage
        onMounted(() => {
            const savedDarkMode = localStorage.getItem('darkMode');
            if (savedDarkMode === 'true') {
                darkMode.value = true;
                document.documentElement.classList.add('dark');
            }
        });

        return {
            darkMode,
            selectedTheme,
            fontSize,
            themes,
            accountSettings,
            notificationSettings,
            securitySettings,
            systemSettings,
            toggleDarkMode,
            page
        };
    },
};
</script>
