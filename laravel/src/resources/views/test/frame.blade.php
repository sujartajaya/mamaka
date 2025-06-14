<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Modal Form</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

  <!-- Button to open modal -->
  <button 
    onclick="openModal()"
    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
  >
    Tambah Data
  </button>

  <!-- Modal Background -->
  <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <!-- Modal Box -->
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
      <h2 class="text-xl font-semibold mb-4">Form Data</h2>
      
      <form id="dataForm">
        <!-- Nama -->
        <div class="mb-4">
          <label class="block text-gray-700">Nama</label>
          <input type="text" name="nama" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required />
        </div>
        <!-- Email -->
        <div class="mb-4">
          <label class="block text-gray-700">Email</label>
          <input type="email" name="email" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required />
        </div>
        <!-- Telepon -->
        <div class="mb-4">
          <label class="block text-gray-700">Telepon</label>
          <input type="tel" name="telepon" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required />
        </div>
        <!-- Alamat -->
        <div class="mb-4">
          <label class="block text-gray-700">Alamat</label>
          <textarea name="alamat" rows="3" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-2">
          <button 
            type="button" 
            onclick="closeModal()" 
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition"
          >
            Cancel
          </button>
          <button 
            type="submit" 
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition"
          >
            Save
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    const modal = document.getElementById('modal');
    const form = document.getElementById('dataForm');

    function openModal() {
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeModal() {
      modal.classList.remove('flex');
      modal.classList.add('hidden');
      form.reset();
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const data = new FormData(form);
      const values = Object.fromEntries(data.entries());
      console.log('Data disimpan:', values);
      closeModal();
    });
  </script>
</body>
</html>
