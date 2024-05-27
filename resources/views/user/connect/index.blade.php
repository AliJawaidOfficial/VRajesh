{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Connect Your Accounts')

{{-- Styles --}}
@section('styles')
@endsection

{{-- Vendor Scripts --}}
@section('scripts')
    {{-- <script>
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
        window.fbAsyncInit = function() {
            FB.init({
                appId: '{{ env('FACEBOOK_CLIENT_ID') }}',
                xfbml: true,
                version: '{{ env('FACEBOOK_GRAPH_VERSION') }}'
            });
        };

        document.getElementById("loginBtn").addEventListener("click", function() {
            FB.login(function(response) {
                if (response.authResponse) {
                    console.log('Welcome!  Fetching your information.... ');
                    FB.api('/me', {
                        fields: 'name, email'
                    }, function(response) {
                        document.getElementById("profile").innerHTML = "Good to see you, " +
                            response
                            .name + ". i see your email address is " + response.email
                    });
                } else {
                    console.log('User cancelled login or did not fully authorize.');
                }
            });
        })
    </script> --}}

    {{-- <script>
        window.fbAsyncInit = function() {
            FB.init({
                appId: '462875799551571',
                xfbml: true,
                version: 'v19.0'
            });
            FB.AppEvents.logPageView();
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script> --}}

    {{-- <script>
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));


        window.fbAsyncInit = function() {

            FB.init({
                appId: '{your-facebook-app-id}',
                xfbml: true,
                version: '{the-graph-api-version-for-your-app}'
            });

            FB.login(function(response) {
                if (response.authResponse) {
                    console.log('Welcome!  Fetching your information.... ');
                    FB.api('/me', {
                        fields: 'name, email'
                    }, function(response) {
                        document.getElementById("profile").innerHTML = "Good to see you, " + response
                            .name + ". i see your email address is " + response.email
                    });
                } else {
                    console.log('User cancelled login or did not fully authorize.');
                }
            });
        };
    </script> --}}
@endsection

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">
        <div class="h1 p-4 bg-white text-center">
            <h1>Connect Accounts</h1>
        </div>

        {{-- <div class="fb-like" data-share="true" data-width="450" data-show-faces="true"></div> --}}

        {{-- <p id="profile"></p> --}}

        {{-- <script>
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {
                    return;
                }
                js = d.createElement(s);
                js.id = id;
                js.src = "https://connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));


            window.fbAsyncInit = function() {
                FB.init({
                    appId: '462875799551571',
                    xfbml: true,
                    version: 'v19.0'
                });

                FB.login(function(response) {
                    if (response.authResponse) {
                        console.log('Welcome!  Fetching your information.... ');
                        FB.api('/me', {
                            fields: 'name, email'
                        }, function(response) {
                            document.getElementById("profile").innerHTML = "Good to see you, " + response
                                .name + ". i see your email address is " + response.email
                        });
                    } else {
                        console.log('User cancelled login or did not fully authorize.');
                    }
                });
            };
        </script> --}}

        <div class="row row-cols-1 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3 rounded mt-3">
            <div class="col">
                <div class="card shadow-sm">
                    <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/images/icons/icons-facebook.png') }}" alt="">
                    </div>
                    <div class="card-body pt-0">
                        <p class="mb-3 text-center">Connect With Facebok</p>
                        {{-- <a href="javascript:void(0)" id="loginBtn" class="btn btn-dark d-block">Connect</a> --}}
                        @if (Auth::guard('web')->user()->facebook_access_token != null)
                            <a href="{{ route('user.connect.facebook.disconnect') }}"
                                class="btn btn-danger d-block">Disconnect</a>
                        @else
                            <a href="{{ route('user.connect.facebook') }}" class="btn btn-dark d-block">Connect</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
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
