# ONLY THE CODE - NOTHING ELSE

## ROUTE

```php
Route::put('/task/{task}/priority', [TaskController::class, 'updatePriority']);
```

## CONTROLLER METHOD

```php
public function updatePriority(Request $request, Task $task)
{
    $task->priority = $request->priority;
    $task->save();
    return back();
}
```

## MODEL

```php
class Task extends Model
{
    protected $fillable = ['title', 'priority'];
}
```

## MIGRATION

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

## FORM IN VIEW

```blade
<form action="{{ route('task.updatePriority', $task) }}" method="POST">
    @csrf
    @method('PUT')

    <select name="priority">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
    </select>

    <button>Update</button>
</form>
```

---

**That's it. Copy these 5 pieces of code. Done.**
