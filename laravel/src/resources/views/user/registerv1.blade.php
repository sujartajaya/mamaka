<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Form Input</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Input Form</h2>
    
    <form id="userForm" class="space-y-4" method="post" action="{{ route('store.admin.user')}}">
        @csrf
      <!-- Name -->
      <div>
        <label class="block text-gray-700 font-medium">Name</label>
        <input type="text" name="name" required class="w-full mt-1 p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <!-- Email -->
      <div>
        <label class="block text-gray-700 font-medium">Email</label>
        <input type="email" name="email" required class="w-full mt-1 p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <!-- Username -->
      <div>
        <label class="block text-gray-700 font-medium">Username</label>
        <input type="text" name="username" required class="w-full mt-1 p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <!-- Role Option -->
      <div>
        <label class="block text-gray-700 font-medium">Role</label>
        <select name="type" required class="w-full mt-1 p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">-- Pilih Role --</option>
          <option value="user">User</option>
          <option value="operator">Operator</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <!-- Password -->
      <div>
        <label class="block text-gray-700 font-medium">Password</label>
        <input type="password" name="password" id="password" required class="w-full mt-1 p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>

      <!-- Confirm Password -->
      <div>
        <label class="block text-gray-700 font-medium">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required class="w-full mt-1 p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        <p id="error" class="text-red-600 text-sm mt-1 hidden">Password tidak cocok</p>
      </div>

      <!-- Submit -->
      <div>
        <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700">Submit</button>
      </div>
    </form>
  </div>

  <script>
    // const form = document.getElementById('userForm');
    // const password = document.getElementById('password');
    // const confirm_password = document.getElementById('confirm_password');
    // const error = document.getElementById('error');

    // form.addEventListener('submit', function (e) {
    //   if (password.value !== confirm_password.value) {
    //     e.preventDefault();
    //     error.classList.remove('hidden');
    //   } else {
    //     error.classList.add('hidden');
    //     // Submit logic here (optional: alert or fetch)
    //     alert("Form berhasil dikirim!");
    //   }
    // });
  </script>

</body>
</html>
