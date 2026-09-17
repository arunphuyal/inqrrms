@extends('layouts.app')

@section('content')

<x-coming-soon
    :title="__('modules.hotel.title')"
    :description="__('modules.hotel.description')"
    :items="[
        __('modules.hotel.frontDeskDashboard'),
        __('modules.hotel.roomTypes'),
        __('modules.hotel.rooms'),
        __('modules.hotel.roomStatusBoard'),
        __('modules.hotel.guests'),
        __('modules.hotel.reservations'),
        __('modules.hotel.quotations'),
        __('modules.hotel.checkIn'),
        __('modules.hotel.checkOut'),
        __('modules.hotel.ratePlans'),
        __('modules.hotel.housekeeping'),
        __('modules.hotel.roomService'),
        __('modules.hotel.stayHistory'),
        __('modules.hotel.outlets'),
        __('modules.hotel.cashierDashboard'),
        __('modules.hotel.managementDashboard'),
        __('modules.hotel.paymentHistory'),
        __('modules.hotel.guestRevenueReport'),
        __('modules.hotel.fbDebtors'),
        __('modules.hotel.salesByWaiter'),
        __('modules.hotel.banquetAndEvents'),
        __('modules.hotel.agreements'),
        __('modules.hotel.hotelSettings'),
    ]"
/>

@endsection
