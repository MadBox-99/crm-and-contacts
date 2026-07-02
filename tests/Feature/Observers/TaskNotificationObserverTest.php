<?php

declare(strict_types=1);

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Support\Facades\Notification;

it('notifies assignee when a task is created with assigned_to', function (): void {
    Notification::fake();

    $assignee = User::factory()->create();
    $task = Task::factory()->create(['assigned_to' => $assignee->id]);

    Notification::assertSentTo($assignee, TaskAssignedNotification::class, fn (TaskAssignedNotification $notification): bool => $notification->task->id === $task->id);
});

it('notifies new assignee when assigned_to changes', function (): void {
    $originalAssignee = User::factory()->create();
    $newAssignee = User::factory()->create();

    $task = Task::factory()->create(['assigned_to' => $originalAssignee->id]);

    Notification::fake();

    $task->update(['assigned_to' => $newAssignee->id]);

    Notification::assertSentTo($newAssignee, TaskAssignedNotification::class);
    Notification::assertNotSentTo($originalAssignee, TaskAssignedNotification::class);
});

it('does not notify when other fields change', function (): void {
    $assignee = User::factory()->create();
    $task = Task::factory()->create(['assigned_to' => $assignee->id]);

    Notification::fake();

    $task->update(['title' => 'Updated title']);

    Notification::assertNothingSent();
});
