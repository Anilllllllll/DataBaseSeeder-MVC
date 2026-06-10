# BASIC SIMPLE CODE - NO OVERENGINEERING

---

## 1️⃣ ROUTE

**File: routes/web.php**

```php
Route::put('/task/{task}/priority', [TaskController::class, 'updatePriority']);
```

---

## 2️⃣ CONTROLLER METHOD

**File: app/Http/Controllers/TaskController.php**

```php
public function updatePriority(Request $request, Task $task)
{
    $task->priority = $request->priority;
    $task->save();

    return back();
}
```

That's it. Just update and save.

---

## 3️⃣ SIMPLE FORM IN VIEW

**File: resources/views/tasks/index.blade.php**

```blade
@foreach($tasks as $task)
    <div>
        <h3>{{ $task->title }}</h3>
        <p>Priority: {{ $task->priority }}</p>

        <form action="{{ route('task.updatePriority', $task) }}" method="POST">
            @csrf
            @method('PUT')

            <select name="priority">
                <option value="1">1 - Critical</option>
                <option value="2">2 - High</option>
                <option value="3">3 - Medium</option>
                <option value="4">4 - Low</option>
                <option value="5">5 - Minimal</option>
            </select>

            <button type="submit">Update</button>
        </form>
    </div>
@endforeach
```

---

## 4️⃣ BASIC MIGRATION

**File: database/migrations/create_tasks_table.php**

```php
public function up()
{
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->integer('priority')->default(1);
        $table->timestamps();
    });
}
```

---

## 5️⃣ BASIC MODEL

**File: app/Models/Task.php**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'priority'];
}
```

---

## 6️⃣ BASIC CONTROLLER

**File: app/Http/Controllers/TaskController.php**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Show all tasks
    public function index()
    {
        $tasks = Task::all();
        return view('tasks.index', compact('tasks'));
    }

    // Create task form
    public function create()
    {
        return view('tasks.create');
    }

    // Save new task
    public function store(Request $request)
    {
        Task::create([
            'title' => $request->title,
            'priority' => $request->priority ?? 3
        ]);

        return redirect('/tasks');
    }

    // Update priority - MAIN METHOD
    public function updatePriority(Request $request, Task $task)
    {
        $task->priority = $request->priority;
        $task->save();

        return back();
    }
}
```

---

## 7️⃣ BASIC CREATE VIEW

**File: resources/views/tasks/create.blade.php**

```blade
<form action="{{ route('tasks.store') }}" method="POST">
    @csrf

    <input type="text" name="title" placeholder="Task title" required>

    <select name="priority">
        <option value="1">1 - Critical</option>
        <option value="2">2 - High</option>
        <option value="3">3 - Medium</option>
        <option value="4">4 - Low</option>
        <option value="5">5 - Minimal</option>
    </select>

    <button type="submit">Create Task</button>
</form>
```

---

## ✅ THAT'S ALL

No:

- ❌ TaskLog model
- ❌ Logging table
- ❌ History tracking
- ❌ Blade components
- ❌ Fancy styling
- ❌ Complex tests
- ❌ Foreign keys

Just:

- ✅ Task model
- ✅ Update priority
- ✅ Simple form
- ✅ Basic controller

---

## RUN IT

```bash
cd unit6
php artisan migrate
php artisan serve
```

Then visit: http://localhost:8000/tasks

Create task → Update priority → Done

---

**Simple, basic, working. No overengineering.**
