@extends('app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold">Tracking de la orden</h1>

    <pre>{{ json_encode($orden ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
</div>
@endsection
