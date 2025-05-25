<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tabel Responsif dengan Tailwind</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-7xl mx-auto">
    <div class="mb-4">
      <input type="text" id="searchInput" placeholder="Cari..." 
        class="w-full p-2 border border-gray-300 rounded-lg shadow-sm" 
        onkeyup="searchTable()" />
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-6 py-3 text-left font-medium text-gray-700">Nama</th>
            <th class="px-6 py-3 text-left font-medium text-gray-700">Email</th>
            <th class="px-6 py-3 text-left font-medium text-gray-700">Kota</th>
          </tr>
        </thead>
        <tbody id="dataTable" class="divide-y divide-gray-200">
          <tr>
            <td class="px-6 py-4">Andi</td>
            <td class="px-6 py-4">andi@example.com</td>
            <td class="px-6 py-4">Jakarta</td>
          </tr>
          <tr>
            <td class="px-6 py-4">Budi</td>
            <td class="px-6 py-4">budi@example.com</td>
            <td class="px-6 py-4">Bandung</td>
          </tr>
          <tr>
            <td class="px-6 py-4">Citra</td>
            <td class="px-6 py-4">citra@example.com</td>
            <td class="px-6 py-4">Surabaya</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    function searchTable() {
      const input = document.getElementById("searchInput").value.toLowerCase();
      const rows = document.getElementById("dataTable").getElementsByTagName("tr");
      for (let i = 0; i < rows.length; i++) {
        let rowText = rows[i].textContent.toLowerCase();
        rows[i].style.display = rowText.includes(input) ? "" : "none";
      }
    }
  </script>
</body>
</html>
