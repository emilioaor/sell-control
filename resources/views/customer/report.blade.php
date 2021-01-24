@extends('layouts.app')

@section('content')
    <customer-report
        :sellers = "{{ json_encode($sellers) }}"
    ></customer-report>
@endsection
