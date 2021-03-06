@extends('layouts.app')

@section('content')
    <user-form
        :roles-available = "{{ json_encode(\App\User::rolesAvailable()) }}"
        :status-available = "{{ json_encode(\App\Customer::statusAvailable()) }}"
        :edit-data = "{{ isset($user) ? json_encode($user) : 'null' }}"
        :sellers = "{{ isset($sellers) ? json_encode($sellers) : '[]' }}"
    ></user-form>
@endsection
