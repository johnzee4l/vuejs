<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('planner', [
    'tasks' => [
        ['id' => 1, 'title' => 'Build the task form and validation', 'priority' => 'high', 'done' => false],
        ['id' => 2, 'title' => 'Add filters, search, and progress', 'priority' => 'normal', 'done' => false],
        ['id' => 3, 'title' => 'Review the project before submitting', 'priority' => 'low', 'done' => false],
        ['id' => 4, 'title' => 'Sketch the planner layout', 'priority' => 'low', 'done' => true],
        ['id' => 5, 'title' => 'Connect Vue to the Laravel page', 'priority' => 'normal', 'done' => true],
    ],
]));
