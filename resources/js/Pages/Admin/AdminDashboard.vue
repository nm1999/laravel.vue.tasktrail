<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md p-4">
            <SideBar />
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 overflow-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Task</h1>
                <button class="bg-blue-500 text-white px-4 py-2 rounded-lg">
                    + Add Task
                </button>
            </div>

            <!-- Columns -->
            <div class="grid grid-cols-3 gap-6">
                <!-- To Do -->
                <div>
                    <h2 class="font-semibold mb-4">To Do</h2>
                    <div class="space-y-4">
                        <TaskCard
                            title="Q3 Evaluation"
                            description="Product evaluation"
                            date="11 Jun 2025"
                        />
                        <TaskCard
                            title="Monthly Report"
                            description="Traffic reports"
                            date="11 Jun 2025"
                        />
                    </div>
                </div>

                <!-- Doing -->
                <div>
                    <h2 class="font-semibold mb-4">Doing</h2>
                    <div class="space-y-4">
                        <TaskCard
                            title="Q2 Report"
                            description="Monthly reports"
                            date="14 Jan 2025"
                        />
                        <TaskCard
                            title="Product Exhibition"
                            description="March expo prep"
                            date="10 Jan 2025"
                        />
                    </div>
                </div>

                <!-- Done -->
                <div>
                    <h2 class="font-semibold mb-4">Done</h2>
                    <div class="space-y-4">
                        <TaskCard
                            title="Digital Marketing"
                            description="Campaign"
                            date="10 Jan 2025"
                        />
                        <TaskCard
                            title="Event 3.3"
                            description="Preparation"
                            date="10 Jan 2025"
                        />
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>


<script>
import TaskCard from "../../Components/TaskCard.vue";
import SideBar from "./SideBar.vue";
export default {
    name: "AdminDashboard",
    components: {
        TaskCard,
        SideBar
    },  
    props: {
        title: String,
        description: String,
        date: String,
    },
};
</script>

<style>
body {
    font-family: sans-serif;
}
</style>
