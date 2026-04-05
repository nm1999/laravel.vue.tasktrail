<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md p-4">
            <SideBar />
        </aside>
        <main class="flex-1 p-6 overflow-auto">
            <h3>Settings</h3>
        </main>
    </div>
</template>

<script>
import SideBar from "../SideBar.vue";
export default {
    name: "Settings",
    components: {
        SideBar,
    },
};
</script>
