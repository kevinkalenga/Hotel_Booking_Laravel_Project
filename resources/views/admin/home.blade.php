@extends('admin.layout.app')

@section('heading', 'Dashboard')

@section('main_content') 
    <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Completed Orders</h4>
                                </div>
                                <div class="card-body">
                                    {{$total_completed_orders}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-danger">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Pending Orders</h4>
                                </div>
                                <div class="card-body">
                                    {{$total_pending_orders}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-warning">
                                <i class="fa fa-bullhorn"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Active Customers</h4>
                                </div>
                                <div class="card-body">
                                    {{$total_active_customers}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-success">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Pending Customers</h4>
                                </div>
                                <div class="card-body">
                                    {{$total_pending_customers}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-info">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Rooms</h4>
                                </div>
                                <div class="card-body">
                                    {{$total_rooms}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-dark">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4>Total Subscribers</h4>
                                </div>
                                <div class="card-body">
                                    {{$total_subscribers}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
@endsection