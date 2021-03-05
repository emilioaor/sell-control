@extends('layouts.app')

@section('content')
    <city-list
        :items = "{{ json_encode($cities->items()) }}"
        :total = "{{ $cities->total() }}"
    >
        <template v-slot:pagination>{{ $cities->links() }}</template>
    </city-list>
@endsection
