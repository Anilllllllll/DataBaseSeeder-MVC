# Task Manager Application

A complete Laravel task management application with priority-based task management and priority change logging.

## Features

### 1. **Task Management**

- Create new tasks with title, description, and priority (1-5)
- View all tasks in a table format
- View individual task details
- Edit task information
- Delete tasks

### 2. **Priority Management**

- Set task priority from 1 (Critical) to 5 (Minimal)
- Update priority via dedicated priority dropdown component
- Color-coded priority levels:
    - 🔴 **Priority 1 (Critical)** - Red
    - 🟠 **Priority 2 (High)** - Orange
    - 🟡 **Priority 3 (Medium)** - Yellow
    - 🟢 **Priority 4 (Low)** - Green
    - ⚫ **Priority 5 (Minimal)** - Gray

### 3. **Priority Change Logging**

- Automatic logging of all priority changes
- `task_logs` table records:
    - `task_id` - Reference to the task
    - `old_priority` - Previous priority value
    - `new_priority` - Updated priority value
    - `created_at` - Timestamp of the change
- View complete change history on task detail page

## Database Schema

### Tasks Table

```sql
CREATE TABLE tasks (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT NULLABLE,
    priority INT DEFAULT 3,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Task Logs Table

```sql
CREATE TABLE task_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    task_id BIGINT NOT NULL FOREIGN KEY REFERENCES tasks(id) ON DELETE CASCADE,
    old_priority INT NOT NULL,
    new_priority INT NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Routes

| Method  | Route                       | Controller                        | Purpose                   |
| ------- | --------------------------- | --------------------------------- | ------------------------- |
| GET     | `/tasks`                    | TaskController@index              | List all tasks            |
| GET     | `/tasks/create`             | TaskController@create             | Show create form          |
| POST    | `/tasks`                    | TaskController@store              | Store new task            |
| GET     | `/tasks/{task}`             | TaskController@show               | View task details         |
| GET     | `/tasks/{task}/edit`        | TaskController@edit               | Show edit form            |
| PUT     | `/tasks/{task}`             | TaskController@update             | Update task               |
| **PUT** | **`/task/{task}/priority`** | **TaskController@updatePriority** | **Update priority & log** |
| DELETE  | `/tasks/{task}`             | TaskController@destroy            | Delete task               |

## Key Implementation Details

### 1. Route Binding with Parameter Names

```php
// Route with explicit binding
Route::put('/task/{task}/priority', [TaskController::class, 'updatePriority']);
```

- Uses implicit route model binding
- `{task}` automatically resolves to Task model by ID

### 2. Priority Dropdown Component (`priority-dropdown.blade.php`)

```php
<x-priority-dropdown :priority="$task->priority" />
```

- Reusable Blade component with customizable styling
- Color-coded display for each priority level
- Optional disabled state for read-only display
- Supports emojis for visual feedback

### 3. Update Priority Method with Validation

```php
public function updatePriority(Request $request, Task $task)
{
    // Validate priority is between 1-5
    $validated = $request->validate([
        'priority' => 'required|integer|between:1,5',
    ]);

    // Only log if priority actually changed
    if ($oldPriority !== $newPriority) {
        // Update task
        $task->update(['priority' => $newPriority]);

        // Log the change
        TaskLog::create([
            'task_id' => $task->id,
            'old_priority' => $oldPriority,
            'new_priority' => $newPriority,
        ]);
    }
}
```

### 4. Model Relationships

```php
// Task Model
public function logs()
{
    return $this->hasMany(TaskLog::class);
}

// TaskLog Model
public function task()
{
    return $this->belongsTo(Task::class);
}
```

## Views Structure

```
resources/views/
├── layouts/
│   └── app.blade.php          # Main layout
├── components/
│   └── priority-dropdown.blade.php  # Priority selector component
└── tasks/
    ├── index.blade.php        # List all tasks
    ├── create.blade.php       # Create form
    ├── edit.blade.php         # Edit form
    └── show.blade.php         # View details + logs
```

## Usage Examples

### Creating a Task

```php
$task = Task::create([
    'title' => 'Fix bug in authentication',
    'description' => 'User login not working',
    'priority' => 2, // High priority
]);
```

### Updating Task Priority

Via the web interface:

1. Go to Tasks list
2. Select new priority from dropdown
3. Click "Update" button
4. Change is automatically logged

Via direct API:

```php
$task->update(['priority' => 4]);
TaskLog::create([
    'task_id' => $task->id,
    'old_priority' => 2,
    'new_priority' => 4,
]);
```

### Viewing Change History

```php
$task = Task::find(1);
$logs = $task->logs; // Get all priority changes

foreach ($logs as $log) {
    echo "Changed from {$log->old_priority} to {$log->new_priority}";
}
```

## Validation Rules

### Priority Validation

- **Required**: Yes
- **Type**: Integer
- **Range**: 1-5
- **Message**: "The priority field must be a number between 1 and 5"

### Title Validation

- **Required**: Yes
- **Type**: String
- **Max Length**: 255 characters

### Description Validation

- **Optional**: Can be null
- **Type**: String
- **Max Length**: Unlimited

## Sample Data

Run the seeder to generate 10 sample tasks:

```bash
php artisan db:seed --class=TaskSeeder
```

Or seed all data:

```bash
php artisan db:seed
```

## Installation Steps

1. **Clone/Setup project**

```bash
cd unit6
```

2. **Install dependencies**

```bash
composer install
npm install
```

3. **Configure environment**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Setup database**

```bash
php artisan migrate
php artisan db:seed --class=TaskSeeder
```

5. **Start server**

```bash
php artisan serve
```

Visit: `http://localhost:8000/tasks`

## Files Created/Modified

### New Files

- `app/Models/Task.php` - Task model with relationships
- `app/Models/TaskLog.php` - TaskLog model for logging changes
- `app/Http/Controllers/TaskController.php` - All task operations
- `database/migrations/2026_06_09_053008_create_tasks_table.php` - Tasks table
- `database/migrations/2026_06_09_053028_create_task_logs_table.php` - Logs table
- `database/factories/TaskFactory.php` - Task factory for seeding
- `database/seeders/TaskSeeder.php` - Task seeder
- `resources/views/layouts/app.blade.php` - Base layout
- `resources/views/components/priority-dropdown.blade.php` - Priority component
- `resources/views/tasks/index.blade.php` - Tasks list
- `resources/views/tasks/create.blade.php` - Create form
- `resources/views/tasks/edit.blade.php` - Edit form
- `resources/views/tasks/show.blade.php` - Task details & logs

### Modified Files

- `routes/web.php` - Added task routes with priority update route
- `database/seeders/DatabaseSeeder.php` - Added TaskSeeder

## API Response Examples

### GET /tasks

```json
[
    {
        "id": 1,
        "title": "Fix authentication bug",
        "description": "User login fails",
        "priority": 2,
        "created_at": "2026-06-09T05:30:08Z",
        "updated_at": "2026-06-09T05:30:08Z"
    }
]
```

### PUT /task/1/priority

Request:

```json
{
    "priority": 4
}
```

Response (Redirect to previous page with success message)

### Priority Change Log

```json
{
    "id": 1,
    "task_id": 1,
    "old_priority": 2,
    "new_priority": 4,
    "created_at": "2026-06-09T06:00:00Z"
}
```

## Error Handling

- **Validation Errors**: Redirected back with error messages
- **Model Not Found**: Returns 404 error (implicit route binding)
- **Unauthorized**: Can be extended with authorization policies
- **Database Errors**: Handled by Laravel framework

## Future Enhancements

- [ ] User authentication and task ownership
- [ ] Task categories/tags
- [ ] Task due dates and reminders
- [ ] Task filtering and search
- [ ] Priority statistics and reports
- [ ] Task assignments to users
- [ ] Task comments and notes
- [ ] Email notifications on priority changes

## Testing the Application

1. Visit `/tasks` to see all tasks
2. Click "+ Create Task" to add a new task
3. Set priority from 1-5
4. On the tasks list, use the dropdown to update priority
5. Click "View" on any task to see the change history
6. Verify the `task_logs` table is being populated

## Troubleshooting

**Database connection error:**

- Ensure SQLite is enabled or MySQL is running
- Check `.env` file database configuration

**Migration fails:**

- Run `php artisan migrate:rollback` then `php artisan migrate`

**Blade component not found:**

- Ensure component file exists at `resources/views/components/priority-dropdown.blade.php`
- Clear view cache: `php artisan view:clear`

**Factory not working:**

- Verify `HasFactory` trait is added to Task model
- Check TaskFactory definition

---

Created: 2026-06-09
Last Updated: 2026-06-09
