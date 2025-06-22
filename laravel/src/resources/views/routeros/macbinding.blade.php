@extends('layout.appv1')
@section('jsscript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
@section('content')
<div class="container mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Mac Add Binding</h2>
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
            class="max-w-sm mn-w-[150px] p-2 text-gray-900 bg-gradient-to-r from-blue-200 via-blue-400 to-blue-500 hover:bg-gradient-to-br focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2">Add
            Mac</button>
    </div>
    <div
        class="overflow-x-auto flex flex-col w-full max-w-wd bg-white rounded-lg shadow-lg justify-center items-center mt-2">
        <table class="min-w-full divide-gray-200 divide-y">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mac Address</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Disable</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
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

<!-- Modal add mac add -->
<div id="macModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white w-full max-w-4xl rounded-lg shadow-lg">
        <!-- Modal Header -->
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <h2 class="text-xl font-semibold text-gray-800" id="userProfileInfo"></h2>
            <button id="userProfilecloseModalBtn" class="text-gray-600 hover:text-gray-800 focus:outline-none">
                ✖
            </button>
        </div>
        <!-- Modal Body -->
        <div class="relative p-6 flex-auto">
            <form id="formMacAdd">
                @csrf
                <div class="flex flex-wrap -mx-3 mb-4">
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="mac">
                            Mac address
                        </label>
                        <input
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                            id="mac" type="text" placeholder="00:00:00:00:00:00" name="mac">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="type">
                            Type
                        </label>
                        <select
                            class="block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                            id="type" name="type">
                            <option value="blocked">blocked</option>
                            <option value="bypassed">bypassed</option>
                            <option value="regular">regular</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-wrap -mx-3 mb-4">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="comment">
                            Description
                        </label>
                        <input
                            class="block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                            id="comment" type="text" placeholder="Description" name="comment">
                    </div>
                    <div class="w-full md:w-1/2 px-3 hidden" id="frmdisable">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                            for="disabled">
                            Disabled
                        </label>
                        <select
                            class="block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                            id="disabled" name="disabled">
                            <option value="false">No</option>
                            <option value="true">Yes</option>
                        </select>
                    </div>
                </div>
        </div>
        <!-- Modal Footer -->
        <div class="flex items-center justify-end p-6 border-t border-solid border-blueGray-200 rounded-b space-x-2">

            <input id="submitBtn"
                class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400"
                value="" />
            </form>
            <button id="closeBtn"
                class="bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                Close
            </button>
        </div>
    </div>
</div>
<!-- end modal add macc -->

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
    const macModal = document.getElementById('macModal');
    const btnAddmac = document.getElementById('btnAddmac');
    const formAddMac = document.getElementById('formMacAdd');
    const submitBtn = document.getElementById('submitBtn');
    const rowsPerPage = 10;
    let currentPage = 1;
    let data = [];
    let filteredData = [];
    async function editMac(id) {
        try {
            const url = `<?php echo route('mac'); ?>/${id}`;
            const response = await fetch(url);
            const macadd = await response.json();
            const mac = macadd['mac'];
            formEditMacBinding(mac, id);
            closeBtn.addEventListener('click', () => {
                macModal.classList.add('hidden');
            });
        } catch (error) {
            console.error('Error get data: ', error);
        }
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
            deleteMacBingding(id)
        }
    }
    
    async function deleteMacBingding(id) {
        try {
            const url = `{{ route('post.mac')}}/${id}`;
            const frmmac = document.getElementById('frmdelete');
            frmmac.method = 'POST';
            frmmac.action = url;
            frmmac.submit();
        } catch (error) {
            console.error('Error get data: ', error);
        }
    }

    function formEditMacBinding(mac, id) {
        formAddMac.method = 'POST';
        formAddMac.action = `<?php echo route('mac'); ?>/${id}`;
        submitBtn.type = 'submit';
        submitBtn.value = 'Edit';
        document.getElementById('frmdisable').classList.remove('hidden');
        macModal.classList.remove('hidden');
        document.getElementById('mac').value = mac[0]['mac-address'];
        document.getElementById('type').value = mac[0]['type'];
        document.getElementById('comment').value = mac[0]['comment'];
        document.getElementById('disabled').value = mac[0]['disabled'];
    }

    function renderTable(page = 1) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageData = filteredData.slice(start, end);
        let i = start + 1;
        tableBody.innerHTML = '';
        pageData.forEach(row => {
            const rowtype = (row['type'] !== undefined) ? row['type'] : "";
            const rowcomment = (row['comment'] !== undefined) ? row['comment'] : "";
            const tr = document.createElement('tr');
            tr.innerHTML = `
          <td class="px-4 py-2">${i}</td>
          <td class="px-4 py-2">${row['mac-address']}</td>
          <td class="px-4 py-2">${rowtype}</td>
          <td class="px-4 py-2">${row['disabled']}</td>
          <td class="px-4 py-2">${rowcomment}</td>
          <td class="px-4 py-2">
            <div class="flex space-x-2">
                <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400" onClick="editMac('${row['.id']}')">
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

    function formAddMacBinding() {
        formAddMac.method = 'POST';
        formAddMac.action = '<?php echo route('mac'); ?>';
        submitBtn.type = 'submit';
        submitBtn.value = 'Save'
    }

    function updatePaginationButtons() {
        document.getElementById('prevBtn').disabled = currentPage === 1;
        document.getElementById('nextBtn').disabled = currentPage === Math.ceil(filteredData.length / rowsPerPage);
    }
    async function getMac() {
        try {
            const response = await fetch('<?php echo route('macbinding'); ?>');
            const macadd = await response.json();
            data = macadd['macbind'];
            filteredData = [...data];
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
            closeBtn.addEventListener('click', () => {
                macModal.classList.add('hidden');
            });
            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                filteredData = data.filter(item =>
                    item['mac-address'].toLowerCase().includes(searchTerm) || item['type'].toLowerCase()
                    .includes(searchTerm) || item['comment'].toLowerCase().includes(searchTerm)
                );
                currentPage = 1;
                renderTable(currentPage);
                updatePaginationButtons();
            });
            btnAddmac.addEventListener('click', () => {
                macModal.classList.remove('hidden');
                document.getElementById('mac').value = "";
                document.getElementById('type').value = "bypassed";
                document.getElementById('comment').value = "";
                document.getElementById('disabled').value = "false";
                formAddMacBinding();
            });
        } catch (error) {
            console.error('Error get data: ', error);
        }
    }
    getMac();
    setTimeout(() => {
        document.getElementById('flashMessage').classList.add("hidden");
    }, 3000);
</script>
@endsection