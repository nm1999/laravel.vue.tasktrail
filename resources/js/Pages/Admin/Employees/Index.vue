<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md p-4">
            <SideBar />
        </aside>
        <main class="flex-1 p-6 overflow-auto">
            <h3 class="text-2xl font-bold mb-6">Employees</h3>

            <!-- Employee Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-6">Add New Employee</h2>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Grid Layout for Form Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <InputLabel value="First Name" />
                            <TextInput
                                v-model="form.firstName"
                                type="text"
                                placeholder="Enter first name"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <!-- Surname -->
                        <div>
                            <InputLabel value="Surname" />
                            <TextInput
                                v-model="form.surname"
                                type="text"
                                placeholder="Enter surname"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <!-- Email -->
                        <div>
                            <InputLabel value="Email" />
                            <TextInput
                                v-model="form.email"
                                type="email"
                                placeholder="Enter email address"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <InputLabel value="Date of Birth" />
                            <TextInput
                                v-model="form.dateOfBirth"
                                type="date"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <!-- Contact -->
                        <div>
                            <InputLabel value="Contact Number" />
                            <TextInput
                                v-model="form.contact"
                                type="tel"
                                placeholder="Enter contact number"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <!-- Department -->
                        <div>
                            <InputLabel value="Department" />
                            <select
                                v-model="form.department"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Select Department</option>
                                <option value="hr">Human Resources</option>
                                <option value="it">
                                    Information Technology
                                </option>
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

                        <!-- Password -->
                        <div>
                            <InputLabel value="Password" />
                            <TextInput
                                v-model="form.password"
                                type="password"
                                placeholder="Enter password"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <InputLabel value="Confirm Password" />
                            <TextInput
                                v-model="form.confirmPassword"
                                type="password"
                                placeholder="Confirm password"
                                class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors"
                        >
                            Add Employee
                        </button>
                    </div>
                </form>
            </div>

            <!-- Search Section -->
            <div class="mt-8 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Search Employees</h2>
                <InputLabel for="search" value="Search Employees" />
                <TextInput
                    v-model="search"
                    class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Type to search..."
                />
            </div>
        </main>
    </div>
</template>

<script>
import { ref } from "vue";
import SideBar from "../SideBar.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { router  } from "@inertiajs/vue3";

export default {
    name: "Employees",
    components: {
        SideBar,
        TextInput,
        InputLabel,
    },
    setup() {
        const form = ref({
            firstname: "",
            surname: "",
            email: "",
            dateOfBirth: "",
            contact: "",
            department: "",
            role: "",
            password: "",
            confirmPassword: "",
        });

        const search = ref("");

        const handleSubmit = async () => {
            console.log("Form submitted:", form.value);

            await router.post("/register", form, {
                onSuccess: (res) => {
                    console.log(res);
                },
            });
        };

        return {
            form,
            search,
            handleSubmit,
        };
    },
};
</script>
