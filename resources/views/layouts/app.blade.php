<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Kiava HR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//cdn.jsdelivr.net/npm/alpine.js" defer></script>
</head>
<body class="bg-slate-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <nav class="bg-slate-900 text-white w-64 p-6">
            <h1 class="text-2xl font-bold mb-8">Kiava HR</h1>
            
            <div class="space-y-4">
                <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition @routeIs('dashboard') ? 'bg-blue-600' : ''">
                    Dashboard
                </a>
                
                @if(Auth::user()->role === 'employee')
                    <a href="#" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                        My Documents
                    </a>
                    <a href="#" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                        Notifications
                    </a>
                @endif

                @if(in_array(Auth::user()->role, ['hr_admin', 'company_owner', 'super_admin']))
                    <a href="#" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                        Employees
                    </a>
                    <a href="#" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                        Documents
                    </a>
                    <a href="#" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                        Compliance
                    </a>
                @endif

                @if(Auth::user()->role === 'super_admin')
                    <hr class="my-4 border-slate-700">
                    <a href="#" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                        Companies
                    </a>
                    <a href="#" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition">
                        Audit Logs
                    </a>
                @endif
            </div>

            <div class="absolute bottom-6 left-6 right-6">
                <div class="bg-slate-800 rounded-lg p-4 mb-4">
                    <p class="text-sm text-slate-400">Logged in as</p>
                    <p class="font-semibold text-white">{{ Auth::user()->name }}</p>
                </div>
                <form action="{{ route('auth.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                        Logout
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white border-b border-slate-200 shadow-sm px-6 py-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-slate-900">@yield('page-title', 'Dashboard')</h2>
                    <div class="flex items-center space-x-4">
                        <button class="relative p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full"></span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto p-6">
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @vite(['resources/js/bootstrap.js'])
</body>
</html>
