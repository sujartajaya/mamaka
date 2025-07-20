@extends('layout.appv1')
@section('content')
  <div class="container mx-auto bg-white p-6 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-4">Users Telegram</h1>

    <!-- Search -->
    <input type="text" id="searchInput" placeholder="Search by name or email..." class="mb-4 w-full p-2 border rounded" oninput="renderTable()" />

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto border border-gray-300 text-left" id="userTable">
        <thead class="bg-gray-200">
          <tr>
            <th class="p-2">First Name</th>
            <th class="p-2">Last Name</th>
            <th class="p-2">Username</th>
            <th class="p-2">Verified</th>
            <th class="p-2">Verified at</th>
            <th class="p-2">Action</th>
          </tr>
        </thead>
        <tbody id="tableBody">
          <!-- Dynamic Data -->
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center mt-4">
      <button onclick="prevPage()" class="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600">Previous</button>
      <span id="pageInfo" class="text-sm"></span>
      <button onclick="nextPage()" class="bg-blue-500 text-white px-4 py-1 rounded hover:bg-blue-600">Next</button>
    </div>
  </div>

  <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-md mx-auto p-6 rounded-xl shadow-lg relative">
        <h2 class="text-xl font-semibold mb-4">Form Input Data</h2>
        
        <form id="userForm" class="space-y-4">
            <div>
            <label class="block text-sm font-medium text-gray-700">Verified</label>
            <select id="verified" name="verified" class="w-full mt-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300" required>
                <option value="0">Pending</option>
                <option value="1">Approve</option>
            </select>
            </div>
            <div>
            <label class="block text-sm font-medium text-gray-700">Role</label>
            <select id="role" name="role" class="w-full mt-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            </div>
            <div class="flex justify-end space-x-2 pt-4">
            <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                Cancel
            </button>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700" onClick="handleSubmit(event)">
                Save
            </button>
            </div>
        </form>
        </div>
    </div>

  <script>
    let users = []; // full data from API
    let currentPage = 1;
    let telegram_id = "";
    const rowsPerPage = 5;
    const frmuser = document.getElementById('userForm');
    const verified = document.getElementById('verified');
    const role = document.getElementById('role');
    
    function openModal(id) {
      document.getElementById('modal').classList.remove('hidden');
      document.getElementById('modal').classList.add('flex');
      telegram_id = id;
    }

    function closeModal() {
      document.getElementById('modal').classList.add('hidden');
      document.getElementById('modal').classList.remove('flex');
      frmuser.reset();
    }

    async function handleSubmit(event) {
      event.preventDefault();
      const data_verified = verified.value;
      const data_role = role.value;
      const response = await fetch("<?php echo route('update.telegram.user'); ?>",{
        method: 'POST',
        headers: {
        'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          verified: data_verified,
          role: data_role,
          _token: '{{ csrf_token() }}',
          telegram_id: telegram_id,
        })},
      );
      const usrupdate = await response.json();
      console.log(usrupdate);
      closeModal();
      fetchData();
    }
    // Fetch data from API
    async function fetchData() {
      try {
        const res = await fetch('<?php echo route('get.telegram.users'); ?>');
        users = await res.json();
        renderTable();
      } catch (err) {
        console.error('Fetch error:', err);
      }
    }

    // Render table based on search and pagination
    function renderTable() {
      const searchValue = document.getElementById('searchInput').value.toLowerCase();
      const filteredUsers = users.filter(user =>
        user.first_name.toLowerCase().includes(searchValue) ||
        user.last_name.toLowerCase().includes(searchValue)
      );

      const start = (currentPage - 1) * rowsPerPage;
      const paginatedUsers = filteredUsers.slice(start, start + rowsPerPage);

      const tableBody = document.getElementById('tableBody');
      tableBody.innerHTML = '';

      paginatedUsers.forEach(user => {
        let btn = "";
        if (user.verified === 0) {
            btn = `<button class= "bg-green-500  text-white px-3 py-1 rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400" onClick="openModal(${user.telegram_id})">
                    Verified
                </button>`;
        } else {
            btn = `<button class= "bg-red-500  text-white px-3 py-1 rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400" onClick="openModal(${user.telegram_id})">
                    Approve
                </button>`;
        }
        const row = `<tr class="border-t">
            <td class="p-2">${user.first_name}</td>
            <td class="p-2">${user.last_name}</td>
            <td class="p-2">${user.username}</td>
            <td class="p-2">${user.verified}</td>
            <td class="p-2">${user.verified_at}</td>
            <td class="p-2">
            <div class="flex space-x-2">
                ${btn}
            </div>
            </td>
          </tr>`;
        tableBody.innerHTML += row;
      });

      // Update pagination info
      const pageInfo = document.getElementById('pageInfo');
      const totalPages = Math.ceil(filteredUsers.length / rowsPerPage);
      pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;

      // Disable buttons if needed
      document.querySelector("button[onclick='prevPage()']").disabled = currentPage === 1;
      document.querySelector("button[onclick='nextPage()']").disabled = currentPage >= totalPages;
    }

    function nextPage() {
      currentPage++;
      renderTable();
    }

    function prevPage() {
      currentPage--;
      renderTable();
    }

    // Initialize
    fetchData();
  </script>
@endsection

