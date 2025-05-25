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
        async function updatePage(page) {
                urlParams.set("page", page);
                //window.history.pushState({}, "", `?${urlParams.toString()}`);
                let currentURL = "http://localhost:8000/testguest?page="+page;
                console.log(currentURL);
                   try {
                        const response = await fetch(currentURL)
                        const result = await response.json();
                        console.log(result);
                   } catch (error) {
                        console.log(error);
                   } 
              
                createPagination(2, 3);
        }

        function createPagination(totalPages, perPage) {
            const paginationContainer = document.getElementById("pagination");
            paginationContainer.innerHTML = "";
            
            
            let currentPage = parseInt(urlParams.get("page")) || 1;
            let startPage = 1;
            let endPage = totalPages;
            
            if (totalPages > 100) {
                let pageGroup = Math.ceil(currentPage / 10);
                startPage = (pageGroup - 1) * 10 + 1;
                endPage = Math.min(startPage + 9, totalPages);
            }
            
            
            
            if (currentPage > 1) {
                paginationContainer.innerHTML += `<button class="px-3 py-1 border rounded bg-gray-200" onclick="updatePage(${currentPage - 1})">Prev</button>`;
            }
            
            for (let i = startPage; i <= endPage; i++) {
                paginationContainer.innerHTML += `<button class="px-3 py-1 border rounded ${i === currentPage ? 'bg-blue-500 text-white' : 'bg-gray-200'}" onclick="updatePage(${i})">${i}</button>`;
            }
            
            if (currentPage < totalPages) {
                paginationContainer.innerHTML += `<button class="px-3 py-1 border rounded bg-gray-200" onclick="updatePage(${currentPage + 1})">Next</button>`;
            }
        }
        
        createPagination(2, 3);
    </script>
</body>
</html>
