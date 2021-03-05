@extends('layouts.app')

@section('content')
    <country-list
        :items = "{{ json_encode($countries->items()) }}"
        :total = "{{ $countries->total() }}"
    >
        <template v-slot:pagination>{{ $countries->links() }}</template>
    </country-list>
@endsection
