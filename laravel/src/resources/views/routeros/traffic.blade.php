@extends('layout.appv1')
@section('tools')
<div class="w-full text-white text-center py-4 mt-6">
        @if($traffic == 'wan')
		<h2>WAN TRAFFIC</h2>
        @endif
        @if($traffic == 'guest')
		<h2>GUEST TRAFFIC</h2>
        @endif
        <h4 id="outul">Last update:</h4>
</div>
<div class="max-w-7xl mx-auto">
    <div id="output" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6 mt-6">
    </div>
</div>
<script>

    async function loadBoxesFromURL() {
      @if($traffic == 'wan')
      const url = "{{ route('get.traffic','wan') }}";
      const imgBaseUrl = 'https://222.165.249.230/graphs/iface/ether1/';
      @endif
      @if($traffic == 'guest')
      const url = "{{ route('get.traffic','guest') }}";
      const imgBaseUrl = 'https://222.165.249.230/graphs/iface/VLAN%2D50/';
      @endif
      const output = document.getElementById('output');
      const outul = document.getElementById('outul');

      outul.innerHTML = '⏳ Loading....';
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
          const divtraffic = document.createElement('div');
          divtraffic.classList.add('bg-white', 'rounded-2xl', 'shadow-md', 'p-6', 'hover:shadow-xl', 'transition', 'duration-300');
          // Ubah semua img src di dalam box
          clone.querySelectorAll('img').forEach(img => {
            const originalSrc = img.getAttribute('src') || '';
            if (!originalSrc.startsWith('http')) {
              img.src = imgBaseUrl + originalSrc;
            }
          });

          clone.classList.add('p-4', 'bg-gray-50', 'border', 'rounded');
          divtraffic.appendChild(clone);
          output.appendChild(divtraffic);
        });
      } catch (error) {
        output.innerHTML = `<p class="text-red-600">Gagal memuat data dari URL: ${error.message}</p>`;
        console.error(error);
      }
    }
    loadBoxesFromURL();
</script>
@endsection
