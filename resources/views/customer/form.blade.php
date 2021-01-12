@extends('layouts.app')

@section('content')
    <customer-form
        :status-available = "{{ json_encode(\App\Customer::statusAvailable()) }}"
    ></customer-form>
@endsection
