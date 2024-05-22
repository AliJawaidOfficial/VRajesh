<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <!-------------- Favion -------------->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png?v=0') }}" type="image/x-icon">
    <!---------------- SEO ---------------->
    <title>@yield('title') {{ config('app.name') }}</title>
    <meta name="description" content="" />
    <!--------------- Fonts --------------->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <!-------- Vendors -------->
    <!-- font-awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Pace -->
    <script src="https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pace-js@latest/pace-theme-default.min.css">
    <!-- select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Tagify -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <!------------ Stylesheets ------------>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
    <!---------- Page ---------->
    @yield('styles')
</head>

<body>

    {{-- Content --}}
    @if (Request()->is('login') ||
            Request()->is('reset-password') ||
            Request()->is('new-password') ||
            Request()->is('new-password/*'))
        @yield('content')
    @else
        <div class="dashboard">
            {{-- Sidebar --}}
            @include('user.layouts.sidebar')
            {{-- /Sidebar --}}

            <main class="main-wrapper">
                {{-- Header --}}
                @include('user.layouts.header')
                {{-- .Header --}}

                {{-- Content --}}
                @yield('content')
                {{-- /Content --}}
            </main>
            <!-- /Page Layout -->
        </div>
    @endif
    {{-- /Content --}}


    <!---------- Vendor Scripts ---------->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- jquery validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js"
        integrity="sha512-WMEKGZ7L5LWgaPeJtw9MBM4i5w5OSBlSjTjCtSnvFJGSVD26gE5+Td12qN5pvWXhuWaWcVwF++F7aqu9cvqP0A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- tagify -->
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <!-- tagify -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script>
        @if (Session::has('success'))
            toastr['success']('{{ session('success')['text'] }}', 'Successfully');
        @elseif (Session::has('error'))
            toastr['error']('{{ session('error')['text'] }}', 'Oops!');
        @elseif (Session::has('info'))
            toastr['info']('{{ session('info')['text'] }}', 'Alert!');
        @elseif (Session::has('warning'))
            toastr['warning']('{{ session('warning')['text'] }}', 'Alert!');
        @endif
    </script>
    <!----------- Page Scripts ----------->
    @yield('scripts')
</body>

</html>
