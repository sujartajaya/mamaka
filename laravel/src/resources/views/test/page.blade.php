<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagination Dinamis</title>
    @vite('resources/css/app.css')
</head>
<body class="flex justify-center items-center h-screen bg-gray-100">
    <div class="p-6 bg-white shadow-lg rounded-lg">
        <div id="pagination" class="flex space-x-2"></div>
    </div>
    
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        function updatePage(page) {
                urlParams.set("page", page);
                window.history.pushState({}, "", `?${urlParams.toString()}`);
                createPagination(100, 5);
        }
        
        function createPagination(totalPages, perPage) {
            const paginationContainer = document.getElementById("pagination");
            paginationContainer.innerHTML = "";
            
            // const urlParams = new URLSearchParams(window.location.search);
            let currentPage = parseInt(urlParams.get("page")) || 1;
            
            // function updatePage(page) {
            //     urlParams.set("page", page);
            //     window.history.pushState({}, "", `?${urlParams.toString()}`);
            //     createPagination(totalPages, perPage);
            // }
            
            if (currentPage > 1) {
                paginationContainer.innerHTML += `<button class="px-3 py-1 border rounded bg-gray-200" onclick="updatePage(${currentPage - 1})">Prev</button>`;
            }
            
            for (let i = 1; i <= totalPages; i++) {
                if (i <= 10 ) {
                    paginationContainer.innerHTML += `<button class="px-3 py-1 border rounded ${i === currentPage ? 'bg-blue-500 text-white' : 'bg-gray-200'}" onclick="updatePage(${i})">${i}</button>`;
                 } else if ((i >= totalPages-10) ) {
                    paginationContainer.innerHTML += `<button class="px-3 py-1 border rounded ${i === currentPage ? 'bg-blue-500 text-white' : 'bg-gray-200'}" onclick="updatePage(${i})">${i}</button>`;
                 }
                
            }
            
            if (currentPage < totalPages) {
                paginationContainer.innerHTML += `<button class="px-3 py-1 border rounded bg-gray-200" onclick="updatePage(${currentPage + 1})">Next</button>`;
            }
        }
        
        createPagination(100, 5);
    </script>
</body>
</html>
