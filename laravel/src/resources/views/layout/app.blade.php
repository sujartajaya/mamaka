<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar dengan Tailwind</title>
    @vite('resources/css/app.css')
</head>
<body>
    <nav class="bg-blue-600 p-4 text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#" class="text-xl font-bold">Brand</a>
            <ul class="hidden md:flex space-x-6">
                <li><a href="#" class="hover:underline">Home</a></li>
                <li><a href="#" class="hover:underline">About</a></li>
                <li><a href="#" class="hover:underline">Services</a></li>
                <li><a href="#" class="hover:underline">Contact</a></li>
            </ul>
            <button class="md:hidden text-white" id="menu-btn">
                &#9776;
            </button>
        </div>
    </nav>
    <script>
        document.getElementById('menu-btn').addEventListener('click', function() {
            alert('Menu button clicked!');
        });
    </script>
</body>
</html>
