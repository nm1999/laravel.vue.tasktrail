<template>
    <div v-if="task" class="space-y-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <Link href="/employee/tasks" class="text-sm text-blue-600 hover:text-blue-800">&larr; Back to Tasks</Link>
                <h2 class="mt-2 text-2xl font-bold text-gray-900">{{ task.title }}</h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ task.comments_count ?? task.comments?.length ?? 0 }} comment{{ (task.comments_count ?? task.comments?.length ?? 0) === 1 ? '' : 's' }}
                </p>
            </div>
            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold uppercase text-gray-700">{{ task.status }}</span>
        </div>

        <div class="rounded-xl bg-white p-5 shadow">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Description</h3>
            <p class="mt-2 whitespace-pre-line text-gray-800">{{ task.description || 'No description added for this task.' }}</p>
        </div>

        <div class="rounded-xl bg-white p-5 shadow">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Add Comment</h3>
            <form class="mt-3 space-y-3" @submit.prevent="submitComment">
                <textarea
                    v-model="form.body"
                    rows="4"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    placeholder="Write a comment for this task"
                />
                <p v-if="form.errors.body" class="text-sm text-red-600">{{ form.errors.body }}</p>
                <div class="flex justify-end">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        {{ form.processing ? 'Posting...' : 'Post Comment' }}
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-xl bg-white p-5 shadow">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Comments</h3>
            <div v-if="(task.comments || []).length === 0" class="mt-4 rounded-lg bg-gray-50 p-4 text-sm text-gray-600">
                No comments yet. Start the discussion.
            </div>
            <div v-else class="mt-4 space-y-4">
                <div
                    v-for="comment in task.comments"
                    :key="comment.id"
                    class="rounded-lg border border-gray-200 p-4"
                >
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-semibold text-gray-900">{{ authorName(comment) }}</p>
                        <p class="text-xs text-gray-500">{{ formatDate(comment.created_at) }}</p>
                    </div>
                    <p class="mt-2 whitespace-pre-line text-sm text-gray-700">{{ comment.body }}</p>
                </div>
            </div>
        </div>
    </div>

    <div v-else class="rounded-xl bg-white p-6 text-sm text-gray-600 shadow">
        Task not found.
    </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    task: {
        type: Object,
        default: null,
    },
})

const form = useForm({
    body: '',
})

const submitComment = () => {
    if (!props.task?.id) return

    form.post(`/employee/tasks/${props.task.id}/comments`, {
        preserveScroll: true,
        onSuccess: () => form.reset('body'),
    })
}

const authorName = (comment) => {
    const first = comment?.author?.firstname ?? ''
    const last = comment?.author?.surname ?? ''
    const fullName = `${first} ${last}`.trim()
    return fullName || 'Unknown user'
}

const formatDate = (value) => {
    if (!value) return 'Unknown time'

    return new Date(value).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}
</script>
