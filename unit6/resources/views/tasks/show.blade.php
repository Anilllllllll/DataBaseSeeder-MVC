@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">{{ $task->title }}</h1>
        <a href="{{ route('tasks.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            ← Back to Tasks
        </a>
    </div>

    <div class="bg-white shadow-md rounded px-6 py-8 mb-6">
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-gray-700 font-bold mb-2">ID</h3>
                <p>{{ $task->id }}</p>
            </div>
            <div>
                <h3 class="text-gray-700 font-bold mb-2">Current Priority</h3>
                <x-priority-dropdown :priority="$task->priority" disabled />
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-gray-700 font-bold mb-2">Description</h3>
            <p class="whitespace-pre-wrap">{{ $task->description ?? 'No description provided' }}</p>
        </div>

        <div class="mb-6">
            <h3 class="text-gray-700 font-bold mb-2">Created</h3>
            <p>{{ $task->created_at->format('Y-m-d H:i:s') }}</p>
        </div>

        <div class="mb-6">
            <h3 class="text-gray-700 font-bold mb-2">Last Updated</h3>
            <p>{{ $task->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>

        <div class="flex gap-4">
            <a href="{{ route('tasks.edit', $task) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                Edit Task
            </a>
            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Delete Task
                </button>
            </form>
        </div>
    </div>

    @if ($task->logs->count() > 0)
        <div class="bg-white shadow-md rounded px-6 py-8">
            <h2 class="text-2xl font-bold mb-4">Priority Change History</h2>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-left">ID</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Old Priority</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">New Priority</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Changed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($task->logs as $log)
                            <tr class="hover:bg-gray-100">
                                <td class="border border-gray-300 px-4 py-2">{{ $log->id }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <x-priority-dropdown :priority="$log->old_priority" disabled />
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <x-priority-dropdown :priority="$log->new_priority" disabled />
                                </td>
                                <td class="border border-gray-300 px-4 py-2">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            No priority changes recorded yet.
        </div>
    @endif
</div>
@endsection
