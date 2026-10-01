@extends('layouts.app')

@section('content')

<x-coming-soon
    :title="__('modules.inventory.title')"
    :description="__('modules.inventory.description')"
    :items="[
        __('modules.inventory.dashboard'),
        __('modules.inventory.units'),
        __('modules.inventory.inventoryItems'),
        __('modules.inventory.categories'),
        __('modules.inventory.inventoryStocks'),
        __('modules.inventory.inventoryMovements'),
        __('modules.inventory.recipes'),
        __('modules.inventory.batchRecipes'),
        __('modules.inventory.batchInventory'),
        __('modules.inventory.purchaseOrders'),
        __('modules.inventory.suppliers'),
        __('modules.inventory.reports'),
        __('modules.inventory.batchReports'),
        __('modules.inventory.settings'),
    ]"
/>

@endsection
