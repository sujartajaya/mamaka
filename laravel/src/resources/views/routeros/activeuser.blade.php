@extends('layout.appv1')
@section('jsscript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
@section('content')
<div class="container mx-auto bg-white p-6 rounded-lg shadow-md">
  <h2 class="text-2xl font-bold mb-4">Active User</h2>
  @if(session()->get('messages'))
  <div id="flashMessage"
    class="fixed top-4 left-1/2 transform -translate-x-1/2 w-auto px-4 py-3 rounded-lg shadow-lg text-white text-center bg-red-500 z-50">
    <span id="flashText">
      {{ session()->get('messages') }}
    </span>
  </div>
  @endif
  <div class="overflow-x-auto flex w-full max-w-wd bg-white rounded-lg  justify-start items-center mt-2 mb-4 space-x-2">
    <input type="text" id="searchInput" placeholder="Search..."
      class="max-w-sm mn-w-[150px] mb-4 p-2 border border-gray-300 rounded" />
    <button id="btnAddmac"
      class="max-w-sm mn-w-[150px] p-2 text-gray-900 bg-gradient-to-r from-blue-200 via-blue-400 to-blue-500 hover:bg-gradient-to-br focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2" onClick="addMacBinding('F0:D5:BF:F8:8C:5C')">Test Mac</button>
  </div>
  <div
    class="overflow-x-auto flex flex-col w-full max-w-wd bg-white rounded-lg shadow-lg justify-center items-center mt-2">
    <table class="min-w-full divide-gray-200 divide-y">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">User</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Server</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Address</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mac Add</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Uptime</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bytes In</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bytes Out</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
        </tr>
      </thead>
      <tbody id="tableBody" class="bg-white divide-y divide-gray-200">
        <!-- Baris data akan di-generate oleh JavaScript -->
      </tbody>
    </table>
  </div>

  <div class="mt-4 flex justify-between items-center">
    <button id="prevBtn" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Previous</button>
    <span id="pageInfo" class="text-gray-600"></span>
    <button id="nextBtn" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Next</button>
  </div>
</div>
<form id="frmmac">
  @csrf
  <input id="mac_add" type="hidden" name="mac"/>
  <input id="comment" type="hidden" name="comment"/>
  <input id="bindtype" type="hidden" name="type" value="bypassed"/>
</form>
<script>
    const tableBody = document.getElementById('tableBody');
    const pageInfo = document.getElementById('pageInfo');
    const searchInput = document.getElementById('searchInput');
    const mac_add = document.getElementById('mac_add');
    const comment = document.getElementById('comment');
    const frmmac = document.getElementById('frmmac');

    const rowsPerPage = 10;
    let currentPage = 1;
    let data = [];
    let filteredData = [];

    function updatePaginationButtons() {
        document.getElementById('prevBtn').disabled = currentPage === 1;
        document.getElementById('nextBtn').disabled = currentPage === Math.ceil(filteredData.length / rowsPerPage);
    }

    async function addMacBinding(mac) {
      frmmac.method = "POST";
      frmmac.action = "/mac";
      mac_add.value = mac;
      try {
        const response = await fetch(`/guest/${mac}`); 
        const guest = await response.json();
        comment.value = (guest['name'] !== undefined ) ? guest['name'] : "Guest Mac Add";
        const result = await Swal.fire({
            title: `Mac Binding\n${(guest['name'] !== undefined) ? guest['name']: "Guest Mac Add"}\n${(guest['email'] !== undefined) ? guest['email'] : "" }`,
            text: `${mac}`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#dc2626', // Tailwind red-600
            cancelButtonColor: '#6b7280', // Tailwind gray-500
            confirmButtonText: 'Yes, add it!',
            cancelButtonText: 'Cancel'
        });
        if (result.isConfirmed) {
            frmmac.submit();
        }
      } catch (error) {
        console.error('Error get data: ', error);
      }
    }

    function renderTable(page = 1) {
      const start = (page - 1) * rowsPerPage;
      const end = start + rowsPerPage;
      const pageData = filteredData.slice(start, end);
      let i = start + 1;

      tableBody.innerHTML = '';
      pageData.forEach(row => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td class="px-4 py-2">${i}</td>
          <td class="px-4 py-2">${row['user']}</td>
          <td class="px-4 py-2">${row['server']}</td>
          <td class="px-4 py-2">${row['address']}</td>
          <td class="px-4 py-2">${row['mac-address']}</td>
          <td class="px-4 py-2">${row['uptime']}</td>
          <td class="px-4 py-2">${row['bytes-in']}</td>
          <td class="px-4 py-2">${row['bytes-out']}</td>
          <td class="px-4 py-2">
            <div class="flex space-x-2">
                <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400" onClick="addMacBinding('${row['mac-address']}')">
                    Mac Bind
                </button>
            </div>
          </td>
        `;
        tableBody.appendChild(tr);
        i++;
      });

      pageInfo.textContent = `Page ${currentPage} of ${Math.ceil(filteredData.length / rowsPerPage)}`;
    }

    async function getActiveUsers() {
        try {
            const response = await fetch('<?php echo route('get.active.users'); ?>');
            const ativeusers = await response.json();
            data =  ativeusers['activeuser'];
            filteredData =  [...data];
            renderTable();

            document.getElementById('prevBtn').addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderTable(currentPage);
                    updatePaginationButtons();
                }
            });
            document.getElementById('nextBtn').addEventListener('click', () => {
                if (currentPage < Math.ceil(filteredData.length / rowsPerPage)) {
                    currentPage++;
                    renderTable(currentPage);
                    updatePaginationButtons();
                }
            });
            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                filteredData = data.filter(item =>
                    item['mac-address'].toLowerCase().includes(searchTerm) || item['user'].toLowerCase().includes(searchTerm) || item['address'].toLowerCase().includes(searchTerm)
                );
                currentPage = 1;
                renderTable(currentPage);
                updatePaginationButtons();
            });

        } catch (error) {
            console.error('Error get data: ', error);
        }
    }
    getActiveUsers();
    setTimeout(() => {
        document.getElementById('flashMessage').classList.add("hidden");
      }, 3000);
</script>
@endsection