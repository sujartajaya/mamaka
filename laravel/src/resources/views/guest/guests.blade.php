@extends('layout.appv1')
@section('content')

  <div class="container mx-auto bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Guest Lists</h2>
    
    <div class="overflow-x-auto flex w-full max-w-wd bg-white rounded-lg  justify-start items-center mt-2 mb-4 space-x-2">
                  <input
                      type="text"
                      id="searchInput"
                      placeholder="Search..."
                      class="max-w-sm mn-w-[150px] mb-4 p-2 border border-gray-300 rounded"
                    />
                  <form method="GET" action="{{ route('getguests')}}" class="overflow-x-auto flex w-full max-w-wd bg-white rounded-lg  justify-start items-center space-x-2">
                  @csrf()
                  <input
                      type="date"
                      id="startdate"
                      name="startdate"
                      class="max-w-sm mn-w-[150px] mb-4 p-2 border border-gray-300 rounded"
                    />
                  <input
                      type="date"
                      id="enddate"
                      name="enddate"
                      class="max-w-sm mn-w-[150px] mb-4 p-2 border border-gray-300 rounded"
                    />
                  <button id="submitbtn" type="submit" class="max-w-sm mn-w-[150px] p-2 text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-4 focus:ring-lime-300 dark:focus:ring-lime-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2">Submit</button>
                  <button class="max-w-sm mn-w-[150px] p-2 text-gray-900 bg-gradient-to-r from-blue-200 via-blue-400 to-blue-500 hover:bg-gradient-to-br focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2" onclick="exportToCSV('guests.csv')">Export</button> 
                  </form>
    </div>    
    <div class="overflow-x-auto flex flex-col w-full max-w-wd bg-white rounded-lg shadow-lg justify-center items-center mt-2">
      <table class="min-w-full divide-gray-200 divide-y">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Country</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Os</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Browser</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Device</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Brand</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Model</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">First connect</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Byte in</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Byte out</th>
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
    <input type="hidden" id="url" value="{{ route('getguests') }}" />
    <input type="hidden" id="startdt" value="{{ $data['startdate'] }}" />
    <input type="hidden" id="enddt" value="{{ $data['enddate'] }}" />
    <input type="hidden" id="datajson" value="{{ json_encode($data['guests']) }}" />
  </div>

  <script>
    // const data = Array.from({ length: 11 }, (_, i) => ({
    //   no: i + 1,
    //   nama: `Karyawan ${i + 1}`,
    //   jabatan: ['Staff', 'Manager', 'Supervisor'][i % 3],
    //   departemen: ['HRD', 'IT', 'Marketing'][i % 3]
    // }));
    const startdate = document.getElementById('startdt').value;
    const enddate = document.getElementById('enddt').value;
    const data = <?php echo json_encode($data['guests']); ?>;
    const datajson = document.getElementById('datajson').value;


    if ((startdate === "") || (enddate === "")) { 
      const today = new Date();
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
      const day = String(today.getDate()).padStart(2, '0');
      document.getElementById("startdate").value =`${year}-${month}-01`;
      document.getElementById("enddate").value = `${year}-${month}-${day}`;
    } else {
      document.getElementById("startdate").value = startdate;
      document.getElementById("enddate").value = enddate;
    }
    
    // console.log(`Bulan tgl 1 bulan ini = ${year}-${month}-01`);


    const rowsPerPage = 10;
    let currentPage = 1;
    let filteredData = [...data];

    const tableBody = document.getElementById('tableBody');
    const pageInfo = document.getElementById('pageInfo');
    const searchInput = document.getElementById('searchInput');

    function renderTable(page = 1) {
      const start = (page - 1) * rowsPerPage;
      const end = start + rowsPerPage;
      const pageData = filteredData.slice(start, end);
      let i = start + 1;

      tableBody.innerHTML = '';
      pageData.forEach(row => {
        const tr = document.createElement('tr');
        const gb_in = row.byteinput/1000/1000/1000;
        const gb_out = row.byteoutput/1000/1000/1000;
        tr.innerHTML = `
          <td class="px-4 py-2">${i}</td>
          <td class="px-4 py-2">${row.name}</td>
          <td class="px-4 py-2">${row.email}</td>
          <td class="px-4 py-2">${row.username}</td>
          <td class="px-4 py-2">${row.country_name}</td>
          <td class="px-4 py-2">${row.os_client}</td>
          <td class="px-4 py-2">${row.browser_client}</td>
          <td class="px-4 py-2">${row.device_client}</td>
          <td class="px-4 py-2">${row.brand_client}</td>
          <td class="px-4 py-2">${row.model_client}</td>
          <td class="px-4 py-2">${row.device_type}</td>
          <td class="px-4 py-2">${row.created_at}</td>
          <td class="px-4 py-2">${gb_in.toFixed(6)} GB</td>
          <td class="px-4 py-2">${gb_out.toFixed(6)} GB</td>
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

    function exportToCSV(filename) {
      // console.log(`data length = ${datajson.length}`);
      if (datajson.length >= 0) {
                    const myArr = JSON.parse(datajson);
                    if (Array.isArray(myArr)) {
                        let i = 1;
                        myArr.map(item => {
                        if (typeof item === 'object' && item !== null) {
                            item['no'] = i;
                            }
                            i=i+1;
                            return item;
                         });
                        // console.log(`is array = ${myArr}`);
                    } 
                    const headers = Object.keys(myArr[0]);
                    const rows = myArr.map(obj =>
                        headers.map(header =>
                            `"${obj[header] || ''}"`
                        ).join(','));
                    const csvContent = [headers.join(','), ...rows].join('\n');
                    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = filename;
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
        }
    }

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

    // document.getElementById('submitbtn').addEventListener('click', () => {
    //     const guest_url = document.getElementById('url').value;
    //     console.log(guest_url);
    // });

    searchInput.addEventListener('input', (e) => {
      const searchTerm = e.target.value.toLowerCase();
      filteredData = data.filter(item =>
        item.name.toLowerCase().includes(searchTerm) || item.username.toLowerCase().includes(searchTerm) || item.email.toLowerCase().includes(searchTerm)
     );
      currentPage = 1;
      renderTable(currentPage);
      updatePaginationButtons();
    });


    // Inisialisasi
    renderTable(currentPage);
    updatePaginationButtons();
  </script>


@endsection
