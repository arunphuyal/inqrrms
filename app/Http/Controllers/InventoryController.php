<?php

namespace App\Http\Controllers;

class InventoryController extends Controller
{
    public function index()
    {
        abort_if(!in_array('Inventory', restaurant_modules()), 303);
        abort_if(!user_can('Show Inventory'), 303);
        return view('inventory.index');
    }
}
