@extends('layouts.app')

@section('content')
    <province-list
        :items = "{{ json_encode($provinces->items()) }}"
        :total = "{{ $provinces->total() }}"
    >
        <template v-slot:pagination>{{ $provinces->links() }}</template>
    </province-list>
@endsection
