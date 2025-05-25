<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Card 3 Kolom Responsif</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

  <div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-center">Daftar Card</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      
      <!-- Card 1 -->
      <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition duration-300">
        <img src="https://via.placeholder.com/400x200" alt="Gambar 1" class="rounded-xl mb-4 w-full object-cover h-48">
        <h2 class="text-xl font-semibold mb-2">Judul Card 1</h2>
        <p class="text-gray-600 mb-4">Deskripsi singkat untuk card ini. Menjelaskan isi atau fitur utama.</p>
        <button class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 transition">Selengkapnya</button>
      </div>

      <!-- Card 2 -->
      <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition duration-300">
        <img src="https://via.placeholder.com/400x200" alt="Gambar 2" class="rounded-xl mb-4 w-full object-cover h-48">
        <h2 class="text-xl font-semibold mb-2">Judul Card 2</h2>
        <p class="text-gray-600 mb-4">Deskripsi singkat untuk card ini. Menjelaskan isi atau fitur utama.</p>
        <button class="bg-green-600 text-white px-4 py-2 rounded-xl hover:bg-green-700 transition">Selengkapnya</button>
      </div>

      <!-- Card 3 -->
      <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition duration-300">
        <img src="https://via.placeholder.com/400x200" alt="Gambar 3" class="rounded-xl mb-4 w-full object-cover h-48">
        <h2 class="text-xl font-semibold mb-2">Judul Card 3</h2>
        <p class="text-gray-600 mb-4">Deskripsi singkat untuk card ini. Menjelaskan isi atau fitur utama.</p>
        <button class="bg-red-600 text-white px-4 py-2 rounded-xl hover:bg-red-700 transition">Selengkapnya</button>
      </div>

    </div>
  </div>

</body>
</html>
