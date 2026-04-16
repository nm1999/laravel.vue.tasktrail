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
                <p v-if="message" class="text-sm text-red-600">{{ message }}</p>

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
                        <!-- Assigned To (Searchable Multi-select) -->
                        <div class="relative" ref="dropdownContainer">
                            <InputLabel value="Assigned To" />

                            <!-- Search Input -->
                            <div class="relative mt-2">
                                <input
                                    v-model="searchQuery"
                                    @focus="showDropdown = true"
                                    @input="showDropdown = true"
                                    type="text"
                                    placeholder="Search team members..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 pr-10"
                                />
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"
                                >
                                    <svg
                                        class="w-4 h-4 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Dropdown -->
                            <div
                                v-show="showDropdown"
                                class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto"
                            >
                                <div
                                    v-if="filteredEmployees.length === 0"
                                    class="px-3 py-2 text-gray-500 text-sm"
                                >
                                    No team members found
                                </div>
                                <div
                                    v-for="employee in filteredEmployees"
                                    :key="employee.id"
                                    @click="toggleEmployee(employee.id)"
                                    class="px-3 py-2 hover:bg-gray-100 cursor-pointer flex items-center justify-between"
                                    :class="{
                                        'bg-blue-50': isSelected(employee.id),
                                    }"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-xs font-medium"
                                        >
                                            {{ employee.initials }}
                                        </div>
                                        <div>
                                            <div
                                                class="font-medium text-gray-900"
                                            >
                                                {{ employee.name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ employee.role }}
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        v-if="isSelected(employee.id)"
                                        class="text-blue-600"
                                    >
                                        <svg
                                            class="w-5 h-5"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"
                                            ></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Selected Count -->
                            <p class="text-xs text-gray-500 mt-2">
                                {{ form.assignedTo.length }} team member{{
                                    form.assignedTo.length !== 1 ? "s" : ""
                                }}
                                selected
                            </p>
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
                    <div
                        v-if="form.assignedTo.length > 0"
                        class="bg-blue-50 border border-blue-200 rounded-md p-4"
                    >
                        <p class="text-sm font-medium text-blue-900 mb-3">
                            Selected Team Members:
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="employeeId in form.assignedTo"
                                :key="employeeId"
                                class="inline-flex items-center gap-2 bg-blue-200 text-blue-800 px-3 py-2 rounded-full text-sm"
                            >
                                <div
                                    class="w-5 h-5 bg-blue-300 rounded-full flex items-center justify-center text-xs font-medium"
                                >
                                    {{ getEmployeeById(employeeId).initials }}
                                </div>
                                {{ getEmployeeById(employeeId).name }}
                                <button
                                    @click="removeEmployee(employeeId)"
                                    class="ml-1 hover:bg-blue-300 rounded-full p-0.5 transition-colors"
                                >
                                    <svg
                                        class="w-3 h-3"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                </button>
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
                            :disabled="isLoading"
                            type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors"
                        >
                            <i v-if="isLoading" class="fa fa-spin"></i>
                            Create Task
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
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
    props: {
        employees: {
            type: [Array, Object],
            default: () => [],
        },
    },
    setup(props) {
        const page = usePage();
        const form = ref({
            title: "",
            description: "",
            assignedTo: [],
            deadline: "",
            status: "todo",
        });

        // Search and dropdown state
        const searchQuery = ref("");
        const showDropdown = ref(false);
        const dropdownContainer = ref(null);
        const message = ref(null);
        const isLoading = ref(false);

        // Normalize employees coming from Laravel/Inertia
        const employees = computed(() => {
            const source = Array.isArray(props.employees)
                ? props.employees
                : props.employees?.data || [];

            return source.map((employee) => {
                const fullName = `${employee.firstname || ""} ${employee.lastname || ""}`.trim() || "Unknown";
                const initials = fullName
                    .split(" ")
                    .filter(Boolean)
                    .slice(0, 2)
                    .map((part) => part[0])
                    .join("")
                    .toUpperCase();

                return {
                    id: employee.id,
                    name: fullName,
                    initials: initials || "??",
                    role: employee.role || employee.email || "Team Member",
                };
            });
        });

        // Filtered employees based on search
        const filteredEmployees = computed(() => {
            if (!searchQuery.value) {
                return employees.value;
            }
            const query = searchQuery.value.toLowerCase();
            return employees.value.filter(
                (employee) =>
                    employee.name.toLowerCase().includes(query) ||
                    employee.role.toLowerCase().includes(query),
            );
        });

        // Check if employee is selected
        const isSelected = (employeeId) => {
            return form.value.assignedTo.includes(employeeId);
        };

        // Toggle employee selection
        const toggleEmployee = (employeeId) => {
            const index = form.value.assignedTo.indexOf(employeeId);
            if (index > -1) {
                form.value.assignedTo.splice(index, 1);
            } else {
                form.value.assignedTo.push(employeeId);
            }
        };

        // Handle click outside to close dropdown
        const handleClickOutside = (event) => {
            if (
                dropdownContainer.value &&
                !dropdownContainer.value.contains(event.target)
            ) {
                showDropdown.value = false;
            }
        };

        // Get employee by ID
        const getEmployeeById = (employeeId) => {
            return (
                employees.value.find((emp) => emp.id === employeeId) || {
                    name: "Unknown",
                    initials: "??",
                }
            );
        };

        // Remove employee from selection
        const removeEmployee = (employeeId) => {
            const index = form.value.assignedTo.indexOf(employeeId);
            if (index > -1) {
                form.value.assignedTo.splice(index, 1);
            }
        };

        // Lifecycle hooks for click outside detection
        onMounted(() => {
            document.addEventListener("click", handleClickOutside);
        });

        onUnmounted(() => {
            document.removeEventListener("click", handleClickOutside);
        });

        const showSuccessAlert = (successMessage) => {
            if (!successMessage) {
                return;
            }

            Swal.fire({
                title: "Task created",
                text: successMessage,
                icon: "success",
                confirmButtonText: "OK",
            });
        };

        watch(
            () => page.props.flash?.success,
            (successMessage) => {
                if (!successMessage) {
                    return;
                }

                showSuccessAlert(successMessage);
                resetForm();
                isLoading.value = false;
                message.value = null;
            },
            { immediate: true },
        );

        const handleSubmit = () => {
            isLoading.value = true;
            router.post("/admin/tasks", form.value, {
                onSuccess: () => {
                    message.value = null;
                    isLoading.value = false;
                },
                onError: () => {
                    message.value = "Unable to create task. Check the form and try again.";
                    isLoading.value = false;
                },
            });
        };

        const resetForm = () => {
            form.value = {
                title: "",
                description: "",
                assignedTo: [],
                deadline: "",
                status: "todo",
            };
            searchQuery.value = "";
        };

        return {
            form,
            searchQuery,
            showDropdown,
            dropdownContainer,
            employees,
            filteredEmployees,
            isSelected,
            toggleEmployee,
            getEmployeeById,
            removeEmployee,
            handleSubmit,
            resetForm,
            message,
            isLoading,
        };
    },
};
</script>
