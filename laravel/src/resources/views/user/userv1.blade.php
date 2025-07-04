@extends('layout.appv1')
@section('jsscript')
<script src="/build/assets/sweetalert2@11.js"></script>
@endsection
@section('content')
<div class="container mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">User Admin</h2>
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
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
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

<!-- Modal -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-md mx-auto p-6 rounded-xl shadow-lg relative">
      <h2 class="text-xl font-semibold mb-4">Form Input Data</h2>
      
      <form id="userForm" class="space-y-4">
        @csrf()
        <div>
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input id="name" type="text" name="name" class="w-full mt-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300" required />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input id="email" type="email" name="email" class="w-full mt-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300" required />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Username</label>
          <input id="username" type="text" name="username" class="w-full mt-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300" required />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Type</label>
          <select id="type" name="type" class="w-full mt-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
            <option value="operator">Operator</option>
          </select>
        </div>

        <div id="div_pwd" class="">
          <label id="label_pwd" class="block text-sm font-medium text-gray-700">Password</label>
          <input id="password" type="password" name="password" class="w-full mt-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300" required />
        </div>

        <div id="div_conf_pwd" class="">
          <label id="label_comfirm_pwd" class="block text-sm font-medium text-gray-700">Confirm Password</label>
          <input id="confirm_password" type="password" name="confirm_password" class="w-full mt-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300" required />
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
    const tableBody = document.getElementById('tableBody');
    const pageInfo = document.getElementById('pageInfo');
    const searchInput = document.getElementById('searchInput');

    const rowsPerPage = 10;
    let currentPage = 1;
    let data = [];
    let filteredData = [];
    let edit_user = false;
    let id_user = 0;

    function openModal() {
      document.getElementById('modal').classList.remove('hidden');
      document.getElementById('modal').classList.add('flex');

      document.getElementById('password').classList.remove('hidden');
      document.getElementById('confirm_password').classList.remove('hidden');
      document.getElementById('div_pwd').classList.remove('hidden');
      document.getElementById('div_conf_pwd').classList.remove('hidden');
      edit_user = false;
    }

    function closeModal() {
      document.getElementById('modal').classList.add('hidden');
      document.getElementById('modal').classList.remove('flex');
      document.getElementById('div_pwd').classList.remove('hidden');
      document.getElementById('div_conf_pwd').classList.remove('hidden');
      document.getElementById('userForm').reset();

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
          <td class="px-4 py-2">${row['username']}</td>
          <td class="px-4 py-2">${row['email']}</td>
          <td class="px-4 py-2">${row['type']}</td>
          <td class="px-4 py-2">
            <div class="flex space-x-2">
                <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400" onClick="updateUser('${row['id']}')">
                    Edit
                </button>
                <button
                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400" onClick="confirmDelete('${row['id']}')">
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

    async function updateUser(id) {
      id_user = id;
      try {
          const response = await fetch("/user/"+id);
          const user = await response.json();
          document.getElementById('name').value = user['name'];
          document.getElementById('email').value = user['email'];
          document.getElementById('username').value = user['username'];
          document.getElementById('type').value = user['type'];
          document.getElementById('password').classList.add('hidden');
          document.getElementById('confirm_password').classList.add('hidden');
          document.getElementById('div_pwd').classList.add('hidden');
          document.getElementById('div_conf_pwd').classList.add('hidden');
          // openModal();
          document.getElementById('modal').classList.remove('hidden');
          document.getElementById('modal').classList.add('flex');
          edit_user = true;
      } catch (error) {
        console.error('Error get data: ', error);
      }
    }

    async function handleSubmit(event) {
        event.preventDefault();
        const formData = new FormData(document.getElementById('userForm'));
        console.log(formData);
        try {
          if (edit_user === false) {
            const response = await fetch("<?php echo route('store.admin.user');?>", {
                method: "POST",
                body: formData // Tidak perlu set Content-Type
            });
            const result = await response.json(); 
            console.log(result['msg']);
            getUsers();
          } else {
            const response = await fetch(`/user/${id_user}`, {
                method: "POST",
                headers: { 'Content-Type': 'application/json'},
                body: JSON.stringify({name:document.getElementById('name').value, email:document.getElementById('email').value, username:document.getElementById('username').value, type:document.getElementById('type').value, _token: '{!! csrf_token() !!}'})
            });
            const result = await response.json(); 
            console.log(result['msg']);
            getUsers();
          }
            
        } catch (error) {
            console.error("Gagal:", error);
        }
        closeModal();
        // Reset form
        document.getElementById('userForm').reset();
    }

    document.getElementById('userForm').addEventListener('submit', async function(e) {
      e.preventDefault();
        const formData = new FormData(this);
        try {
          if (edit_user === false) {
            const response = await fetch("<?php echo route('store.admin.user');?>", {
                method: "POST",
                body: formData // Tidak perlu set Content-Type
            });
            const result = await response.json();
            if (result.error === true) {
                let msg_name = (result.msg.name !== undefined)? result.msg.name[0] : "";
                let msg_email = (result.msg.email !== undefined)? result.msg.email[0] : "";
                let msg_username = (result.msg.username !== undefined)? result.msg.username[0] : "";
                let msg_password = (result.msg.password !== undefined)? result.msg.password[0] : "";
                let msg_confirm_password = (result.msg.confirm_password !== undefined)? result.msg.confirm_password[0] : "";
                let msg_print = "";
                if (msg_name === "") { msg_print = ""} else msg_print = msg_username;
                if (msg_email !== "") {msg_print = msg_print + "<br>"+msg_email};
                if (msg_username !== "") {msg_print = msg_print + "<br>"+msg_username};
                if (msg_password !== "") {msg_print = msg_print + "<br>"+msg_password};
                if (msg_confirm_password !== "") {msg_print = msg_print + "<br>"+msg_confirm_password};
                Swal.fire({
                    title: 'Warning',
                    html: `Confirm Data:${msg_print}`,
                    icon: 'warning',
                    confirmButtonText: 'Close',
                    customClass: {
                        popup: 'rounded-xl shadow-lg',
                        title: 'text-xl font-bold',
                        confirmButton: 'bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded mt-4'
                    }
                });
            } else {
                closeModal();
                this.reset();
                getUsers();
            }
          }
        } catch (error) {
            console.error("Gagal:", error);
        }
    });

    async function getUsers()
    {
        try {
            const response = await fetch('<?php echo route('get.admin.users'); ?>');
            const users = await response.json();
            data =  users['users'];
            filteredData =  [...data];
            renderTable();
            
        } catch (error) {
            console.error('Error get data: ', error);
        }
    }
    getUsers();
</script>
@endsection