@extends('layout.appv1')
@section('jsscript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
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
            class="max-w-sm mn-w-[150px] p-2 text-gray-900 bg-gradient-to-r from-green-200 via-green-400 to-green-500 hover:bg-gradient-to-br focus:ring-4 focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2" onClick="openModal()">Add</button>
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
<!-- Modal Input data -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <!-- Modal Box -->
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
      <h2 class="text-xl font-semibold mb-4">User Profile</h2>
      
      <form id="dataForm" onsubmit="handleSubmit(event)">
        @csrf
        <div class="mb-4">
          <label class="block text-gray-700">Name</label>
          <input id="frmname" type="text" name="name" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Session timeout</label>
          <input id="frmsessiontimeout" type="text" name="session-timeout" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="1d2h1m"/>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Rate Limit</label>
          <input id="frmratelimit" type="text" name="rate-limit" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="RX/TX" />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Shared Users</label>
          <input id="frmsharedusers" type="text" name="shared-users" rows="3" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="1" value="1" />
        </div>
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
<!-- End Modal -->
<!-- form delete -->
<form id="frmdelete">
    @csrf
    <input type="hidden" name="_method" value="delete" />
</form>
<!-- end form delete -->
<script>
    const tableBody = document.getElementById('tableBody');
    const pageInfo = document.getElementById('pageInfo');
    const searchInput = document.getElementById('searchInput');

    const rowsPerPage = 10;
    let currentPage = 1;
    let data = [];
    let filteredData = [];
    let edit = false;
    let id_user = 0;

    const modal = document.getElementById('modal');
    const form = document.getElementById('dataForm');
    const frm_name = document.getElementById('frmname');
    const frm_sessiontimeout = document.getElementById('frmsessiontimeout');
    const frm_ratelimit = document.getElementById('frmratelimit');
    const frm_sharedusers = document.getElementById('frmsharedusers');

    function openModal() {
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeModal() {
      modal.classList.remove('flex');
      modal.classList.add('hidden');
      form.reset();
    }

    async function confirmDelete(id) {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undo!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626', // Tailwind red-600
            cancelButtonColor: '#6b7280', // Tailwind gray-500
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        });
        if (result.isConfirmed) {
          deleteUserprofile(id)
          getUserProfiles();
        }
    }

    async function deleteUserprofile(id) {
        try {
            const url = `{{ route('user.profile')}}/${id}`;
            const frmmac = document.getElementById('frmdelete');
            frmmac.method = 'POST';
            frmmac.action = url;
            frmmac.submit();
        } catch (error) {
            console.error('Error get data: ', error);
        }
    }

    async function handleSubmit(event) {
      event.preventDefault();

      // const form = document.getElementById("myForm");
      const formData = new FormData(form); // Ambil semua data dari form

      try {
        if (edit) {
          const response = await fetch(`/user/profile/${id_user}`, {
            method: "POST",
            body: formData // Tidak perlu set Content-Type
          });
          // const result = await response.json();
          // console.log(result);
        } else {
          const response = await fetch("<?php echo route('post.user.profile');?>", {
            method: "POST",
            body: formData // Tidak perlu set Content-Type
          });
        }
        // const result = await response.json(); // Bisa juga .json() kalau response-nya JSON
        // console.log(result);
        getUserProfiles();
        closeModal();
      } catch (error) {
        console.error("Gagal:", error);
        alert("Terjadi kesalahan saat mengirim data.");
      }
    }

    async function updateUserProfile(id) {
      edit = true;
      id_user = id;
      try {
        const response = await fetch("/profiles/"+id);
        const result = await response.json(); 
        frm_name.value = result['userprofile'][0]['name'];
        frm_sessiontimeout.value = (result['userprofile'][0]['session-timeout'] !== undefined)? result['userprofile'][0]['session-timeout'] : "";
        frm_ratelimit.value = (result['userprofile'][0]['rate-limit'] !== undefined)? result['userprofile'][0]['rate-limit'] : "";
        frm_sharedusers.value = result['userprofile'][0]['shared-users'];
        openModal();
        // console.log(result['userprofile'][0]['name']);
      } catch (error) {
        console.error("Gagal:", error);
        alert("Terjadi kesalahan saat mengirim data.");
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
          <td class="px-4 py-2">${row['name']}</td>
          <td class="px-4 py-2">${(row['session-timeout'] !== undefined) ? row['session-timeout'] : "-"}</td>
          <td class="px-4 py-2">${(row['rate-limit']  !== undefined) ? row['rate-limit'] : "-"}</td>
          <td class="px-4 py-2" style="text-align: center;">${row['shared-users']}</td>
          <td class="px-4 py-2">
            <div class="flex space-x-2">
                <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400" onClick="updateUserProfile('${row['.id']}')">
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

    function updatePaginationButtons() {
        document.getElementById('prevBtn').disabled = currentPage === 1;
        document.getElementById('nextBtn').disabled = currentPage === Math.ceil(filteredData.length / rowsPerPage);
    }

    async function getUserProfiles() {
        try {
            const response = await fetch('<?php echo route('show.user.profile'); ?>');
            const userprofiles = await response.json();
            data =  userprofiles['userprofile'];
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
                    item['name'].toLowerCase().includes(searchTerm)
                );
                currentPage = 1;
                renderTable(currentPage);
                updatePaginationButtons();
            });
        } catch (error) {
            console.error('Error get data: ', error);
        }
        
    }

    getUserProfiles();

</script>
@endsection