<?php

namespace App\Http\Controllers;

class CashRegisterController extends Controller
{
    public function dashboard()
    {
        abort_if(!in_array('Cash Register', restaurant_modules()), 303);
        abort_if(!user_can('Show Cash Register Dashboard'), 303);
        return view('cash-register.dashboard');
    }

    public function operate()
    {
        abort_if(!in_array('Cash Register', restaurant_modules()), 303);
        abort_if(!user_can('Manage Cash Register'), 303);
        return view('cash-register.operate');
    }

    public function reports()
    {
        abort_if(!in_array('Cash Register', restaurant_modules()), 303);
        abort_if(!user_can('Show Cash Register Reports'), 303);
        return view('cash-register.reports');
    }

    public function approvals()
    {
        abort_if(!in_array('Cash Register', restaurant_modules()), 303);
        abort_if(!user_can('Manage Cash Register Approvals'), 303);
        return view('cash-register.approvals');
    }

    public function denominations()
    {
        abort_if(!in_array('Cash Register', restaurant_modules()), 303);
        abort_if(!user_can('Manage Cash Register Settings'), 303);
        return view('cash-register.denominations');
    }

    public function settings()
    {
        abort_if(!in_array('Cash Register', restaurant_modules()), 303);
        abort_if(!user_can('Manage Cash Register Settings'), 303);
        return view('cash-register.settings');
    }
}
