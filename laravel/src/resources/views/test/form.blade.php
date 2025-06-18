<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Form Input dengan FormData</title>
  <script>
    async function handleSubmit(event) {
      event.preventDefault();

      const form = document.getElementById("myForm");
      const formData = new FormData(form); // Ambil semua data dari form

      try {
        const response = await fetch("/fetch", {
          method: "POST",
          body: formData // Tidak perlu set Content-Type
        });

        const result = await response.json(); // Bisa juga .json() kalau response-nya JSON
        console.log(result);
        alert("Data berhasil dikirim!");
      } catch (error) {
        console.error("Gagal:", error);
        alert("Terjadi kesalahan saat mengirim data.");
      }
    }
  </script>
  @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <form id="myForm" onsubmit="handleSubmit(event)" class="bg-white p-6 rounded-xl shadow-md w-full max-w-md">
    @csrf
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Form Input</h2>

    <div class="mb-4">
      <label for="name" class="block text-gray-700 font-medium mb-2">Name</label>
      <input type="text" name="name" id="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
    </div>

    <div class="mb-4">
      <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
      <input type="email" name="email" id="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
    </div>

    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition">
      Submit
    </button>
  </form>
</body>
</html>
