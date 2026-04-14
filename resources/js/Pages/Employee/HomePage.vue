<template>
    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-100">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Welcome back, {{ page.props.auth.user.name }}!</h1>
            <p class="text-gray-700">You have a great day ahead. Let's stay productive!</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Active Tasks</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2">{{ activeTasks }}</p>
                    </div>
                    <svg class="h-12 w-12 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Completed</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ completedTasks }}</p>
                    </div>
                    <svg class="h-12 w-12 text-green-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Pending</p>
                        <p class="text-3xl font-bold text-orange-600 mt-2">{{ pendingTasks }}</p>
                    </div>
                    <svg class="h-12 w-12 text-orange-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                        <span class="text-gray-700">Started work on Setup environment</span>
                    </div>
                    <span class="text-sm text-gray-500">2 hours ago</span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                        <span class="text-gray-700">Completed Mobile app design</span>
                    </div>
                    <span class="text-sm text-gray-500">5 hours ago</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-orange-600 rounded-full"></div>
                        <span class="text-gray-700">Moved Wireframes to In Review</span>
                    </div>
                    <span class="text-sm text-gray-500">1 day ago</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";

const page = usePage();

const activeTasks = 5;
const completedTasks = 8;
const pendingTasks = 3;
</script>
