<template>
    <div class="flex h-screen bg-gray-100">
        <aside class="w-64 bg-white shadow-md p-4">
            <SideBar />
        </aside>
        <main class="flex-1 p-6 overflow-auto">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold">Change User Roles</h3>
                <button
                    type="button"
                    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                    @click="router.visit('/admin/employees/create')"
                >
                    Create User
                </button>
            </div>

            <div class="mt-8 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Search Employees</h2>
                <InputLabel for="search" value="Search Employees" />
                <TextInput
                    v-model="search"
                    class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Type to search..."
                />
            </div>

            <div class="mt-8 bg-white rounded-lg shadow-md p-6">
                <div class="flex flex-col gap-6">
                    <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold">Created Users</h2>
                            <p class="text-sm text-gray-500">
                                Review registered users and change their access level from here.
                            </p>
                        </div>
                        <div class="text-sm text-gray-500">
                            Showing {{ filteredEmployees.length }} of {{ totalEmployees }} users
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Total users</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-900">{{ totalEmployees }}</p>
                        </div>
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                            <p class="text-sm text-emerald-700">Admins</p>
                            <p class="mt-2 text-2xl font-semibold text-emerald-900">{{ adminCount }}</p>
                        </div>
                        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                            <p class="text-sm text-blue-700">Employees</p>
                            <p class="mt-2 text-2xl font-semibold text-blue-900">{{ employeeCount }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">
                            Change a user's role without editing the rest of the profile.
                        </span>
                    </div>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                    User
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                    Department
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                    Current access
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                    Role
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wide text-gray-500">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr
                                v-for="employee in filteredEmployees"
                                :key="employee.id"
                            >
                                <td class="px-4 py-4">
                                    <div class="font-medium text-gray-900">
                                        {{ employee.firstname }} {{ employee.surname }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ employee.email }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">
                                    {{ employee.department || "Unassigned" }}
                                </td>
                                <td class="px-4 py-4">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide"
                                        :class="employee.role === 'admin'
                                            ? 'bg-emerald-100 text-emerald-800'
                                            : 'bg-blue-100 text-blue-800'"
                                    >
                                        {{ employee.role }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <select
                                        v-model="roleSelections[employee.id]"
                                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500"
                                    >
                                        <option value="employee">Employee</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <button
                                        type="button"
                                        class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-slate-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                                        :disabled="roleSelections[employee.id] === employee.role"
                                        @click="updateRole(employee)"
                                    >
                                        Save role
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredEmployees.length === 0">
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                                    No employees match your search.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="paginationLinks.length > 0"
                    class="mt-6 flex flex-wrap items-center justify-end gap-2"
                >
                    <button
                        v-for="link in paginationLinks"
                        :key="`${link.label}-${link.url}`"
                        type="button"
                        class="rounded-md border px-3 py-2 text-sm transition-colors"
                        :class="link.active
                            ? 'border-slate-900 bg-slate-900 text-white'
                            : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50'"
                        :disabled="!link.url"
                        @click="goToPage(link.url)"
                        v-html="link.label"
                    />
                </div>
            </div>
        </main>
    </div>
</template>

<script>
import { computed, ref, watch } from "vue";
import SideBar from "../SideBar.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";

export default {
    name: "Employees",
    props: {
        employees: {
            type: Object,
            default: () => ({ data: [] }),
        },
    },
    components: {
        SideBar,
        TextInput,
        InputLabel,
    },
    setup(props) {
        const search = ref("");
        const roleSelections = ref({});

        const employeeList = computed(() => props.employees?.data || []);
        const totalEmployees = computed(() => props.employees?.total || employeeList.value.length);
        const adminCount = computed(
            () => employeeList.value.filter((employee) => employee.role === "admin").length
        );
        const employeeCount = computed(
            () => employeeList.value.filter((employee) => employee.role !== "admin").length
        );
        const paginationLinks = computed(() =>
            (props.employees?.links || []).filter(
                (link) => link.label !== "Previous" && link.label !== "Next"
            )
        );

        const filteredEmployees = computed(() => {
            const query = search.value.trim().toLowerCase();

            if (!query) {
                return employeeList.value;
            }

            return employeeList.value.filter((employee) => {
                const haystack = [
                    employee.firstname,
                    employee.surname,
                    employee.email,
                    employee.department,
                    employee.role,
                ]
                    .filter(Boolean)
                    .join(" ")
                    .toLowerCase();

                return haystack.includes(query);
            });
        });

        watch(
            employeeList,
            (employees) => {
                roleSelections.value = employees.reduce((selections, employee) => {
                    selections[employee.id] = employee.role || "employee";

                    return selections;
                }, {});
            },
            { immediate: true }
        );

        const updateRole = (employee) => {
            router.patch(
                `/admin/employees/${employee.id}/role`,
                {
                    role: roleSelections.value[employee.id],
                },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            title: "Role updated",
                            text: `${employee.firstname} ${employee.surname} is now ${roleSelections.value[employee.id]}.`,
                            icon: "success",
                            confirmButtonText: "OK",
                        });
                    },
                    onError: () => {
                        roleSelections.value[employee.id] = employee.role;
                        Swal.fire({
                            title: "Unable to update role",
                            text: "The role could not be changed. Try again.",
                            icon: "error",
                            confirmButtonText: "OK",
                        });
                    },
                }
            );
        };

        const goToPage = (url) => {
            if (!url) {
                return;
            }

            router.visit(url, {
                preserveScroll: true,
                preserveState: true,
            });
        };

        return {
            adminCount,
            employeeCount,
            router,
            search,
            filteredEmployees,
            goToPage,
            paginationLinks,
            roleSelections,
            totalEmployees,
            updateRole,
        };
    },
};
</script>
