<!-- Full Kanban Dashboard with Drag & Drop (Vue 3 + Tailwind + vuedraggable) -->
<template>
    <div>
        <div class="grid grid-cols-4 gap-6">
            <KanbanColumn @modelValue="updateTodo()" title="To Do" v-model="stats.todo" />
            <KanbanColumn title="In Progress" v-model="stats.progress" />
            <KanbanColumn title="In Review" v-model="stats.review" />
            <KanbanColumn title="Done" v-model="stats.done" />
        </div>
    </div>
</template>

<script>
import { ref } from "vue";
import KanbanColumn from "./kanbanColumn.vue";


export default {
    props: {
        stats: {
            type: Object,
            default: null,
        },
    },
    components:{
        KanbanColumn
    },

    setup(props) {
        const columns = ref({
            todo: [
                { id: 1, title: "Setup environment", progress: 40 },
                { id: 2, title: "Mobile app design", progress: 24 },
            ],
            progress: [{ id: 3, title: "Wireframes", progress: 24 }],
            review: [{ id: 4, title: "Booking UI", progress: 80 }],
            done: [{ id: 5, title: "Marketing", progress: 100 }],
        });

       
        return {
            columns,
        };
    },
    methods:{
        updateTodo(e){
            console.log("changed items")
            console.log(e)
        }   
    }
};
</script>
