@extends('layout.appv1')
@section('content')
<div class="container mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">User Profiles</h2>
    @if(session()->get('messages'))
    <div id="flashMessage"
        class="fixed top-4 left-1/2 transform -translate-x-1/2 w-auto px-4 py-3 rounded-lg shadow-lg text-white text-center bg-red-500 z-50">
        <span id="flashText">
            {{ session()->get('messages') }}
        </span>
    </div>
    @endif
    <div
        class="overflow-x-auto flex w-full max-w-wd bg-white rounded-lg  justify-start items-center mt-2 mb-4 space-x-2">
        <input type="text" id="searchInput" placeholder="Search..."
            class="max-w-sm mn-w-[150px] mb-4 p-2 border border-gray-300 rounded" />
        <button id="btnAddmac"
            class="max-w-sm mn-w-[150px] p-2 text-gray-900 bg-gradient-to-r from-green-200 via-green-400 to-green-500 hover:bg-gradient-to-br focus:ring-4 focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2">Add</button>
    </div>
    <div
        class="overflow-x-auto flex flex-col w-full max-w-wd bg-white rounded-lg shadow-lg justify-center items-center mt-2">
        <table class="min-w-full divide-gray-200 divide-y">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Session time out</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rate limit</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Shared users</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
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
<script>
    const tableBody = document.getElementById('tableBody');
    const pageInfo = document.getElementById('pageInfo');
    const searchInput = document.getElementById('searchInput');

    const rowsPerPage = 10;
    let currentPage = 1;
    let data = [];
    let filteredData = [];

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
          <td class="px-4 py-2">${row['name']}</td>
          <td class="px-4 py-2">${(row['session-timeout'] !== undefined) ? row['session-timeout'] : "-"}</td>
          <td class="px-4 py-2">${(row['rate-limit']  !== undefined) ? row['rate-limit'] : "-"}</td>
          <td class="px-4 py-2" style="text-align: center;">${row['shared-users']}</td>
          <td class="px-4 py-2">
            <div class="flex space-x-2">
                <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400" onClick="addMacBinding('${row['id']}')">
                    Edit
                </button>
                <button
                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400" onClick="confirmDelete('${row['.id']}')">
                    Delete
                </button>
            </div>
          </td>
        `;
        tableBody.appendChild(tr);
        i++;
      });

      pageInfo.textContent = `Page ${currentPage} of ${Math.ceil(filteredData.length / rowsPerPage)}`;
    }

    async function getUserProfiles() {
        try {
            const response = await fetch('<?php echo route('get.user.profile'); ?>');
            const userprofiles = await response.json();
            data =  userprofiles['userprofiles'];
            filteredData =  [...data];
            renderTable();
        } catch (error) {
            console.error('Error get data: ', error);
        }
        
    }

    getUserProfiles();

</script>
@endsection