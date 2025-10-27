@extends('front.layout.app')

@section('main_content')
    
<div class="page-top">
            <div class="bg"></div>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2>{{$global_page_data->cart_heading}}</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="page-content">
            <div class="container">
                <div class="row cart">
                    <div class="col-md-12">
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-cart">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Serial</th>
                                        <th>Photo</th>
                                        <th>Room Info</th>
                                        <th>Price/Night</th>
                                        <th>Checkin</th>
                                        <th>Checkout</th>
                                        <th>Guests</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @php 
                                     $arr_cart_room_id = array();
                                     $i=0;
                                     foreach(session()->get('cart_room_id') as $value){
                                       $arr_cart_room_id[$i] = $value;
                                       $i++;
                                     }
                                     
                                     $arr_cart_checkin_date = array();
                                     $i=0;
                                     foreach(session()->get('cart_checkin_date') as $value){
                                       $arr_cart_checkin_date[$i] = $value;
                                       $i++;
                                     }
                                     
                                     $arr_cart_checkout_date = array();
                                     $i=0;
                                     foreach(session()->get('cart_checkout_date') as $value){
                                       $arr_cart_checkout_date[$i] = $value;
                                       $i++;
                                     }
                                     
                                     $arr_cart_adult = array();
                                     $i=0;
                                     foreach(session()->get('cart_adult') as $value){
                                       $arr_cart_adult[$i] = $value;
                                       $i++;
                                     }
                                     $arr_cart_children = array();
                                     $i=0;
                                     foreach(session()->get('cart_children') as $value){
                                       $arr_cart_children[$i] = $value;
                                       $i++;
                                     }

                                     for($i=0; $i < count($arr_cart_room_id); $i++)
                                     {

                                        $room_data = DB::table('rooms')->where('id', $arr_cart_room_id)->first();

                                @endphp 

                                            <tr>
                                                <td>
                                                    <a href="" class="cart-delete-link" onclick="return confirm('Are you sure?');"><i class="fa fa-times"></i></a>
                                                </td>
                                                <td>{{$i+1}}</td>
                                                <td><img src="{{asset('uploads/'.$room_data->featured_photo)}}"></td>
                                                <td>
                                                    <a href="{{route('room_detail', $room_data->id)}}" class="room-name">{{$room_data->name}}</a>
                                                </td>
                                                <td>${{$room_data->price}}</td>
                                                <td>{{$arr_cart_checkin_date[$i]}}</td>
                                                <td>{{$arr_cart_checkout_date[$i]}}</td>
                                                <td>
                                                    Adult: {{$arr_cart_adult[$i]}}<br>
                                                    Children: {{$arr_cart_children[$i]}}
                                                </td>
                                                <td>$60</td>
                                            </tr>

                                      
                                      
                                      
                                      
                                         @php
                                    
                                    
                                      }
                                    
                                  @endphp
                                  
                                    <tr>
                                        <td colspan="8" class="tar">Total:</td>
                                        <td>$160</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>                       

                        <div class="checkout mb_20">
                            <a href="checkout.html" class="btn btn-primary bg-website">Checkout</a>
                        </div>
    
                    </div>
                </div>
            </div>
        </div>
@endsection



