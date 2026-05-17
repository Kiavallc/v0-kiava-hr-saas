@extends('auth.layout')

@section('title', 'Change Password')

@section('content')
    <p class="text-slate-600 text-center mb-6 text-sm">
        You must change your password to continue
    </p>

    <form action="{{ route('auth.force-password-change.update') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-2">New Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
                autofocus
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                placeholder="••••••••"
            >
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Confirm Password</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                required
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password_confirmation') border-red-500 @enderror"
                placeholder="••••••••"
            >
        </div>

        <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
        >
            Change Password
        </button>
    </form>
@endsection
