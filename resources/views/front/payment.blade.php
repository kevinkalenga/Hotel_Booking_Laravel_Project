@extends('front.layout.app')

@section('main_content')
<script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=USD"></script>

<div class="page-top">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>{{$global_page_data->payment_heading}}</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="container">
        <div class="row">

            <div class="col-lg-4 col-md-4 checkout-left mb_30">
                <h4>Make Payment</h4>
                <select name="payment_method" class="form-control select2" id="paymentMethodChange" autocomplete="off">
                    <option value="">Select Payment Method</option>
                    <option value="PayPal">PayPal</option>
                    <option value="Stripe">Stripe</option>
                </select>

                <div class="paypal mt_20">
                    <h4>Pay with PayPal</h4>
                    <div id="paypal-button-container"></div>
                </div>

                <div class="stripe mt_20">
                    <h4>Pay with Stripe</h4>
                    <p>Write necessary code here</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 checkout-right">
                <div class="inner">
                    <h4 class="mb_10">Billing Details</h4>
                    <div>Name: {{session()->get('billing_name')}}</div>
                    <div>Email: {{session()->get('billing_email')}}</div>
                    <div>Phone: {{session()->get('billing_phone')}}</div>
                    <div>Country: {{session()->get('billing_country')}}</div>
                    <div>Address: {{session()->get('billing_address')}}</div>
                    <div>State: {{session()->get('billing_state')}}</div>
                    <div>City: {{session()->get('billing_city')}}</div>
                    <div>Zip: {{session()->get('billing_zip')}}</div>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 checkout-right">
                <div class="inner">
                    <h4 class="mb_10">Cart Details</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                @php 
                                    $total_price = 0;
                                    $cart_room_id = session()->get('cart_room_id', []);
                                    $cart_checkin_date = session()->get('cart_checkin_date', []);
                                    $cart_checkout_date = session()->get('cart_checkout_date', []);
                                    $cart_adult = session()->get('cart_adult', []);
                                    $cart_children = session()->get('cart_children', []);
                                @endphp

                                @foreach($cart_room_id as $i => $room_id)
                                    @php
                                        $room_data = DB::table('rooms')->where('id', $room_id)->first();
                                        if (!$room_data) continue;

                                        $d1 = explode('/', $cart_checkin_date[$i]);
                                        $d2 = explode('/', $cart_checkout_date[$i]);
                                        $checkin = strtotime($d1[2].'-'.$d1[1].'-'.$d1[0]);
                                        $checkout = strtotime($d2[2].'-'.$d2[1].'-'.$d2[0]);
                                        $nights = ($checkout - $checkin) / 60 / 60 / 24;
                                        $price = $room_data->price * $nights;
                                        $total_price += $price;
                                    @endphp
                                    <tr>
                                        <td>
                                            {{$room_data->name}}<br>
                                            ({{$cart_checkin_date[$i]}} - {{$cart_checkout_date[$i]}})<br>
                                            Adult: {{$cart_adult[$i]}}, Children: {{$cart_children[$i]}}
                                        </td>
                                        <td class="p_price">${{ $price }}</td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <td><b>Total:</b></td>
                                    <td class="p_price"><b>${{ $total_price }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const totalAmount = {{ number_format((float)$total_price, 2, '.', '') }};

    paypal.Buttons({
        style: { layout: 'vertical', color: 'blue', shape: 'rect', label: 'paypal' },

        // Crée la commande côté client
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{ amount: { value: totalAmount } }]
            });
        },

        // Capture le paiement côté client avant la redirection
        onApprove: function(data, actions) {
             return actions.order.capture().then(function(details) {
           // Récupère le captureId généré après paiement réussi
           const captureId = details.purchase_units[0].payments.captures[0].id;

        // Redirection vers Laravel pour enregistrer la commande
        window.location.href = "{{ route('paypal') }}?capture_id=" + captureId;
    });
        },

        onError: function(err) {
            console.error('Erreur PayPal :', err);
            alert('Une erreur est survenue avec PayPal.');
        }

    }).render('#paypal-button-container');
});
</script>
@endsection
