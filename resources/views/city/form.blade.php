@extends('layouts.app')

@section('content')
    <city-form
        :edit-data = "{{ isset($city) ? json_encode($city) : 'null' }}"
    ></city-form>
@endsection
