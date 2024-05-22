{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Add New User - ')

{{-- Styles --}}
@section('styles')
@endsection

{{-- Vendor Scripts --}}
@section('scripts')
@endsection

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">
        <div class="main-inventory-heading d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center justify-content-center">
                <span class="go-back-icon d-inline-block"><img src="./assets/images/icons/arrow-back.png"
                        alt=""></span>
                <span class="heading">Connect Accounts</span>
            </div>
        </div>
        <div class="inventory-main-input-wrapper mt-3">
            <div class="row justify-content-between">
                <div class="col-md-12 py-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="m-0">Connect With Facebok</p>
                        @if (Session::get('facebook_user'))
                            <a href="{{ route('user.logout') }}" class="btn btn-danger">Disconnect</a>
                        @else
                            <a href="{{ route('user.connect.linkedin') }}" class="btn btn-dark">Connect</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="inventory-main-input-wrapper mt-3">
            <div class="row justify-content-between">
                <div class="col-md-12 py-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="m-0">Connect With LinkedIn</p>
                        @if (Session::get('LINKEDIN_USER_TOKEN'))
                            <a href="{{ route('user.logout') }}" class="btn btn-danger">Disconnect</a>
                        @else
                            <a href="{{ route('user.connect.linkedin') }}" class="btn btn-dark">Connect</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
