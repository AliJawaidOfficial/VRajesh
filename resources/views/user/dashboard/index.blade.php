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
                <span class="heading">Dashboard</span>
            </div>
        </div>
        {{-- <div class="inventory-main-input-wrapper mt-3">
            <div class="row justify-content-between">
                <div class="col-md-12 py-2">
                    <p>
                        @if (isset(session()->get('user')))
                            {{ session()->get('user') }}
                        @endif
                    </p>
                </div>
            </div>
        </div> --}}
    </section>
@endsection
