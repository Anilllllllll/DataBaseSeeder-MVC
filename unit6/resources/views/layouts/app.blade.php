<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager - @yield('title', 'Laravel')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="{{ route('tasks.index') }}" class="text-2xl font-bold">
                    📋 Task Manager
                </a>
                <div class="space-x-4">
                    <a href="{{ route('tasks.index') }}" class="hover:text-blue-200">Tasks</a>
                    <a href="{{ route('tasks.create') }}" class="hover:text-blue-200">New Task</a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-gray-300 text-center py-4 mt-8">
        <p>&copy; 2026 Task Manager. All rights reserved.</p>
    </footer>
</body>
</html>
