<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagination with Tailwind</title>
    @vite('resources/css/app.css')
</head>
<body class="flex justify-center items-center min-h-screen bg-green-600">
    <div class="flex items-center space-x-2">
        <button id="prevPage" class="bg-gray-200 px-3 py-2 text-gray-500 hover:text-white hover:bg-blue-500 rounded">&lt;</button>
        <div id="pagination" class="flex space-x-3"></div>
        <button id="nextPage" class="bg-gray-200 px-3 py-2 text-gray-500 hover:text-white hover:bg-blue-500 rounded">&gt;</button>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const totalResults = 51;
            const resultsPerPage = 5;
            const totalPages = Math.ceil(totalResults / resultsPerPage);
            let currentPage = 1;
            
            const paginationContainer = document.getElementById("pagination");
            const prevButton = document.getElementById("prevPage");
            const nextButton = document.getElementById("nextPage");
            const urlParams = new URLSearchParams(window.location.search);

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
                        button.classList.add("hover:bg-blue-500", "hover:text-white");
                        if (page === currentPage) {
                            button.classList.add("bg-blue-500", "text-white");
                            /** perubahan data table */
                             urlParams.set("page", page);
                             window.history.pushState({}, "", `?${urlParams.toString()}`);

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

            renderPagination();
        });
    </script>
</body>
</html>
