<script setup>
defineProps({
    task: {
        type: Object,
        required: true,
    },
});

defineEmits(['toggle', 'remove']);
</script>

<template>
    <li class="task" :class="{ done: task.done }">
        <button
            class="check"
            type="button"
            :aria-pressed="task.done"
            :aria-label="(task.done ? 'Reopen ' : 'Complete ') + task.title"
            @click="$emit('toggle')"
        >
            <svg v-if="task.done" viewBox="0 0 16 16" aria-hidden="true">
                <path d="m3.5 8.5 3 3 6-7" />
            </svg>
        </button>

        <div class="task-body">
            <p class="task-title">{{ task.title }}</p>
            <span class="tag" :class="'tag-' + task.priority">
                <span class="tag-dot"></span>{{ task.priority }}
            </span>
        </div>

        <button
            class="remove"
            type="button"
            :aria-label="'Delete ' + task.title"
            @click="$emit('remove')"
        >
            <svg viewBox="0 0 16 16" aria-hidden="true">
                <path d="m4 4 8 8M12 4l-8 8" />
            </svg>
        </button>
    </li>
</template>
