{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Connect Your Accounts')

{{-- Styles --}}
@section('styles')
@endsection

{{-- Vendor Scripts --}}
@section('scripts')
@endsection

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">

        <div class="h1 p-4 bg-white text-center">
            <h1>Connect Accounts</h1>
        </div>


        <div class="row row-cols-1 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3 rounded mt-3">
            {{-- <div class="col">
                <div class="card shadow-sm">
                    <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/images/icons/icons-facebook.png') }}" alt="">
                    </div>

                    <div class="card-body pt-0">
                        <p class="mb-3 text-center">Connect With Facebok</p>
                        @if (Session::get('facebook_user'))
                            <a href="{{ route('user.logout') }}" class="btn btn-danger d-block">Disconnect</a>
                        @else
                            <a href="{{ route('user.connect.linkedin') }}" class="btn btn-dark d-block">Connect</a>
                        @endif
                    </div>
                </div>
            </div> --}}
            <div class="col">
                <div class="card">
                    <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/images/icons/icons-linkedin.png') }}" alt="">
                    </div>

                    <div class="card-body pt-0">
                        <p class="mb-3 text-center">Connect With LinkedIn</p>
                        @if (Auth::guard('web')->user()->linkedin_access_token != null)
                            <a href="{{ route('user.logout') }}" class="btn btn-danger d-block">Disconnect</a>
                        @else
                            <a href="{{ route('user.connect.linkedin') }}" class="btn btn-dark d-block">Connect</a>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
