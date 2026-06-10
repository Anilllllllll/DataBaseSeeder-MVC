<!DOCTYPE html>
<html>
<head>
    <title>Tasks</title>
</head>
<body>

<h1>Tasks</h1>
<a href="{{ route('tasks.create') }}">Create Task</a>

@if($tasks->count() > 0)
    @foreach($tasks as $task)
        <div>
            <h3>#{{ $task->id }} - {{ $task->title }}</h3>
            <p>Priority: {{ $task->priority }}/5</p>
            
            <form action="{{ route('task.updatePriority', $task) }}" method="POST">
                @csrf
                @method('PUT')
                
                <select name="priority">
                    <option value="1" @if($task->priority == 1) selected @endif>1</option>
                    <option value="2" @if($task->priority == 2) selected @endif>2</option>
                    <option value="3" @if($task->priority == 3) selected @endif>3</option>
                    <option value="4" @if($task->priority == 4) selected @endif>4</option>
                    <option value="5" @if($task->priority == 5) selected @endif>5</option>
                </select>
                
                <button type="submit">Update</button>
            </form>
        </div>
        <hr>
    @endforeach
@else
    <p>No tasks yet. <a href="{{ route('tasks.create') }}">Create one</a></p>
@endif

</body>
</html>
