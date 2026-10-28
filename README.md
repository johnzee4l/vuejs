# Tend — Laravel + Vue task planner

A small task planner built to explore core Vue 3 concepts inside a Laravel app. Laravel serves the page and supplies the starting tasks; Vue handles the interface and remembers changes in `localStorage`. No database or login is needed.

## Requirements

- PHP 8.3+ and Composer (Laragon works)
- Node.js 20+ and npm

## Run locally

```bash
composer install
cp .env.example .env        # Windows: copy .env.example .env
php artisan key:generate
npm install
npm run build
```

Serve the project with Laragon (point the site at the `public` folder), or use Laravel's server:

```bash
php artisan serve
```

Open http://127.0.0.1:8000.

For hot reload while editing, run these in two terminals:

```bash
php artisan serve
npm run dev
```

## Vue concepts used

| Concept | Where |
| --- | --- |
| `ref()` | `tasks`, `filter`, and `search` state in `App.vue` |
| `reactive()` | `draft` and `errors` form state |
| Interpolation `{{ }}` | heading date, counts, progress, task text |
| `v-model` | title input, search input, priority radios |
| `v-if` / `v-else` | task list versus empty state |
| `v-show` | validation message, completed-task footer |
| `@click` / event methods | add, toggle, delete, filter, clear completed |
| `computed()` | `visibleTasks`, `doneCount`, `openCount`, `progress` |
| `v-for` | task list, filter buttons, priority options |
| Arrays and objects | task records, form state, option lists |
| Form validation | `addTask()` requires a title of at least 3 characters |
| Dynamic class binding | active filters and priorities, completed rows, invalid form |
| Dynamic style binding | progress bar `scaleX(...)` fill |
| Watchers | deep `watch` saves the task list to `localStorage` |
| Components | `TaskItem.vue` with props and emitted events |

## Files that make up the app

- `routes/web.php` — serves the page and the seed tasks
- `resources/views/planner.blade.php` — HTML shell that mounts Vue
- `resources/js/App.vue` — state, form, filters, computed values, watcher
- `resources/js/components/TaskItem.vue` — task row component
- `resources/css/app.css` — all styling

Tasks are stored in the browser under the `tend.tasks` key. Clear that key in DevTools to restore the Laravel seed list.
