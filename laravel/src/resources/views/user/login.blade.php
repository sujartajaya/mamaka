<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-900 flex justify-center items-center min-h-screen">
    <div class="w-full max-w-md bg-gray-800 p-6 rounded-lg shadow-lg text-white">
        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
        
        <form action="#" method="POST" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium">Email</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-teal-400">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-teal-400">
            </div>
            
            <button type="submit" class="w-full bg-teal-400 text-gray-900 font-bold py-2 rounded-lg hover:bg-teal-500">Login</button>
        </form>

        <p class="text-center text-sm text-gray-400 mt-4">Belum punya akun? <a href="#" class="text-teal-400 hover:underline">Daftar</a></p>
    </div>
</body>
</html>
