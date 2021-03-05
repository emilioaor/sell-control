@extends('layouts.app')

@section('content')
    <province-form
        :edit-data = "{{ isset($province) ? json_encode($province) : 'null' }}"
    ></province-form>
@endsection
