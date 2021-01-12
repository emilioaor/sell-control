@extends('layouts.app')

@section('content')
    <phone-type-edit-list
        :items = "{{ json_encode($phoneTypes) }}"
    ></phone-type-edit-list>
@endsection
