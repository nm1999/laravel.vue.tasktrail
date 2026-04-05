<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md p-4">
            <SideBar />
        </aside>
        <main class="flex-1 p-6 overflow-auto">
            <h3 class="text-2xl font-bold mb-6">Tasks</h3>
            
            <!-- Create Task Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-6">Create New Task</h2>
                
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <InputLabel value="Task Title" />
                        <TextInput
                            v-model="form.title"
                            type="text"
                            placeholder="Enter task title"
                            class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>

                    <!-- Description -->
                    <div>
                        <InputLabel value="Description" />
                        <textarea
                            v-model="form.description"
                            placeholder="Enter task description"
                            rows="4"
                            class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        ></textarea>
                    </div>

                    <!-- Grid for Assigned To and Deadline -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Assigned To (Multi-select) -->
                        <div>
                            <InputLabel value="Assigned To" />
                            <select
                                v-model="form.assignedTo"
                                multiple
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                size="5"
                            >
                                <option value="john">John Doe</option>
                                <option value="jane">Jane Smith</option>
                                <option value="mike">Mike Johnson</option>
                                <option value="sarah">Sarah Williams</option>
                                <option value="robert">Robert Brown</option>
                                <option value="emily">Emily Davis</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-2">Hold Ctrl/Cmd to select multiple people</p>
                        </div>

                        <!-- Deadline -->
                        <div>
                            <InputLabel value="Deadline" />
                            <TextInput
                                v-model="form.deadline"
                                type="datetime-local"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                    </div>

                    <!-- Selected Team Members Display -->
                    <div v-if="form.assignedTo.length > 0" class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <p class="text-sm font-medium text-blue-900 mb-2">Selected Team Members:</p>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="person in form.assignedTo"
                                :key="person"
                                class="inline-block bg-blue-200 text-blue-800 px-3 py-1 rounded-full text-sm"
                            >
                                {{ person }}
                            </span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end gap-4">
                        <button
                            type="button"
                            @click="resetForm"
                            class="px-6 py-2 bg-gray-300 text-gray-800 font-medium rounded-md hover:bg-gray-400 transition-colors"
                        >
                            Clear
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors"
                        >
                            Create Task
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>

<script>
import { ref } from 'vue';
import SideBar from "../SideBar.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";

export default {
    name: "Tasks",
    components: {
        SideBar,
        TextInput,
        InputLabel,
    },
    setup() {
        const form = ref({
            title: '',
            description: '',
            assignedTo: [],
            deadline: '',
        });

        const handleSubmit = () => {
            console.log('Task submitted:', form.value);
            // Add your form submission logic here
        };

        const resetForm = () => {
            form.value = {
                title: '',
                description: '',
                assignedTo: [],
                deadline: '',
            };
        };

        return {
            form,
            handleSubmit,
            resetForm,
        };
    },
};
</script>
