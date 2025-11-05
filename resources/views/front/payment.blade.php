@extends('front.layout.app')

@section('main_content')
<script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=USD"></script>

@php
    $total_price = 0;
    $cart_room_id = session()->get('cart_room_id', []);
    $cart_checkin_date = session()->get('cart_checkin_date', []);
    $cart_checkout_date = session()->get('cart_checkout_date', []);
    $cart_adult = session()->get('cart_adult', []);
    $cart_children = session()->get('cart_children', []);

    foreach($cart_room_id as $i => $room_id){
        $room_data = DB::table('rooms')->where('id', $room_id)->first();
        if (!$room_data) continue;

        $d1 = explode('/', $cart_checkin_date[$i]);
        $d2 = explode('/', $cart_checkout_date[$i]);
        $checkin = strtotime($d1[2].'-'.$d1[1].'-'.$d1[0]);
        $checkout = strtotime($d2[2].'-'.$d2[1].'-'.$d2[0]);
        $nights = ($checkout - $checkin)/60/60/24;

        $total_price += $room_data->price * $nights;
    }
@endphp

<div class="page-top">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{ $global_page_data->payment_heading }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="container">
        <div class="row">

            {{-- Payment Method --}}
            <div class="col-lg-4 col-md-4 checkout-left mb_30">
                <h4>Make Payment</h4>
                <select name="payment_method" class="form-control select2" id="paymentMethodChange" autocomplete="off">
                    <option value="">Select Payment Method</option>
                    <option value="PayPal">PayPal</option>
                    <option value="Stripe">Stripe</option>
                </select>

                {{-- PayPal --}}
                <div class="paypal mt_20">
                    <h4>Pay with PayPal</h4>
                    <div id="paypal-button-container"></div>
                </div>

                {{-- Stripe --}}
                <div class="stripe mt_20" style="display:none;">
                    <h4>Pay with Stripe</h4>
                    <p>Total to pay: <strong>${{ number_format($total_price, 2) }}</strong></p>

                    <form id="stripe-payment-form">
                        <div id="card-element"></div>
                        <button id="stripe-submit" class="btn btn-primary mt_3">Pay Now</button>
                        <div id="stripe-message" class="mt-3 text-danger"></div>
                    </form>
                </div>
            </div>

            {{-- Billing details --}}
            <div class="col-lg-4 col-md-4 checkout-right">
                <div class="inner">
                    <h4 class="mb_10">Billing Details</h4>
                    <div>Name: {{ session()->get('billing_name') }}</div>
                    <div>Email: {{ session()->get('billing_email') }}</div>
                    <div>Phone: {{ session()->get('billing_phone') }}</div>
                    <div>Country: {{ session()->get('billing_country') }}</div>
                    <div>Address: {{ session()->get('billing_address') }}</div>
                    <div>State: {{ session()->get('billing_state') }}</div>
                    <div>City: {{ session()->get('billing_city') }}</div>
                    <div>Zip: {{ session()->get('billing_zip') }}</div>
                </div>
            </div>

            {{-- Cart details --}}
            <div class="col-lg-4 col-md-4 checkout-right">
                <div class="inner">
                    <h4 class="mb_10">Cart Details</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                @foreach($cart_room_id as $i => $room_id)
                                    @php
                                        $room_data = DB::table('rooms')->where('id', $room_id)->first();
                                        if (!$room_data) continue;

                                        $d1 = explode('/', $cart_checkin_date[$i]);
                                        $d2 = explode('/', $cart_checkout_date[$i]);
                                        $checkin = strtotime($d1[2].'-'.$d1[1].'-'.$d1[0]);
                                        $checkout = strtotime($d2[2].'-'.$d2[1].'-'.$d2[0]);
                                        $nights = ($checkout - $checkin)/60/60/24;
                                        $price = $room_data->price * $nights;
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ $room_data->name }}<br>
                                            ({{ $cart_checkin_date[$i] }} - {{ $cart_checkout_date[$i] }})<br>
                                            Adult: {{ $cart_adult[$i] }}, Children: {{ $cart_children[$i] }}
                                        </td>
                                        <td class="p_price">${{ $price }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td><b>Total:</b></td>
                                    <td class="p_price"><b>${{ number_format($total_price, 2) }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- PAYPAL --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    const totalAmount = {{ number_format((float)$total_price, 2, '.', '') }};
    const paypalSection = document.querySelector('.paypal');
    const stripeSection = document.querySelector('.stripe');

    paypal.Buttons({
        style: { layout: 'vertical', color: 'blue', shape: 'rect', label: 'paypal' },

        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{ amount: { value: totalAmount } }]
            });
        },

        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                const captureId = details.purchase_units[0].payments.captures[0].id;
                window.location.href = "{{ route('paypal') }}?capture_id=" + captureId;
            });
        },

        onError: function(err) {
            console.error('PayPal error:', err);
            alert('Une erreur est survenue avec PayPal.');
        }

    }).render('#paypal-button-container');
});
</script>

{{-- STRIPE --}}
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener("DOMContentLoaded", async function() {
    const totalAmount = {{ number_format((float)$total_price, 2, '.', '') }};
    const paymentSelect = document.getElementById('paymentMethodChange');
    const stripeSection = document.querySelector('.stripe');
    const paypalSection = document.querySelector('.paypal');

    stripeSection.style.display = 'none';

    paymentSelect.addEventListener('change', function() {
        if (this.value === 'Stripe') {
            stripeSection.style.display = 'block';
            paypalSection.style.display = 'none';
        } else if (this.value === 'PayPal') {
            paypalSection.style.display = 'block';
            stripeSection.style.display = 'none';
        } else {
            stripeSection.style.display = 'none';
            paypalSection.style.display = 'none';
        }
    });

    const stripe = Stripe("{{ config('services.stripe.key') }}");
    const elements = stripe.elements();
    const card = elements.create('card');
    card.mount('#card-element');

    const form = document.getElementById('stripe-payment-form');
    const message = document.getElementById('stripe-message');

    let clientSecret = null;

    try {
        const res = await fetch("{{ route('stripe.createIntent') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ amount: totalAmount })
        });
        const data = await res.json();
        clientSecret = data.client_secret;
    } catch (error) {
        console.error('Stripe error:', error);
        return;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        message.textContent = '';

        const { paymentIntent, error } = await stripe.confirmCardPayment(clientSecret, {
            payment_method: { card: card }
        });

        if (error) {
            message.textContent = error.message;
        } else if (paymentIntent.status === 'succeeded') {
            window.location.href = "{{ route('stripe.success') }}?payment_intent=" + paymentIntent.id;
        }
    });
});
</script>
@endsection
