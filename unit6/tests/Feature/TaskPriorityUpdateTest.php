<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Task;
use App\Models\TaskLog;

class TaskPriorityUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful priority update
     */
    public function test_priority_can_be_updated(): void
    {
        $task = Task::factory()->create(['priority' => 2]);

        $response = $this->put(route('task.updatePriority', $task), [
            'priority' => 4,
        ]);

        $this->assertEquals(4, $task->fresh()->priority);
        $response->assertRedirect();
    }

    /**
     * Test that priority change is logged
     */
    public function test_priority_change_is_logged(): void
    {
        $task = Task::factory()->create(['priority' => 2]);

        $this->put(route('task.updatePriority', $task), [
            'priority' => 5,
        ]);

        $log = TaskLog::latest()->first();
        $this->assertEquals($task->id, $log->task_id);
        $this->assertEquals(2, $log->old_priority);
        $this->assertEquals(5, $log->new_priority);
    }

    /**
     * Test that no log is created if priority doesn't change
     */
    public function test_no_log_created_when_priority_unchanged(): void
    {
        $task = Task::factory()->create(['priority' => 3]);

        $this->put(route('task.updatePriority', $task), [
            'priority' => 3,
        ]);

        $this->assertEquals(0, TaskLog::count());
    }

    /**
     * Test priority validation - priority must be between 1-5
     */
    public function test_priority_validation_lower_bound(): void
    {
        $task = Task::factory()->create(['priority' => 2]);

        $response = $this->put(route('task.updatePriority', $task), [
            'priority' => 0,
        ]);

        $response->assertSessionHasErrors('priority');
        $this->assertEquals(2, $task->fresh()->priority); // Unchanged
    }

    /**
     * Test priority validation - priority upper bound
     */
    public function test_priority_validation_upper_bound(): void
    {
        $task = Task::factory()->create(['priority' => 2]);

        $response = $this->put(route('task.updatePriority', $task), [
            'priority' => 6,
        ]);

        $response->assertSessionHasErrors('priority');
        $this->assertEquals(2, $task->fresh()->priority); // Unchanged
    }

    /**
     * Test priority must be required
     */
    public function test_priority_is_required(): void
    {
        $task = Task::factory()->create(['priority' => 3]);

        $response = $this->put(route('task.updatePriority', $task), []);

        $response->assertSessionHasErrors('priority');
    }

    /**
     * Test all valid priority levels (1-5)
     */
    public function test_all_valid_priority_levels(): void
    {
        $task = Task::factory()->create(['priority' => 1]);

        foreach ([1, 2, 3, 4, 5] as $priority) {
            $this->put(route('task.updatePriority', $task), [
                'priority' => $priority,
            ]);

            $this->assertEquals($priority, $task->fresh()->priority);
        }
    }

    /**
     * Test multiple logs are created for multiple updates
     */
    public function test_multiple_priority_changes_are_logged(): void
    {
        $task = Task::factory()->create(['priority' => 1]);

        $this->put(route('task.updatePriority', $task), ['priority' => 2]);
        $this->put(route('task.updatePriority', $task), ['priority' => 3]);
        $this->put(route('task.updatePriority', $task), ['priority' => 4]);

        $this->assertEquals(3, $task->logs()->count());

        $logs = $task->logs()->orderBy('created_at')->get();
        $this->assertEquals([1, 2, 3], $logs->pluck('old_priority')->toArray());
        $this->assertEquals([2, 3, 4], $logs->pluck('new_priority')->toArray());
    }

    /**
     * Test task with foreign key constraint is properly linked
     */
    public function test_task_log_foreign_key_constraint(): void
    {
        $task = Task::factory()->create(['priority' => 1]);

        $this->put(route('task.updatePriority', $task), ['priority' => 3]);

        $log = TaskLog::first();
        $this->assertNotNull($log->task);
        $this->assertEquals($task->id, $log->task->id);
    }

    /**
     * Test that priority is validated as integer
     */
    public function test_priority_must_be_integer(): void
    {
        $task = Task::factory()->create(['priority' => 2]);

        $response = $this->put(route('task.updatePriority', $task), [
            'priority' => 'high',
        ]);

        $response->assertSessionHasErrors('priority');
    }

    /**
     * Test success message is returned
     */
    public function test_success_message_returned(): void
    {
        $task = Task::factory()->create(['priority' => 2]);

        $response = $this->put(route('task.updatePriority', $task), [
            'priority' => 4,
        ]);

        $response->assertSessionHas('success', 'Priority updated successfully');
    }
}
