@extends('layouts.app')

@section('content')
    <customer-form
        :status-available = "{{ json_encode(\App\Customer::statusAvailable()) }}"
        :phone-types = "{{ json_encode($phoneTypes) }}"
        :phone-brands = "{{ json_encode($phoneBrands) }}"
        :edit-data = "{{ isset($customer) ? json_encode($customer) : 'null' }}"
    ></customer-form>
@endsection
