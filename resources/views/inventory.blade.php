@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div class="container-fluid py-4">
    {{-- Filter Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="background-color: white; padding: 10px;">
        <div class="d-flex gap-2 flex-wrap">
            <select class="form-select form-select-sm" style="width: 200px;">
                <option>Number of Product | All</option>
            </select>
            <select class="form-select form-select-sm" style="width: 200px;">
                <option>Total Product | All</option>
            </select>
        </div>

        <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search" style="width: 300px;">
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3 d-flex align-items-center" style="background-color: white; padding: 10px;">
        <div class="d-flex">
            <li class="nav-item"><a class="nav-link active" href="#">All</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Active</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Draft</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Achieved</a></li>
        </div>
        <div class="ms-auto">
            <button class="btn btn-secondary btn-sm me-2">Export</button>
            <button class="btn btn-secondary btn-sm">Import</button>
        </div>
    </ul>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table align-middle" id="inventoryTable">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;"><input type="checkbox"></th>
                    <th>Product</th>
                    <th>Status</th>
                    <th>Inventory</th>
                    <th>Sales Channels</th>
                    <th>Markets</th>
                    <th>Category</th>
                    <th>Vendor</th>
                    <th style="width: 50px;">Action</th>
                </tr>
            </thead>
            <tbody>
                {{-- Products injected by JS --}}
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div id="tableInfo" class="text-muted"></div>
            <nav>
                <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
            </nav>
        </div>
    </div>
</div>


<style>
    .toggle-row {
        border: none;
        background: none;
        cursor: pointer;
    }
    .table td, .table th {
        padding: 14px 12px;
    }
    img {
        object-fit: cover;
    }
    .pagination .page-link {
        border-radius: 6px;
    }
    .pagination .page-item {
        margin: 0 5px;
    }
    .details-row .btn {
        font-size: 13px;
        padding: 5px 12px;
    }
    .details-row img {
        max-height: 200px;
        object-fit: cover;
    }
    .nav-tabs .nav-link {
        color: #1c1d1dff;
    }
</style>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const productsPerPage = 7;
    let currentPage = 1;
    let products = [];
    let filteredProducts = [];

    const searchInput = document.getElementById('searchInput');

    // Fetch JSON
    fetch('/data.json')
        .then(res => res.json())
        .then(data => {
            products = data;
            filteredProducts = products;
            renderTable();
        })
        .catch(err => console.error('Error loading JSON:', err));

    // Search filter
    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase();
        filteredProducts = products.filter(p =>
            (p.name && p.name.toLowerCase().includes(query)) ||
            (p.description && p.description.toLowerCase().includes(query)) ||
            (p.status && p.status.toLowerCase().includes(query)) ||
            (p.vendor && p.vendor.toLowerCase().includes(query)) ||
            (p.category && p.category.toLowerCase().includes(query))
        );
        currentPage = 1;
        renderTable();
    });

    function renderTable() {
        const tbody = document.querySelector('#inventoryTable tbody');
        tbody.innerHTML = '';

        const start = (currentPage - 1) * productsPerPage;
        const end = start + productsPerPage;
        const pageProducts = filteredProducts.slice(start, end);

        pageProducts.forEach(product => {
            let statusClass = 'bg-info text-dark';
            if (product.status === 'Draft') statusClass = 'bg-primary text-white';
            if (product.status === 'Active') statusClass = 'bg-success text-white';

            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="checkbox"></td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <img src="${product.image ?? 'https://via.placeholder.com/45'}" width="45" height="45" class="rounded">
                        <div class="d-flex flex-column">
                            <span class="fw-semibold">${product.name}</span>
                            <small class="text-muted">${product.description || ''}</small>
                        </div>
                    </div>
                </td>
                <td><span class="badge ${statusClass}">${product.status}</span></td>
                <td>${product.inventory}<br>Last Update - <span style="color: blue;">25 AUG 2025</span></td>
                <td>${product.channels || ''}</td>
                <td>${product.markets || ''}</td>
                <td>${product.category || ''}</td>
                <td>${product.vendor || ''}</td>
                <td>
                    <button class="btn btn-sm btn-outline-secondary toggle-row" ${product.status === 'Draft' ? 'disabled' : ''}>
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);

            // Variants row
            const detailsRow = document.createElement('tr');
            detailsRow.classList.add('details-row');
            detailsRow.style.display = 'none';
            detailsRow.innerHTML = `
                <td colspan="9" class="bg-light p-3">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Variants</th>
                                    <th>Size</th>
                                    <th>Stock</th>
                                    <th>Prices</th>
                                    <th>Discount</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${product.variants.map(v => `
                                    <tr>
                                        <td>
                                            <span style="
                                                display: inline-block;
                                                width: 14px;
                                                height: 14px;
                                                border-radius: 50%;
                                                background-color: ${v.color.toLowerCase()};
                                                margin-right: 6px;
                                                border: 1px solid #ccc;
                                                vertical-align: middle;
                                            "></span>
                                            ${v.color}
                                        </td>
                                        <td>${v.size}</td>
                                        <td>${v.stock}<br>Last Update - <span style="color: blue;">25 AUG 2025</span></td>
                                        <td>${v.price}</td>
                                        <td>${v.discount}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </td>
            `;
            tbody.appendChild(detailsRow);
        });

        // Toggle eye icon
        document.querySelectorAll('.toggle-row').forEach(btn => {
            btn.addEventListener('click', function () {
                const row = this.closest('tr').nextElementSibling;
                row.style.display = row.style.display === 'none' ? '' : 'none';
            });
        });

        // Update table info
        const tableInfo = document.getElementById('tableInfo');
        const startItem = filteredProducts.length === 0 ? 0 : start + 1;
        const endItem = Math.min(end, filteredProducts.length);
        tableInfo.textContent = `Showing ${startItem} - ${endItem} of ${filteredProducts.length} results.`;

        renderPagination();
    }

    function renderPagination() {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';
        const totalPages = Math.ceil(filteredProducts.length / productsPerPage);

        // Previous Button
        const prev = document.createElement('li');
        prev.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prev.innerHTML = `<a class="page-link" href="#">&lt;</a>`;
        prev.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });
        pagination.appendChild(prev);

        // Page Numbers
        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${currentPage === i ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            li.addEventListener('click', () => {
                currentPage = i;
                renderTable();
            });
            pagination.appendChild(li);
        }

        // Next Button
        const next = document.createElement('li');
        next.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        next.innerHTML = `<a class="page-link" href="#">&gt;</a>`;
        next.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        });
        pagination.appendChild(next);
    }
});
</script>
@endsection
