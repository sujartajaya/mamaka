@extends('layout.appv1')
@section('content')
    <div class="w-full max-w-sm p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-center text-gray-700">Login</h2>
        @if (session('loginError'))
             <div>
                <h3 class="text-red-700 font-bold">{{ session('loginError') }}</h3>
            </div>
        @endif
        <form class="mt-4" method="POST" action="{{ route('authtenticate') }}">
            <input type="hidden" value="{{ $prev_url }}" name="prev_url"/>
            @csrf()
            <div>
                <label class="block text-sm font-medium text-gray-600">Username</label>
                <input type="text" placeholder="Input your username" class="w-full px-4 py-2 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required autofocus name="username">
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-600">Password</label>
                <input type="password" placeholder="Input your password" class="w-full px-4 py-2 mt-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required name="password">
            </div>
            <button type="submit" class="w-full px-4 py-2 mt-4 text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none">Login</button>
        </form>
    </div>
@endsection
