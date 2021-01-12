@extends('layouts.app')

@section('content')
    <user-form
        :roles-available = "{{ json_encode(\App\User::rolesAvailable()) }}"
        :edit-data = "{{ isset($user) ? json_encode($user) : 'null' }}"
    ></user-form>
@endsection
