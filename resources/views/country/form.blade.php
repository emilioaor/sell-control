@extends('layouts.app')

@section('content')
    <country-form
        :edit-data = "{{ isset($country) ? json_encode($country) : 'null' }}"
    ></country-form>
@endsection
