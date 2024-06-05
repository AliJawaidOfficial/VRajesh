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
        <div class="h3 p-3 bg-white text-center rounded-6">
            <h3>Connect Your Accounts</h3>
        </div>

        <div class="row row-cols-1 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3 rounded mt-1">
            <div class="col">
                <div class="card rounded-6">
                    <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/images/icons/icons-facebook.png') }}" alt="">
                    </div>
                    <div class="card-body pt-0">
                        <p class="mb-3 text-center">Connect With Facebok</p>
                        @if (Auth::guard('web')->user()->meta_access_token != null)
                            <a href="{{ route('user.connect.facebook.disconnect') }}"
                                class="btn btn-danger d-block">Disconnect</a>
                        @else
                            <a href="{{ route('user.connect.facebook') }}" class="btn btn-dark d-block">Connect</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card rounded-6">
                    <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/images/icons/icons-linkedin.png') }}" alt="">
                    </div>

                    <div class="card-body pt-0">
                        <p class="mb-3 text-center">Connect With LinkedIn</p>
                        @if (Auth::guard('web')->user()->linkedin_access_token != null)
                            <a href="{{ route('user.connect.linkedin.disconnect') }}"
                                class="btn btn-danger d-block">Disconnect</a>
                        @else
                            <a href="{{ route('user.connect.linkedin') }}" class="btn btn-dark d-block">Connect</a>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
