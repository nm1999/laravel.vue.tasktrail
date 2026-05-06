<template>
    <div class="flex h-screen bg-gray-100">
        <aside class="w-64 bg-white shadow-md p-4">
            <SideBar />
        </aside>
        <main class="flex-1 p-6 overflow-auto">
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold">Create User</h3>
                <button
                    type="button"
                    class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-slate-700"
                    @click="router.visit('/admin/employees')"
                >
                    Change Roles
                </button>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-6">Add New User</h2>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel value="First Name" />
                            <TextInput
                                v-model="form.firstname"
                                type="text"
                                placeholder="Enter first name"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <InputLabel value="Surname" />
                            <TextInput
                                v-model="form.surname"
                                type="text"
                                placeholder="Enter surname"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <InputLabel value="Email" />
                            <TextInput
                                v-model="form.email"
                                type="email"
                                placeholder="Enter email address"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <InputLabel value="Date of Birth" />
                            <TextInput
                                v-model="form.dateOfBirth"
                                type="date"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <InputLabel value="Contact Number" />
                            <TextInput
                                v-model="form.contact"
                                type="tel"
                                placeholder="Enter contact number"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <InputLabel value="Department" />
                            <select
                                v-model="form.department"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Select Department</option>
                                <option value="hr">Human Resources</option>
                                <option value="it">Information Technology</option>
                                <option value="sales">Sales</option>
                                <option value="marketing">Marketing</option>
                                <option value="finance">Finance</option>
                            </select>
                        </div>

                        <div>
                            <InputLabel value="User role" />
                            <select
                                v-model="form.role"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="employee">Employee</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div>
                            <InputLabel value="Password" />
                            <TextInput
                                v-model="form.password"
                                type="password"
                                placeholder="Enter password"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <InputLabel value="Confirm Password" />
                            <TextInput
                                v-model="form.password_confirmation"
                                type="password"
                                placeholder="Confirm password"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors"
                        >
                            Add User
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>

<script>
import SideBar from "../SideBar.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

export default {
    name: "CreateEmployee",
    components: {
        SideBar,
        TextInput,
        InputLabel,
    },
    setup() {
        const form = useForm({
            firstname: "",
            surname: "",
            email: "",
            dateOfBirth: "",
            contact: "",
            department: "",
            role: "employee",
            password: "",
            password_confirmation: "",
        });

        const handleSubmit = () => {
            form.post("/admin/employees", {
                preserveScroll: true,
                onSuccess: () => {
                    form.reset();
                    form.role = "employee";
                    Swal.fire({
                        title: "User created",
                        text: "New user created successfully.",
                        icon: "success",
                        confirmButtonText: "OK",
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: "Unable to create user",
                        text: "Please review the form and try again.",
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                },
            });
        };

        return {
            router,
            form,
            handleSubmit,
        };
    },
};
</script>
