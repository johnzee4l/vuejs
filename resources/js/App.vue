<script setup>
import { computed, reactive, ref, watch } from 'vue';
import TaskItem from './components/TaskItem.vue';

const filters = [
    { value: 'all', label: 'All' },
    { value: 'open', label: 'Open' },
    { value: 'done', label: 'Done' },
];

const priorities = [
    { value: 'low', label: 'Low' },
    { value: 'normal', label: 'Normal' },
    { value: 'high', label: 'High' },
];

const seed = JSON.parse(document.getElementById('app').dataset.tasks);
const stored = JSON.parse(localStorage.getItem('tend.tasks') ?? 'null');

const tasks = ref(stored ?? seed);
const draft = reactive({ title: '', priority: 'normal' });
const errors = reactive({ title: '' });
const filter = ref('all');
const search = ref('');

const today = new Intl.DateTimeFormat('en', {
    weekday: 'long',
    month: 'long',
    day: 'numeric',
}).format(new Date());

const visibleTasks = computed(() => {
    const query = search.value.trim().toLowerCase();

    return tasks.value.filter((task) => {
        const matchesFilter =
            filter.value === 'all' ||
            (filter.value === 'done' ? task.done : !task.done);

        return matchesFilter && task.title.toLowerCase().includes(query);
    });
});

const doneCount = computed(() => tasks.value.filter((task) => task.done).length);
const openCount = computed(() => tasks.value.length - doneCount.value);
const progress = computed(() =>
    tasks.value.length ? Math.round((doneCount.value / tasks.value.length) * 100) : 0,
);

// Watcher: keep the user's list between visits.
watch(
    tasks,
    (value) => localStorage.setItem('tend.tasks', JSON.stringify(value)),
    { deep: true },
);

function addTask() {
    const title = draft.title.trim();

    if (title.length < 3) {
        errors.title = 'Please use at least 3 characters.';
        return;
    }

    tasks.value.unshift({
        id: Date.now(),
        title,
        priority: draft.priority,
        done: false,
    });

    draft.title = '';
    draft.priority = 'normal';
    errors.title = '';
    filter.value = 'all';
    search.value = '';
}

function toggleTask(task) {
    task.done = !task.done;
}

function removeTask(id) {
    tasks.value = tasks.value.filter((task) => task.id !== id);
}

function clearDone() {
    tasks.value = tasks.value.filter((task) => !task.done);
}
</script>

<template>
    <div class="shell">
        <header class="topbar">
            <a class="brand" href="/">Tend<span>.</span></a>
            <p class="today">{{ today }}</p>
        </header>

        <main>
            <section class="intro">
                <div>
                    <p class="eyebrow">Task planner</p>
                    <h1>Today’s focus<span>.</span></h1>
                    <p class="lede">
                        A short list for a clear head. Add what matters, finish what you
                        can, and let the rest wait.
                    </p>
                </div>

                <aside class="progress" aria-label="Progress summary">
                    <div class="progress-head">
                        <p class="progress-value">{{ progress }}<span>%</span></p>
                        <p class="progress-meta">{{ doneCount }} of {{ tasks.length }} done</p>
                    </div>
                    <div
                        class="progress-track"
                        role="progressbar"
                        aria-label="Tasks completed"
                        :aria-valuenow="progress"
                        aria-valuemin="0"
                        aria-valuemax="100"
                    >
                        <span
                            class="progress-fill"
                            :style="{ transform: `scaleX(${progress / 100})` }"
                        ></span>
                    </div>
                    <p class="progress-foot">
                        {{ openCount ? openCount + ' left to go' : 'All clear. Nicely done.' }}
                    </p>
                </aside>
            </section>

            <section class="shell-card" aria-label="Add a task">
                <div class="card">
                    <form class="compose" @submit.prevent="addTask" novalidate>
                        <div class="compose-row" :class="{ invalid: errors.title }">
                            <label class="sr-only" for="task-title">New task</label>
                            <input
                                id="task-title"
                                v-model="draft.title"
                                type="text"
                                maxlength="80"
                                placeholder="What needs doing?"
                                autocomplete="off"
                                :aria-invalid="Boolean(errors.title)"
                                :aria-describedby="errors.title ? 'task-error' : undefined"
                                @input="errors.title = ''"
                            />
                            <button class="add" type="submit">
                                <span>Add task</span>
                                <span class="add-icon" aria-hidden="true">
                                    <svg viewBox="0 0 16 16">
                                        <path d="M3 8h9M8.5 3.5 13 8l-4.5 4.5" />
                                    </svg>
                                </span>
                            </button>
                        </div>

                        <div class="compose-meta">
                            <div class="priorities" role="group" aria-label="Priority">
                                <span class="priority-label">Priority</span>
                                <label
                                    v-for="option in priorities"
                                    :key="option.value"
                                    class="priority"
                                    :class="['priority-' + option.value, { active: draft.priority === option.value }]"
                                >
                                    <input
                                        v-model="draft.priority"
                                        class="sr-only"
                                        type="radio"
                                        name="priority"
                                        :value="option.value"
                                    />
                                    <span class="priority-dot"></span>
                                    {{ option.label }}
                                </label>
                            </div>

                            <p v-show="errors.title" id="task-error" class="error">
                                {{ errors.title }}
                            </p>
                        </div>
                    </form>
                </div>
            </section>

            <section class="shell-card board" aria-label="Tasks">
                <div class="card">
                    <div class="toolbar">
                        <div class="filters" role="group" aria-label="Filter tasks">
                            <button
                                v-for="option in filters"
                                :key="option.value"
                                type="button"
                                :class="{ active: filter === option.value }"
                                @click="filter = option.value"
                            >
                                {{ option.label }}
                            </button>
                        </div>

                        <label class="search">
                            <span class="sr-only">Search tasks</span>
                            <svg viewBox="0 0 16 16" aria-hidden="true">
                                <circle cx="7" cy="7" r="4.25" />
                                <path d="m10.5 10.5 3 3" />
                            </svg>
                            <input v-model="search" type="search" placeholder="Search" />
                        </label>
                    </div>

                    <TransitionGroup v-if="visibleTasks.length" name="task" tag="ul" class="list">
                        <TaskItem
                            v-for="task in visibleTasks"
                            :key="task.id"
                            :task="task"
                            @toggle="toggleTask(task)"
                            @remove="removeTask(task.id)"
                        />
                    </TransitionGroup>

                    <div v-else class="empty">
                        <p class="empty-title">
                            {{ tasks.length ? 'Nothing matches.' : 'A clear page.' }}
                        </p>
                        <p>
                            {{
                                tasks.length
                                    ? 'Try a different filter or search term.'
                                    : 'Add your first task above and begin.'
                            }}
                        </p>
                    </div>

                    <footer v-show="doneCount" class="list-foot">
                        <span>{{ doneCount }} completed</span>
                        <button type="button" class="clear" @click="clearDone">
                            Clear completed
                        </button>
                    </footer>
                </div>
            </section>
        </main>

        <footer class="page-foot">
            <span>Laravel + Vue 3</span>
            <span>One task at a time.</span>
        </footer>
    </div>
</template>
