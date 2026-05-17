@extends('auth.layout')

@section('title', 'Forgot Password')

@section('content')
    <p class="text-slate-600 text-center mb-6 text-sm">
        Enter your email to receive a password reset link
    </p>

    <form action="{{ route('auth.send-reset') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                placeholder="you@example.com"
            >
        </div>

        <button
            type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
        >
            Send Reset Link
        </button>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('auth.login') }}" class="text-sm text-blue-600 hover:text-blue-700">
            Back to login
        </a>
    </div>
@endsection
