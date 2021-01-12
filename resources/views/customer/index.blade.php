@extends('layouts.app')

@section('content')
    <customer-list
        :items = "{{ json_encode($customers->items()) }}"
        :total = "{{ json_encode($customers->total()) }}"
    >
        <template v-slot:pagination>{{ $customers->links() }}</template>
    </customer-list>
@endsection
