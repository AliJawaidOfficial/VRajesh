{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Dashboard')

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
                <span class="heading">Posts</span>
            </div>
        </div>
    </section>
@endsection
