@extends('layout.appv1')
@section('content')
    <div class="container mx-auto px-4 py-8 relative">
        <h1 class="text-3xl font-bold text-center mb-8">USER LISTS</h1>
        <!-- Responsive Table Container -->
        <div class="flex items-center justify-start mb-4 space-x-2">
            <div class="w-full max-w-sm min-w-[150px] relative">
                <div class="w-full">
                    <form action="#" method="GET">
                        <input name="search"
                        class="bg-white w-full pr-11 h-10 pl-3 py-2 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded transition duration-200 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md"
                        placeholder="Search ...."
                        />
                        <button
                        class="absolute h-8 w-8 right-1 top-1 my-auto px-2 flex items-center bg-white rounded "
                        type="submit"
                        >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-8 h-8 text-slate-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        </button>
                    </form>
                </div>
            </div>
          <!--  <div class="justify-normal relative"> -->
                <button onclick="openModal()" type="button" class="text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-4 focus:ring-lime-300 dark:focus:ring-lime-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2">Add</button>
                <button onClick="export_data('data.csv')" type="button" class="text-white bg-gradient-to-r from-teal-400 via-teal-500 to-teal-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-teal-300 dark:focus:ring-teal-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">Export</button>
                
            <!-- </div> -->
        </div>
        
        <div class="flex flex-col w-full max-w-wd bg-white rounded-lg shadow-lg justify-center items-center mt-2">
            <table class="min-w-full bg-white" id="myTable">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-4 border-b border-gray-300 text-left text-sm font-semibold text-gray-700">No</th>
                        <th class="py-3 px-4 border-b border-gray-300 text-left text-sm font-semibold text-gray-700">Name</th>
                        <th class="py-3 px-4 border-b border-gray-300 text-left text-sm font-semibold text-gray-700">Email</th>
                        <th class="py-3 px-4 border-b border-gray-300 text-left text-sm font-semibold text-gray-700">Username</th>
                        <th class="py-3 px-4 border-b border-gray-300 text-left text-sm font-semibold text-gray-700">Role</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
            
        </div>
        <input type="hidden" id="url" value="{{ route('users') }}" />
        <input type="hidden" id="totalResults" value="{{ $data['totalrows'] }}" />
        <input type="hidden" id="resultsPerPage" value="{{ $data['perpage'] }}" />
        <input type="hidden" id="totalPages" />

        <!-- start -->
            <div class="flex items-center space-x-2 mt-6 justify-center">
                <button id="prevPage" class="bg-gray-200 px-3 py-2 text-gray-500 hover:text-white hover:bg-red-500 rounded">&lt;</button>
                <div id="pagination" class="flex space-x-3"></div>
                <button id="nextPage" class="bg-gray-200 px-3 py-2 text-gray-500 hover:text-white hover:bg-red-500 rounded">&gt;</button>
            </div>
        <!--- end -->
    </div>


    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-bold mb-4">Input Data</h2>
            
            <!-- Form -->
            <form id="inputForm">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" id="name" name="name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg mr-2 hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Submit</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function  () {
            const paginationContainer = document.getElementById("pagination");
            const prevButton = document.getElementById("prevPage");
            const nextButton = document.getElementById("nextPage");
            const users_url = document.getElementById('url').value;
            const urlParams = new URLSearchParams(window.location.search);

            let totalResults = document.getElementById('totalResults').value;
            let resultsPerPage = document.getElementById('resultsPerPage').value;
            let totalPages = Math.ceil(totalResults / resultsPerPage);
            let currentPage = 1;

            const queryString = window.location.search;
            const dataqr = new URLSearchParams(queryString);
            const search = dataqr.get('search');
            const get_url = users_url+"?"+dataqr;
            get_users(get_url);

            renderPagination();

            console.log(`totalResults awal = ${totalResults}`);
            console.log(`resultPerpage awal = ${resultsPerPage}`);
            console.log(`totalPages awal = ${totalPages}`);
            
           

            function renderPagination() {
                paginationContainer.innerHTML = "";
                let pages = [];
                
                if (totalPages <= 6) {
                    for (let i = 1; i <= totalPages; i++) {
                        pages.push(i);
                    }
                } else {
                    if (currentPage <= 3) {
                        pages = [1, 2, 3, "...", totalPages - 1, totalPages];
                    } else if (currentPage >= totalPages - 2) {
                        pages = [1, 2, "...", totalPages - 2, totalPages - 1, totalPages];
                    } else {
                        pages = [1, "...", currentPage - 1, currentPage, currentPage + 1, "...", totalPages];
                    }
                }

                pages.forEach(page => {
                    const button = document.createElement("button");
                    button.textContent = page;
                    button.classList.add("px-3", "py-2", "rounded");
                    
                    if (page === "...") {
                        button.classList.add("bg-gray-200", "text-gray-500");
                        button.disabled = true;
                    } else {
                        button.classList.add("hover:bg-red-500", "hover:text-white");
                        if (page === currentPage) {
                            button.classList.add("bg-red-500", "text-white");
                            /** perubahan data table */
                            urlParams.set("page", page);
                            window.history.pushState({}, "", `?${urlParams.toString()}`);
                            const queryString = window.location.search;
                            const dataqr = new URLSearchParams(queryString);

                            const search = dataqr.get('search');

                            const get_url = users_url+"?"+dataqr;
                            get_users(get_url);
                            // console.log("search = " + dataqr.get('search')+"\nPage = "+dataqr.get('page')+"\nUrl = "+get_url);

                        } else {
                            button.classList.add("bg-gray-200", "text-gray-500");
                        }
                        button.addEventListener("click", () => {
                            currentPage = page;
                            renderPagination();
                        });
                    }

                    paginationContainer.appendChild(button);
                });

                prevButton.disabled = currentPage === 1;
                nextButton.disabled = currentPage === totalPages;
            }
            
            prevButton.addEventListener("click", () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderPagination();
                }
            });

            nextButton.addEventListener("click", () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderPagination();
                }
            });

        });

        async function get_users(url) {
            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' },
                })
                const result = await response.json();
                const users = result.users;
                

                // totalResults.value = await result.rows;
                // resultsPerPage.value = await result.perpage;
                // totalPages.value = await Math.ceil(totalResults.value / resultsPerPage.value);

                // currentPage = 1;
                console.log(result);
                // console.log(`totalResults = ${totalResults.value}`);
                // console.log(`resultPerpage = ${resultsPerPage.value}`);
                // console.log(`totalPages = ${totalPages.value}`);
                // const data = Object.entries(result);
                // const data = Object.values(result);

                // const data = Object.keys(result).map(key => result[key]);;

                // const userdata = Object.keys(data).map(key => data[key]);;;

                // const current_page = userdata[0][1];
                // const arr_users = userdata[1][1];
                // const last_page = userdata[4][1];
                // const total_rows = userdata[12][1];
                // totalPages = last_page;

                // arr_users.forEach((user) => {
                //     let usr = `Nama = ${user.name}`;
                //     console.log(usr);
                // })

                //console.log(arr_users);

                hapus();
                /** update table data */
                let myTable = document.getElementById('myTable').getElementsByTagName('tbody')[0];
                let row;
                let cell1;
                let cell2;
                let cell3;
                let cell4;
                let cell5;
                let i = 0;
                users.forEach((user) =>{ 
                    // console.log(`Name = ${user.name} email = ${user.email} username = ${user.username} type = ${user.type}`);
                    i = i+1;
                    row = myTable.insertRow();
                    cell1 = row.insertCell(0);
                    cell2 = row.insertCell(1);
                    cell3 = row.insertCell(2);
                    cell4 = row.insertCell(3);
                    cell5 = row.insertCell(4);
                    cell1.classList.add("py-3", "px-4","border-b", "border-gray-300", "text-center", "text-gray-700");
                    cell1.innerHTML = i;
                    cell2.classList.add("py-3", "px-4","border-b", "border-gray-300", "text-left", "text-gray-700");
                    cell2.innerHTML = user.name;
                    cell3.classList.add("py-3", "px-4","border-b", "border-gray-300", "text-left", "text-gray-700");
                    cell3.innerHTML = user.email;
                    cell4.classList.add("py-3", "px-4","border-b", "border-gray-300", "text-left", "text-gray-700");
                    cell4.innerHTML = user.username;
                    cell5.classList.add("py-3", "px-4","border-b", "border-gray-300", "text-left", "text-gray-700");
                    cell5.innerHTML = user.type;
                })
            } catch (error) {
                console.log(error);
            }
        }

        function hapus() {
            var tableHeaderRowCount = 1;
            var table = document.getElementById('myTable');
            var rowCount = table.rows.length;
            for (var i = tableHeaderRowCount; i < rowCount; i++) {
                table.deleteRow(tableHeaderRowCount);
            }
        }

        // Function to open the modal
        function openModal() {
            document.getElementById('modal').classList.remove('hidden');
        }

        // Function to close the modal
        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }

        // Handle form submission
        document.getElementById('inputForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Get form values
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            // Display the values in console (or you can send them to a server)
            console.log('Nama:', name);
            console.log('Email:', email);
            console.log('Username:', username);
            console.log('Password:', password);

            // Close the modal after submission
            closeModal();
        });

    </script>
@endsection
