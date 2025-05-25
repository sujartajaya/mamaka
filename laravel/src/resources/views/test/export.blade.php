<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data JSON Export CSV</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">
  <div class="bg-white shadow-lg rounded-2xl p-6 w-full max-w-6xl">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold text-gray-800">Data Sidanta dari JSON</h2>
      <button onclick="exportToCSV('sidanta-data.csv')" class="bg-green-600 text-white px-4 py-2 rounded-xl hover:bg-green-700 transition">
        Export CSV
      </button>
    </div>
    <div class="overflow-x-auto">
      <table id="dataTable" class="min-w-full border border-gray-300 text-left">
        <thead class="bg-gray-200 text-gray-700">
          <tr id="tableHead"></tr>
        </thead>
        <tbody id="tableBody" class="text-gray-800"></tbody>
      </table>
    </div>
  </div>

  <script>
    // Data JSON
    const sidantaData = [
      {
        aspek: "Asal",
        siwa_sidanta: "Hindu Siwaisme",
        buda_sidanta: "Buddha Mahayana"
      },
      {
        aspek: "Tuhan Tertinggi",
        siwa_sidanta: "Dewa Siwa",
        buda_sidanta: "Dharma Sunya"
      },
      {
        aspek: "Pendeta",
        siwa_sidanta: "Pedanda Siwa",
        buda_sidanta: "Pedanda Buda / Bhiksu"
      },
      {
        aspek: "Pustaka Suci",
        siwa_sidanta: "Siwa Tattwa, Tutur, Tattwa Jñana",
        buda_sidanta: "Dharma Sunya, Bhuwana Kosa"
      },
      {
        aspek: "Fokus Ajaran",
        siwa_sidanta: "Pemujaan Dewa & Yadnya",
        buda_sidanta: "Meditasi & Kesunyian"
      }
    ];

    // Render Tabel dari JSON
    function renderTable(data) {
      const headRow = document.getElementById('tableHead');
      const body = document.getElementById('tableBody');

      // Generate header
      const headers = Object.keys(data[0]);
      headers.forEach(key => {
        const th = document.createElement('th');
        th.className = 'px-4 py-2 border';
        th.textContent = key.replace(/_/g, ' ').toUpperCase();
        headRow.appendChild(th);
      });

      // Generate body
      data.forEach(row => {
        const tr = document.createElement('tr');
        tr.className = 'border-t';
        headers.forEach(key => {
          const td = document.createElement('td');
          td.className = 'px-4 py-2 border';
          td.textContent = row[key];
          tr.appendChild(td);
        });
        body.appendChild(tr);
      });
    }

    // Export CSV dari JSON
    function exportToCSV(filename) {
      const headers = Object.keys(sidantaData[0]);
      const csvRows = [];

      // Header row
      csvRows.push(headers.map(h => `"${h}"`).join(','));

      // Data rows
      sidantaData.forEach(row => {
        const values = headers.map(h => `"${(row[h] || '').replace(/"/g, '""')}"`);
        csvRows.push(values.join(','));
      });

      const csvString = csvRows.join("\n");
      const blob = new Blob([csvString], { type: 'text/csv' });
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.download = filename;
      link.click();
    }

    // Load data ke tabel saat halaman siap
    window.onload = () => renderTable(sidantaData);
  </script>
</body>
</html>
