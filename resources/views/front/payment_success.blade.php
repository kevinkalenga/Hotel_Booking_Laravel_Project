
@extends('front.layout.app')

@section('main_content')
<div class="container mt-5 text-center">
    <h1 class="text-success">✅ Payment successful</h1>
    <p>Thank you for your reservation !</p>
    <a href="{{ route('customer_home') }}" class="btn btn-primary my-3">Return to homepage</a>
</div>
@endsection
