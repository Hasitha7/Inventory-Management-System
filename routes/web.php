<?php

use Illuminate\Support\Facades\Route;

//sidebar.json
$sidebarPath = public_path('sidebar.json');
$sidebarItems = json_decode(file_get_contents($sidebarPath));

// Redirect root to the inventory page
if (!empty($sidebarItems)) {
    Route::get('/', function () use ($sidebarItems) {
        return redirect()->route($sidebarItems[2]->route);
    });
}

// routes
foreach ($sidebarItems as $item) {
    Route::get('/' . $item->route, function () use ($item) {
        return view($item->route);
    })->name($item->route);
}
