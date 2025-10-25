@extends('front.layout.app')

@section('main_content')
        <div class="page-top">
            <div class="bg"></div>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2>{{$global_page_data->signup_heading}}</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="page-content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-4">
                        <div class="login-form">
                            <div class="mb-3">
                                <label for="" class="form-label">Full Name</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Email Address</label>
                                <input type="text" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Password</label>
                                <input type="password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary bg-website">Submit</button>
                            </div>
                            <div class="mb-3">
                                <a href="{{ route('customer_login') }}" class="primary-color">Existing User? Login Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection