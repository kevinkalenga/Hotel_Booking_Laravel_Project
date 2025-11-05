
@extends('front.layout.app')

@section('main_content')
<div class="container mt-5 text-center">
    <h1 class="text-danger">❌ Payment cancelled</h1>
    <p>The payment was cancelled or failed.</p>
    <a href="{{ route('checkout') }}" class="btn btn-secondary my-3">Try again</a>
</div>
@endsection
