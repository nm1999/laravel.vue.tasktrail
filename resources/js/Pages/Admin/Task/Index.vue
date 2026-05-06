<template>
    <div class="flex h-screen bg-gray-100">
        <aside class="w-64 bg-white shadow-md p-4">
            <SideBar />
        </aside>
        <main class="flex-1 p-6 overflow-auto">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold">Manage Task Assignments</h3>
                <button
                    type="button"
                    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                    @click="router.visit('/admin/tasks/create')"
                >
                    Create Task
                </button>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold">Task Assignments</h2>
                        <p class="text-sm text-gray-500">
                            View tasks, assigned team members, and their status.
                        </p>
                    </div>
                    <div class="w-full md:w-80">
                        <InputLabel value="Search tasks" />
                        <TextInput
                            v-model="taskSearch"
                            type="text"
                            placeholder="Search by title, assignee, or status"
                            class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                    Task
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                    Assigned People
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                    Status
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                    Deadline
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">
                                    Update
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="task in filteredTasks" :key="task.id">
                                <td class="px-4 py-4">
                                    <p class="font-medium text-gray-900">{{ task.title }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ task.description || "No description" }}
                                    </p>
                                </td>
                                <td class="px-4 py-4">
                                    <div
                                        v-if="task.assignedEmployees.length"
                                        class="flex flex-wrap gap-2"
                                    >
                                        <span
                                            v-for="person in task.assignedEmployees"
                                            :key="`${task.id}-${person.id}`"
                                            class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800"
                                        >
                                            {{ person.firstname }} {{ person.surname }}
                                            <button
                                                type="button"
                                                class="rounded-full bg-blue-200 px-1 leading-none text-blue-900 hover:bg-blue-300"
                                                title="Remove user from task"
                                                @click="removeAssignee(task, person.id)"
                                            >
                                                x
                                            </button>
                                        </span>
                                    </div>
                                    <span v-else class="text-sm text-gray-500">
                                        Unassigned
                                    </span>

                                    <div class="mt-3">
                                        <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">
                                            Modify assignees
                                        </label>
                                        <select
                                            v-model="assignmentSelections[task.id]"
                                            multiple
                                            class="w-full rounded-md border border-gray-300 px-2 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500"
                                        >
                                            <option
                                                v-for="employee in employees"
                                                :key="`${task.id}-option-${employee.id}`"
                                                :value="employee.id"
                                            >
                                                {{ employee.name }}
                                            </option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500">
                                            Hold Ctrl/Cmd to select multiple users.
                                        </p>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide"
                                        :class="statusClass(task.status)"
                                    >
                                        {{ formatStatus(task.status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">
                                    {{ formatDeadline(task.deadline) }}
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <button
                                        type="button"
                                        class="rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-slate-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                                        :disabled="isSelectionUnchanged(task)"
                                        @click="saveAssignees(task)"
                                    >
                                        Save assignees
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredTasks.length === 0">
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                                    No tasks found for the current search.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="taskPaginationLinks.length"
                    class="mt-6 flex flex-wrap items-center justify-end gap-2"
                >
                    <button
                        v-for="link in taskPaginationLinks"
                        :key="`${link.label}-${link.url}`"
                        type="button"
                        :disabled="!link.url"
                        class="rounded-md border px-3 py-2 text-sm transition-colors"
                        :class="link.active
                            ? 'border-slate-900 bg-slate-900 text-white'
                            : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50'"
                        v-html="link.label"
                        @click="goToTaskPage(link.url)"
                    />
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import { computed, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
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
        tasks: {
            type: [Array, Object],
            default: () => [],
        },
    },
    setup(props) {
        const taskSearch = ref("");
        const assignmentSelections = ref({});

        const taskList = computed(() => {
            const source = Array.isArray(props.tasks)
                ? props.tasks
                : props.tasks?.data || [];

            return source.map((task) => ({
                ...task,
                assignedEmployees: task.assigned_employees || task.assignedEmployees || [],
            }));
        });

        const filteredTasks = computed(() => {
            const query = taskSearch.value.trim().toLowerCase();

            if (!query) {
                return taskList.value;
            }

            return taskList.value.filter((task) => {
                const assigneeNames = task.assignedEmployees
                    .map((person) => `${person.firstname || ""} ${person.surname || ""}`.trim())
                    .join(" ")
                    .toLowerCase();

                const haystack = [
                    task.title,
                    task.description,
                    task.status,
                    assigneeNames,
                ]
                    .filter(Boolean)
                    .join(" ")
                    .toLowerCase();

                return haystack.includes(query);
            });
        });

        const taskPaginationLinks = computed(() =>
            (props.tasks?.links || []).filter(
                (link) => link.label !== "Previous" && link.label !== "Next"
            )
        );

        watch(
            taskList,
            (tasks) => {
                assignmentSelections.value = tasks.reduce((selectionMap, task) => {
                    selectionMap[task.id] = task.assignedEmployees.map((person) => person.id);

                    return selectionMap;
                }, {});
            },
            { immediate: true }
        );

        const employees = computed(() => {
            const source = Array.isArray(props.employees)
                ? props.employees
                : props.employees?.data || [];

            return source.map((employee) => {
                const fullName = `${employee.firstname || ""} ${employee.surname || ""}`.trim() || "Unknown";
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

        const formatStatus = (status) => {
            const labels = {
                todo: "To do",
                in_progress: "In progress",
                completed: "Completed",
            };

            return labels[status] || status || "Unknown";
        };

        const statusClass = (status) => {
            const classes = {
                todo: "bg-gray-100 text-gray-800",
                in_progress: "bg-amber-100 text-amber-800",
                completed: "bg-green-100 text-green-800",
            };

            return classes[status] || "bg-slate-100 text-slate-800";
        };

        const formatDeadline = (value) => {
            if (!value) {
                return "No deadline";
            }

            const date = new Date(value);
            if (Number.isNaN(date.getTime())) {
                return value;
            }

            return date.toLocaleString();
        };

        const goToTaskPage = (url) => {
            if (!url) {
                return;
            }

            router.visit(url, {
                preserveScroll: true,
                preserveState: true,
            });
        };

        const normalizeSelection = (selection = []) =>
            [...selection].map(Number).sort((a, b) => a - b);

        const isSelectionUnchanged = (task) => {
            const current = normalizeSelection(
                task.assignedEmployees.map((person) => person.id)
            );
            const selected = normalizeSelection(assignmentSelections.value[task.id] || []);

            return JSON.stringify(current) === JSON.stringify(selected);
        };

        const saveAssignees = (task) => {
            router.patch(
                `/admin/tasks/${task.id}/assignees`,
                {
                    assignedTo: normalizeSelection(assignmentSelections.value[task.id] || []),
                },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            title: "Task updated",
                            text: "Task assignees were updated successfully.",
                            icon: "success",
                            confirmButtonText: "OK",
                        });
                    },
                    onError: () => {
                        Swal.fire({
                            title: "Unable to update task",
                            text: "Please try again.",
                            icon: "error",
                            confirmButtonText: "OK",
                        });
                    },
                }
            );
        };

        const removeAssignee = async (task, userId) => {
            const person = task.assignedEmployees.find((employee) => employee.id === userId);
            const personName = person
                ? `${person.firstname || ""} ${person.surname || ""}`.trim()
                : "this user";

            const confirmation = await Swal.fire({
                title: "Remove assignee?",
                text: `Remove ${personName} from \"${task.title}\"?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, remove",
                cancelButtonText: "Cancel",
                reverseButtons: true,
            });

            if (!confirmation.isConfirmed) {
                return;
            }

            router.delete(`/admin/tasks/${task.id}/assignees/${userId}`, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: "User removed",
                        text: "The user has been removed from the task.",
                        icon: "success",
                        confirmButtonText: "OK",
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: "Unable to remove user",
                        text: "Please try again.",
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                },
            });
        };

        return {
            router,
            taskSearch,
            filteredTasks,
            taskPaginationLinks,
            assignmentSelections,
            employees,
            formatStatus,
            statusClass,
            formatDeadline,
            goToTaskPage,
            isSelectionUnchanged,
            saveAssignees,
            removeAssignee,
        };
    },
};
</script>
