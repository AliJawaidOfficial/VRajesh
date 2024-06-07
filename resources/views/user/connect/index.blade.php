{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Connect Your Accounts')

{{-- Styles --}}
@section('styles')
    <style>
        .card-header {
            background-color: #f7f7f7;
            padding: 20px;
            border-bottom: 1px solid #e6e6e6;
            text-align: center;
        }

        .card-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
        }

        .card-body {
            padding: 20px;
            text-align: center;
        }

        .card-body .user-details {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: center;
        }

        .card-body .user-details img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .card-body .user-details p {
            margin: 0;
        }

        .card-body p {
            margin: 10px 0;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            color: #fff;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn-danger {
            background-color: #f44336;
        }

        .btn-danger:hover {
            background-color: #d32f2f;
        }

        .btn-dark {
            background-color: #333;
        }

        .btn-dark:hover {
            background-color: #555;
        }
    </style>
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

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 rounded mt-1">
            @foreach ($platforms as $platform)
                @can($platform['permission'])
                    <div class="col">
                        <div class="card rounded-6 h-100 d-flex flex-column justify-content-between">
                            <div
                                class="card-header pb-2 bg-transparent border-0 d-flex gap-3 align-items-center justify-content-center position-relative">
                                @if ($platform['is_connected'] == 1)
                                    <img src="{{ asset($platform['user_avatar']) }}"
                                        style="width: 65px !important; height: 65px !important" alt=""
                                        class="rounded-circle">
                                    <img style="width:30px !important; height: 30px !important"
                                        src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAADBUlEQVR4nO2aSWgUURCGPyeaUQIKgvsYNwgKBsQNRQQPiiueREUEFeLBLYIHD4LLNbiAFxc0KujJ5aCSCIqogbgc4gR3BEETFQ8uRKMRjRl5UIGhfDPTPTM9/Ubmh4I+VNf7q6tevapHQwn/L2LAZqAReA58BbqAd8BNYB8wEYdRCZwEfgMJD3IDmIJjWAV88+hAsnRLhCI4gL1ATxZOJMsFoG+YTmxKQewxsAOYDAwEBgATgPXA7RTvnArLiemSGslkfgAbPaTKIuCtxZm1FBgR4L4i0QHM9FkcXiobHyWCBcNyRcDskaVZ2JkEdCpbB4BpwFRgCAHjolr8fA629mQoBC+AI0A1eUZ/4KdazHy9bDFIDsxMla0HuASMzZcj1WqBN3mwedlHqf4ELA6i5F7Ng81asdUlqXQHeGaJfK/8AdZks9BwYL/0S9qoaUtyxVBJT30oRoGVQLNlXePkXL8R+J4m1OcoTLnfbunjzFlUkenlPvK1M+XsdQqH1ZZD2PRqaXEwBfEOadHrgePALgqLOsWnM11UFloawc+SZiZvw0SFZa+uSJVST5ViO1CFO6hT/M7alJYoJZOTM3ALcxTHFpvSMaV0BvcwQnH8YFN6opTm4x6iljPlH3xRSoNxD5Ve2iTdFoRdpWxYpjjesym9VkrjcA/1iuMhm9ItpVSDWxhjafvn2RR3KqVHQBluIAI0KH6vUt3AjLc0Z+bKxwUnDltaprTt/AnLdLY7xIu0GHDF4kRTJk7mwHlveTEObJA87Rcg8TJgpPR8R1OMEW3AMC/GZnmcpcOQdr+XEbPl+E84JM0ysfqGCfFpmZPDdKANWJePfVolQ1SThPZXgKS7ZeZ4IHdaCwLek56hiRYtEiVHHEE06fItWbYC5RQJRgGtaTZ0XHScj0Srh+r00PXI1PootVsKTS6RgzRI4xeTy71cbOWMXBY3DvRidMkRwo9Io0TCyLWwI+IH23wQM/+tOF1+4x6caHG9/CKHXTyDE2ZEKAqUSztyV366MWKeTToFEom/Zj6aNWMfMy0AAAAASUVORK5CYII=">
                                @endif
                                <img    @if (!$platform['is_connected'] == 1) style="width: 105px !important; height: 105px !important" @endif src="{{ asset($platform['image']) }}" alt="">
                            </div>
                            <div class="card-body pt-0 d-flex flex-column justify-content-end flex-grow-1">
                                @if ($platform['is_connected'] == 1)
                                    <div class="user-details">
                                        <div>
                                            <p class="mb-0 font-weight-bold">{{ $platform['user_name'] }}</p>
                                            <p class="mb-0 text-muted">{{ $platform['user_email'] }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if ($platform['is_connected'] == 1)
                                    <a href="{{ route($platform['disconnect_route']) }}" class="btn btn-danger">Disconnect</a>
                                @else
                                    <p class="mb-3">{{ $platform['text'] }}</p>
                                    <a href="{{ route($platform['connect_route']) }}" class="btn btn-dark">Connect</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endcan
            @endforeach
        </div>
    </section>
@endsection
