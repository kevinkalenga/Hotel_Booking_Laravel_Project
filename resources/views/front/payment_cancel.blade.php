
@extends('front.layout.app')

@section('main_content')
<div class="container mt-5 text-center">
    <h1 class="text-danger">❌ Paiement annulé</h1>
    <p>Le paiement a été annulé ou a échoué.</p>
    <a href="{{ route('checkout') }}" class="btn btn-secondary mt-3">Réessayer</a>
</div>
@endsection
