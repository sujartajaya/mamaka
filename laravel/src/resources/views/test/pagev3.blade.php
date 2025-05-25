<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Pagination</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">
    <div class="mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8">Responsive Pagination</h1>

        <!-- Pagination Info -->
        <div class="text-center text-gray-700 mb-4">
            Showing 1 to 10 of 97 results
        </div>

        <!-- Pagination Component -->
        <div class="flex justify-center items-center space-x-2">
            <!-- Previous Button -->
            <button id="prevButton" class="px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">
                &lt;
            </button>

            <!-- Pagination Numbers -->
            <div class="flex space-x-2">
                <!-- Dynamic Pagination Items -->
                <template id="paginationTemplate">
                    <button class="px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none"></button>
                </template>

                <!-- Placeholder for Pagination Items -->
                <div id="paginationContainer" class="flex space-x-2"></div>
            </div>

            <!-- Next Button -->
            <button id="nextButton" class="px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">
                &gt;
            </button>
        </div>
    </div>

    <script>
        // Parameters
        const totalPage = 100; // Total number of pages
        const currentPage = 1; // Current active page (default: 1)

        // Function to generate pagination
        function generatePagination(totalPage, currentPage) {
            const paginationContainer = document.getElementById('paginationContainer');
            const template = document.getElementById('paginationTemplate').content;

            // Clear existing content
            paginationContainer.innerHTML = '';

            // Helper function to create a pagination button
            function createButton(page, isActive = false) {
                const clone = document.importNode(template, true);
                const button = clone.querySelector('button');
                button.textContent = page;
                if (isActive) {
                    button.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
                }
                button.addEventListener('click', () => {
                    updatePagination(page);
                });
                return clone;
            }

            // Always show the first page
            paginationContainer.appendChild(createButton(1, currentPage === 1));

            // Calculate the range of pages to show
            let start = Math.max(2, currentPage - 2);
            let end = Math.min(totalPage - 1, currentPage + 2);

            // Add ellipsis if necessary
            if (start > 2) {
                paginationContainer.appendChild(createButton('...'));
            }

            // Add middle pages
            for (let i = start; i <= end; i++) {
                paginationContainer.appendChild(createButton(i, i === currentPage));
            }

            // Add ellipsis if necessary
            if (end < totalPage - 1) {
                paginationContainer.appendChild(createButton('...'));
            }

            // Always show the last page
            if (totalPage > 1) {
                paginationContainer.appendChild(createButton(totalPage, currentPage === totalPage));
            }
        }

        // Function to update pagination
        function updatePagination(newPage) {
            if (newPage < 1 || newPage > totalPage) return;
            generatePagination(totalPage, newPage);
        }

        // Event listeners for previous and next buttons
        document.getElementById('prevButton').addEventListener('click', () => {
            updatePagination(currentPage - 1);
        });

        document.getElementById('nextButton').addEventListener('click', () => {
            updatePagination(currentPage + 1);
        });

        // Generate pagination on page load
        generatePagination(totalPage, currentPage);
    </script>
</body>
</html>