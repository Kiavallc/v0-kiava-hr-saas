<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kiava HR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50">
    <nav class="bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-slate-900">Kiava HR</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-slate-600">{{ Auth::user()->name }}</span>
                    <form action="{{ route('auth.logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-slate-600 mb-2">Total Employees</h3>
                <p class="text-3xl font-bold text-slate-900">0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-slate-600 mb-2">Compliance Rate</h3>
                <p class="text-3xl font-bold text-slate-900">0%</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-slate-600 mb-2">Pending Reviews</h3>
                <p class="text-3xl font-bold text-slate-900">0</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Welcome to Kiava HR</h2>
            <p class="text-slate-600">Select an option from the menu above to get started.</p>
        </div>
    </div>
</body>
</html>
