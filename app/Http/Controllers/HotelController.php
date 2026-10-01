<?php

namespace App\Http\Controllers;

class HotelController extends Controller
{
    public function index()
    {
        abort_if(!in_array('Hotel', restaurant_modules()), 303);
        abort_if(!user_can('Show Hotel'), 303);
        return view('hotel.index');
    }
}
