@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.Maintenance')
@endsection

@section('body')

    <body>
    @endsection

    @section('content')

        <div class="home-btn d-none d-sm-block">
            <a href="index" class="text-dark"><i class="fas fa-home h2"></i></a>
        </div>

        <section class="my-5 pt-sm-5">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="home-wrapper">
                            <div class="mb-5">
                                <a href="index" class="d-block auth-logo">
                                    <img src="{{ URL::asset('/assets/images/logo-dark.png') }}" alt="" height="20"
                                        class="auth-logo-dark mx-auto">
                                    <img src="{{ URL::asset('/assets/images/logo-light.png') }}" alt="" height="20"
                                        class="auth-logo-light mx-auto">
                                </a>
                            </div>


                            <div class="row justify-content-center">
                                <div class="col-sm-4">
                                    <div class="maintenance-img">
                                        <img src="{{ URL::asset('/assets/images/maintenance.svg') }}" alt="" class="img-fluid mx-auto d-block">
                                    </div>
                                </div>
                            </div>
                            <h3 class="mt-5">Service is Under Maintenance</h3>
                            <p>Please check back in sometime.</p>
                            <!-- end row -->
                        </div>
                    </div>
                </div>
            </div>
        </section>

    @endsection
