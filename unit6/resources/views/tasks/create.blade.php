<!DOCTYPE html>
<html>
<head>
    <title>Create Task</title>
</head>
<body>

<h1>Create Task</h1>

@if ($errors->any())
    <div>
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form action="{{ route('tasks.store') }}" method="POST">
    @csrf
    
    <label>Title:</label>
    <input type="text" name="title" value="{{ old('title') }}" required />
    
    <label>Description:</label>
    <textarea name="description" rows="4">{{ old('description') }}</textarea>
    
    <label>Priority:</label>
    <select name="priority">
        <option value="1" @if(old('priority') == 1) selected @endif>1</option>
        <option value="2" @if(old('priority') == 2) selected @endif>2</option>
        <option value="3" @if(old('priority') == 3) selected @endif>3</option>
        <option value="4" @if(old('priority') == 4) selected @endif>4</option>
        <option value="5" @if(old('priority') == 5) selected @endif>5</option>
    </select>
    
    <button type="submit">Create</button>
</form>

<a href="{{ route('tasks.index') }}">Back</a>

</body>
</html>
                Create Task
            </button>
            <a href="{{ route('tasks.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
