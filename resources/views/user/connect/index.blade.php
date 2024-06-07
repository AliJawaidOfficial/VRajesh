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
            @foreach ($platforms as $platform)
                @can($platform['permission'])
                    <div class="col">
                        <div class="card rounded-6">
                            <div
                                class="card-header bg-transparent border-0 d-flex align-items-center justify-content-center position-relative">
                                <img src="{{ asset($platform['image']) }}" alt="">
                                @if ($platform['is_connected'] == 1)
                                    <img src="{{ asset($platform['user_avatar']) }}"
                                        class="rounded-circle position-absolute end-0 bottom-0" alt="">
                                @endif
                            </div>
                            <div class="card-body pt-0">

                                @if ($platform['is_connected'] == 1)
                                    <p>{{ $platform['user_name'] }}</p>
                                    <p>{{ $platform['user_email'] }}</p>
                                @endif

                                <p class="mb-3 text-center">{{ $platform['text'] }}</p>
                                @if ($platform['is_connected'] == 1)
                                    <a href="{{ route($platform['disconnect_route']) }}"
                                        class="btn btn-danger d-block">Disconnect</a>
                                @else
                                    <a href="{{ route($platform['connect_route']) }}" class="btn btn-dark d-block">Connect</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endcan
            @endforeach
        </div>
    </section>
@endsection
