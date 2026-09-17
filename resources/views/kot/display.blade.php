@extends('layouts.public')

@section('content')

@livewire('kot.kots', ['showAllKitchens' => true])

@endsection
