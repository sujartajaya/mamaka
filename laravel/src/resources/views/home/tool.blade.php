@extends('layout.appv1')
@section('tools')
<div class="max-w-7xl mx-auto">
    <!-- <h1 class="text-3xl font-bold mb-6 text-center mt-6">Tools</h1> -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
      
      <!-- Card 1 -->
      <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition duration-300">
        <img src="/images/mac-add.png" alt="Mac Add" class="rounded-xl mb-4 w-full object-cover" width="50%" height="50%">
        <h2 class="text-xl font-semibold mb-2">Mac Address Binding</h2>
        <p class="text-gray-600 mb-4">Mac Address Binding helps to ensure that the specified mac address devic can access the internet.</p>
        <a href="<?php echo route('mac'); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 transition">More ...</a>
      </div>

      <!-- Card 2 -->
      <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition duration-300 justify-between">
        <img src="/images/telegram.png" alt="Speed limit" class="rounded-xl mb-4 w-full object-cover" width="100%" height="100%">
        <h2 class="text-xl font-semibold mb-2">Users Telegram</h2>
        <p class="text-gray-600 mb-4">Users Telegram is used to access the system via Telegram, and only verified users are allowed access.</p>
        <a href="{{ route('register.telegram.users') }}" class="bg-green-600 text-white px-4 py-2 rounded-xl hover:bg-green-700 transition">More ...</a>
      </div>

      <!-- Card 3 -->
      <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition duration-300">
        <img src="/images/users.png" alt="Users connection" class="rounded-xl mb-4 w-full object-cover" width="50%" height="50%">
        <h2 class="text-xl font-semibold mb-2">Users connection</h2>
        <p class="text-gray-600 mb-4">The client devices accessing the network after successfully logging in with a username and password</p>
        <a href="{{ route('activeuser') }}" class="bg-red-600 text-white px-4 py-2 rounded-xl hover:bg-red-700 transition">More ...</a>
      </div>

    </div>
  </div>
@endsection
