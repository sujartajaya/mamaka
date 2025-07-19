<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
  <title>Ambil Box dari URL</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-6xl mx-auto bg-white p-4 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Hasil dari URL (div.box)</h1>

    <input id="urlInput" type="text" placeholder="Masukkan URL target..." 
           class="w-full p-2 mb-4 border rounded" 
           value="http://localhost/traffic" />

    <button onclick="loadBoxesFromURL()" 
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
      Ambil Data
    </button>
    <div id="outul" class="mt-2 text-pretty">Ini Ul</div>
    <div id="output" class="grid gap-4 mt-6"></div>
  </div>

  <script>
    async function loadBoxesFromURL() {
      const url = document.getElementById('urlInput').value;
      const output = document.getElementById('output');
      const outul = document.getElementById('outul');

      output.innerHTML = '⏳ Memuat...';
      const imgBaseUrl = 'https://222.165.249.230/graphs/iface/ether1/';
      try {
        const response = await fetch(url);
        const htmlText = await response.text();

        const parser = new DOMParser();
        const doc = parser.parseFromString(htmlText, 'text/html');
        const dataul = doc.querySelector('ul')?.textContent;
        const boxes = doc.querySelectorAll('div.box');
        outul.innerHTML = dataul;
        if (boxes.length === 0) {
          output.innerHTML = '<p class="text-red-600">Tidak ditemukan div dengan class "box".</p>';
          return;
        }

        output.innerHTML = '';
        
        boxes.forEach(box => {
          const clone = box.cloneNode(true);

          // Ubah semua img src di dalam box
          clone.querySelectorAll('img').forEach(img => {
            const originalSrc = img.getAttribute('src') || '';
            if (!originalSrc.startsWith('http')) {
              img.src = imgBaseUrl + originalSrc;
            }
          });

          clone.classList.add('p-4', 'bg-gray-50', 'border', 'rounded');
          output.appendChild(clone);
        });
      } catch (error) {
        output.innerHTML = `<p class="text-red-600">Gagal memuat data dari URL: ${error.message}</p>`;
        console.error(error);
      }
    }
  </script>
</body>
</html>
