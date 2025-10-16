<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <title>@yield('title', 'Inventory')</title>
    @vite(['resources/js/app.js'])
</head>
<body class="bg-light">
    @php
        $sidebarItems = json_decode(file_get_contents(public_path('/sidebar.json')));
    @endphp

    <div class="d-flex">

    {{-- Sidebar --}}
    <div class="sidebar bg-dark text-white p-3 d-none d-md-block" style="width: 240px; min-height: 100vh;">
        <img src="/images/logo.png" alt="ENCORE Custom" style="width: 200px; margin-bottom: 70px; margin-top: 10px;">
        <ul class="nav flex-column gap-2">
            @foreach($sidebarItems as $item)
                <li class="nav-item">
                    <a href="{{ route($item->route) }}" 
                    class="nav-link text-white d-flex align-items-center {{ request()->routeIs($item->route) ? 'active' : '' }}">
                        <img src="{{ $item->icon }}" alt="{{ $item->title }}" style="width: 20px; margin-right: 10px;">
                        {{ $item->title }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>


        {{-- Main Content Area --}}
        <div class="flex-grow-1">
            {{-- Top Bar --}}
            <div class="d-flex justify-content-between align-items-center p-4 border-bottom bg-white position-relative">

            {{-- Centered Title --}}
            @php
                // Find the active sidebar item
                $activeItem = collect($sidebarItems)->first(function($item) {
                    return request()->routeIs($item->route);
                });
            @endphp
            <div class="position-absolute top-50 start-50 translate-middle m-0 fw-bold fs-1">
                {{ $activeItem->title ?? '' }}
            </div>

                {{-- Right: Notifications + User --}}
                <div class="d-flex align-items-center ms-auto">
                    <div class="position-relative me-4">
                        <img src="/images/bell.svg" alt="Notification Icon" style="width: 32px;">
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                            12
                        </span>
                    </div>

                    <img src="/images/user.svg" alt="User Icon" style="width: 32px;" class="me-2">
                    <span class="fw-semibold">User Name</span>
                </div>
            </div>


            {{-- Page Content --}}
            <div>
                @yield('content')
            </div>
        </div>
    </div>

    <style>
        table th, table td {
            vertical-align: middle;
            font-size: 14px;
        }
        .badge {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 12px;
        }
        .toggle-row {
            border: none;
            background: transparent;
            cursor: pointer;
        }
        .sidebar .nav-link.active {
            position: relative;
        }

        .sidebar .nav-link.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background-color: white; 
            border-radius: 2px;
        }
    </style>

    <style>
        @media (max-width: 768px) {
            .sidebar {
                display: none !important;
            }
            body .flex-grow-1 {
                width: 100%;
            }
        }
    </style>
</body>
</html>
