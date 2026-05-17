<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiava HR Compliance Cloud</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 min-h-screen">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="text-center text-white max-w-2xl">
            <h1 class="text-5xl font-bold mb-4">Kiava HR</h1>
            <p class="text-xl text-slate-300 mb-8">Healthcare Compliance Management System</p>
            <p class="text-slate-400 mb-8">Streamlined compliance tracking for healthcare organizations</p>
            
            @if (Auth::check())
                <a href="{{ route('dashboard') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition duration-200">
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('auth.login') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition duration-200">
                    Sign In
                </a>
            @endif
        </div>
    </div>
</body>
</html>
