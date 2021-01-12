@extends('layouts.app')

@section('content')
    <phone-brand-edit-list
        :items = "{{ json_encode($phoneBrands) }}"
    ></phone-brand-edit-list>
@endsection
