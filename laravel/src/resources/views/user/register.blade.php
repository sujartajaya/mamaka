@extends('layout.appv1')
@section('content')
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-4">Register</h2>
        <form action="{{ route('postregister') }}" method="POST" class="space-y-4">
            @csrf()
            <div>
                <label class="block text-gray-700">Name</label>
                <input type="text" name="name" class="w-full p-2 border border-gray-300 rounded mt-1 focus:ring focus:ring-blue-200" required autofocus placeholder="Input your name" value="{{ old('name') }}">
                @error('name')
                    <div class="invalid block text-sm font-medium text-gray-700 dark:text-red-600 mb-2">{{$message}}</div>
                @enderror
            </div>
            <div>
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" class="w-full p-2 border border-gray-300 rounded mt-1 focus:ring focus:ring-blue-200" required placeholder="Input your valid email" value="{{ old('email') }}">
                @error('email')
                    <div class="invalid block text-sm font-medium text-gray-700 dark:text-red-600 mb-2">{{$message}}</div>
                @enderror
            </div>
            <div>
                <label class="block text-gray-700">Username</label>
                <input type="text" name="username" class="w-full p-2 border border-gray-300 rounded mt-1 focus:ring focus:ring-blue-200" required placeholder="Input your username" value="{{ old('username') }}">
                @error('username')
                    <div class="invalid block text-sm font-medium text-gray-700 dark:text-red-600 mb-2">{{$message}}</div>
                @enderror
            </div>
            <div>
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" class="w-full p-2 border border-gray-300 rounded mt-1 focus:ring focus:ring-blue-200" required placeholder="Password">
                @error('password')
                    <div class="invalid block text-sm font-medium text-gray-700 dark:text-red-600 mb-2">{{$message}}</div>
                @enderror
            </div>
            <div>
                <label class="block text-gray-700">Confirm Password</label>
                <input type="password" name="confirm_password" class="w-full p-2 border border-gray-300 rounded mt-1 focus:ring focus:ring-blue-200" required placeholder="Confirm password">
                @error('confirm_password')
                    <div class="invalid block text-sm font-medium text-gray-700 dark:text-red-600 mb-2">{{$message}}</div>
                @enderror
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600 transition">Daftar</button>
        </form>
    </div>
@endsection
