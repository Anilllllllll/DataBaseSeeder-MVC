# Quick Reference & Code Snippets

## 🚀 Quick Start Commands

```bash
# Navigate to project
cd unit6

# Run migrations
php artisan migrate

# Seed sample data
php artisan db:seed --class=TaskSeeder

# Start development server
php artisan serve

# Run tests
php artisan test tests/Feature/TaskPriorityUpdateTest.php

# Clear cache if needed
php artisan view:clear
```

---

## 📋 API Usage Examples

### Create a Task via Code

```php
use App\Models\Task;

// Method 1: Using create()
$task = Task::create([
    'title' => 'Fix login bug',
    'description' => 'Users cannot log in with email',
    'priority' => 2, // High
]);

// Method 2: Using new + save()
$task = new Task();
$task->title = 'Implement dark mode';
$task->description = 'Add dark mode toggle to settings';
$task->priority = 4; // Low
$task->save();

// Method 3: Using factory (for testing)
$task = Task::factory()->create();
```

### Update Priority Programmatically

```php
use App\Models\Task;
use App\Models\TaskLog;

$task = Task::find(1);
$oldPriority = $task->priority;

// Update the priority
$task->update(['priority' => 5]);

// Log the change (if priority changed)
if ($oldPriority !== $task->priority) {
    TaskLog::create([
        'task_id' => $task->id,
        'old_priority' => $oldPriority,
        'new_priority' => $task->priority,
    ]);
}
```

### Retrieve Task with History

```php
// Get a single task
$task = Task::find(1);

// Get all logs for a task
$logs = $task->logs; // Returns collection of TaskLog
$logs = $task->logs()->orderBy('created_at', 'desc')->get(); // Latest first

// Loop through history
foreach ($task->logs as $log) {
    echo "Priority changed from {$log->old_priority} to {$log->new_priority} on {$log->created_at}";
}

// Count changes
$changeCount = $task->logs()->count();

// Get latest change
$lastChange = $task->logs()->latest()->first();
```

### Query Examples

```php
use App\Models\Task;
use App\Models\TaskLog;

// Find all critical priority tasks
$criticalTasks = Task::where('priority', 1)->get();

// Find tasks by priority range
$urgentTasks = Task::whereIn('priority', [1, 2])->get();

// Find tasks updated today
$todaysTasks = Task::whereDate('updated_at', today())->get();

// Get tasks with most priority changes
$mostChangedTasks = Task::withCount('logs')
    ->orderBy('logs_count', 'desc')
    ->limit(10)
    ->get();

// Find tasks never updated (priority never changed)
$neverChangedTasks = Task::doesnthave('logs')->get();

// Get all logs
$allLogs = TaskLog::with('task')->orderBy('created_at', 'desc')->paginate(15);

// Find logs for specific priority level
$logsToUrgent = TaskLog::where('new_priority', 1)->get();
```

---

## 🎯 HTTP Request Examples

### Create Task (POST /tasks)

```bash
POST /tasks HTTP/1.1
Content-Type: application/x-www-form-urlencoded

title=Fix+authentication&description=Users+cannot+login&priority=2
```

### Update Priority (PUT /task/1/priority)

```bash
PUT /task/1/priority HTTP/1.1
Content-Type: application/x-www-form-urlencoded
X-HTTP-Method-Override: PUT

priority=4
```

### Using cURL

```bash
# Create task
curl -X POST http://localhost:8000/tasks \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "title=New Task&description=Task description&priority=3"

# Update priority
curl -X PUT http://localhost:8000/task/1/priority \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "priority=5"
```

---

## 🔧 Blade Template Usage

### Display Priority Dropdown (Create/Edit)

```blade
<form method="POST" action="{{ route('tasks.store') }}">
    @csrf

    <div>
        <label>Priority</label>
        <x-priority-dropdown :priority="old('priority', 3)" />
    </div>
</form>
```

### Display Read-Only Priority (Show/List)

```blade
<td>
    <x-priority-dropdown :priority="$task->priority" :disabled="true" />
</td>
```

### Update Priority Inline

```blade
<form action="{{ route('task.updatePriority', $task) }}" method="POST">
    @csrf
    @method('PUT')

    <x-priority-dropdown :priority="$task->priority" />
    <button type="submit">Update</button>
</form>
```

### Display Priority History

```blade
@if ($task->logs->count() > 0)
    <table>
        <thead>
            <tr>
                <th>From</th>
                <th>To</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($task->logs as $log)
                <tr>
                    <td>
                        <x-priority-dropdown :priority="$log->old_priority" :disabled="true" />
                    </td>
                    <td>
                        <x-priority-dropdown :priority="$log->new_priority" :disabled="true" />
                    </td>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
```

---

## 🧪 Testing Code

### Basic Test

```php
public function test_priority_update(): void
{
    $task = Task::factory()->create(['priority' => 2]);

    $response = $this->put(route('task.updatePriority', $task), [
        'priority' => 4,
    ]);

    $this->assertEquals(4, $task->fresh()->priority);
    $response->assertRedirect();
}
```

### Validation Test

```php
public function test_invalid_priority(): void
{
    $task = Task::factory()->create();

    $response = $this->put(route('task.updatePriority', $task), [
        'priority' => 10, // Invalid
    ]);

    $response->assertSessionHasErrors('priority');
}
```

### Logging Test

```php
public function test_change_is_logged(): void
{
    $task = Task::factory()->create(['priority' => 2]);

    $this->put(route('task.updatePriority', $task), ['priority' => 5]);

    $log = TaskLog::latest()->first();
    $this->assertEquals(2, $log->old_priority);
    $this->assertEquals(5, $log->new_priority);
}
```

---

## 🎨 Styling Reference

### Priority Colors in CSS

```css
.priority-1 {
    background-color: #dc3545;
    color: white;
} /* Red - Critical */
.priority-2 {
    background-color: #fd7e14;
    color: white;
} /* Orange - High */
.priority-3 {
    background-color: #ffc107;
    color: #333;
} /* Yellow - Medium */
.priority-4 {
    background-color: #28a745;
    color: white;
} /* Green - Low */
.priority-5 {
    background-color: #6c757d;
    color: white;
} /* Gray - Minimal */
```

### Tailwind Classes Used

```
bg-blue-500   - Primary button
bg-red-500    - Delete button
bg-green-500  - Update button
bg-yellow-500 - Edit button
bg-gray-500   - Secondary button
hover:opacity-80
rounded-md
shadow-md
```

---

## 📊 Database Queries

### Raw SQL Examples

```sql
-- Get task with most changes
SELECT t.id, t.title, COUNT(tl.id) as change_count
FROM tasks t
LEFT JOIN task_logs tl ON t.id = tl.task_id
GROUP BY t.id
ORDER BY change_count DESC
LIMIT 1;

-- Get priority change trend for a task
SELECT
    DATE(created_at) as date,
    new_priority as priority,
    COUNT(*) as changes
FROM task_logs
WHERE task_id = 1
GROUP BY DATE(created_at)
ORDER BY DATE(created_at);

-- Get all tasks with latest priority
SELECT t.*, tl.new_priority as latest_priority
FROM tasks t
LEFT JOIN task_logs tl ON t.id = tl.task_id
WHERE tl.created_at = (
    SELECT MAX(created_at) FROM task_logs WHERE task_id = t.id
) OR tl.id IS NULL;
```

---

## 🐛 Debugging Tips

### Check if Model Binding Works

```php
// In route
Route::put('/task/{task}/priority', function(Task $task) {
    dd($task); // Dumps the Task model
});
```

### Verify Relationships

```php
$task = Task::find(1);

// Check if relationship is loaded
dd($task->logs); // Should show TaskLog collection

// Check relationship count
dd($task->logs()->count());

// Verify foreign key
dd($task->logs()->first()->task_id === $task->id);
```

### Test Validation

```php
// In Tinker or test
use App\Models\Task;

$task = Task::first();

// Simulate invalid request
$validator = validator([
    'priority' => 'invalid'
], [
    'priority' => 'required|integer|between:1,5'
]);

dd($validator->fails()); // true
dd($validator->errors()); // Shows errors
```

---

## 📱 Common Workflows

### Scenario 1: Create and Log Priority Change

```php
// Create task
$task = Task::create([
    'title' => 'New Feature',
    'priority' => 4,
]);

// Later, update priority
$task->update(['priority' => 2]);

// Log the change
TaskLog::create([
    'task_id' => $task->id,
    'old_priority' => 4,
    'new_priority' => 2,
]);

// View history
echo $task->logs()->count(); // 1 change logged
```

### Scenario 2: Generate Report of Priority Changes

```php
$tasks = Task::withCount('logs as priority_changes')
    ->orderBy('priority_changes', 'desc')
    ->get();

foreach ($tasks as $task) {
    echo "{$task->title}: {$task->priority_changes} changes\n";
}
```

### Scenario 3: Revert Priority

```php
$task = Task::find(1);
$lastLog = $task->logs()->latest()->first();

if ($lastLog) {
    $task->update(['priority' => $lastLog->old_priority]);

    // Create new log for the revert
    TaskLog::create([
        'task_id' => $task->id,
        'old_priority' => $lastLog->new_priority,
        'new_priority' => $lastLog->old_priority,
    ]);
}
```

---

## 🔗 Route Helpers

### Generate Route URLs in Views

```blade
<!-- Show form -->
{{ route('tasks.create') }}          <!-- /tasks/create -->

<!-- Update form -->
{{ route('tasks.update', $task) }}   <!-- /tasks/1 -->

<!-- Priority update form -->
{{ route('task.updatePriority', $task) }}  <!-- /task/1/priority -->

<!-- View task -->
{{ route('tasks.show', $task) }}     <!-- /tasks/1 -->

<!-- Edit task -->
{{ route('tasks.edit', $task) }}     <!-- /tasks/1/edit -->

<!-- Delete form -->
{{ route('tasks.destroy', $task) }}  <!-- /tasks/1 (DELETE) -->
```

---

## 📝 Validation Rules Reference

```php
// In controller
$validated = $request->validate([
    'title' => 'required|string|max:255',           // Required, string, max 255 chars
    'description' => 'nullable|string',             // Optional, string if provided
    'priority' => 'required|integer|between:1,5',   // Required, int, 1-5 only
]);

// Custom error messages
$messages = [
    'priority.between' => 'Priority must be between 1 (Critical) and 5 (Minimal)',
    'title.required' => 'Every task needs a title',
];

$validated = $request->validate(
    [/* rules */],
    $messages
);
```

---

## 🎯 Controller Method Reference

```php
// TaskController methods
TaskController::index()           // GET /tasks
TaskController::create()          // GET /tasks/create
TaskController::store()           // POST /tasks
TaskController::show($task)       // GET /tasks/{task}
TaskController::edit($task)       // GET /tasks/{task}/edit
TaskController::update()          // PUT /tasks/{task}
TaskController::updatePriority()  // PUT /task/{task}/priority ⭐
TaskController::destroy($task)    // DELETE /tasks/{task}
```

---

## 💡 Pro Tips

1. **Use Route Names**: Always use `route('name', params)` instead of hardcoded URLs
2. **Validate Early**: Do validation in controller or form request
3. **Use Relationships**: Load related data with `with()` to avoid N+1 queries
4. **Component Reuse**: Use the priority dropdown component everywhere
5. **Log Changes**: Always log when priority changes for audit trail
6. **Test First**: Write tests before implementing features
7. **Use Factories**: Generate test data with factories, not fixtures
8. **Eager Load**: Use `with('logs')` to avoid N+1 queries

---

## 📞 Support

For issues or questions:

1. Check the main `TASK_MANAGER_README.md` for comprehensive docs
2. Review the test file for usage examples
3. Check Laravel documentation at https://laravel.com/docs
4. Run `php artisan tinker` for interactive testing

---

**Last Updated**: June 9, 2026  
**Laravel Version**: 11  
**PHP Version**: 8.2+
