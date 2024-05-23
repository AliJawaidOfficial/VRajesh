{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Forget Password')

{{-- Styles --}}
@section('styles')
@endsection

{{-- Scripts --}}
@section('scripts')
@endsection


{{-- Content --}}
@section('content')
    <div class="sign-up-page position-relative">
        <div class="sign-up-bg-shaps-1 position-absolute">
            <svg xmlns="http://www.w3.org/2000/svg" width="649" height="563" viewBox="0 0 649 563" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M585.74 -116.863C640.979 -48.7975 623.136 48.5324 629.337 135.993C635.593 224.22 672.772 322.278 622.018 394.758C571.478 466.933 465.217 460.043 382.051 489.136C303.542 516.6 230.934 575.567 149.228 560.148C65.794 544.404 1.36918 477.704 -47.2508 408.125C-93.0136 342.635 -115.876 263.802 -113.674 183.913C-111.608 108.968 -74.924 42.6223 -35.4192 -21.1278C4.48218 -85.5179 46.805 -150.527 114.563 -184.4C186.052 -220.138 267.683 -223.525 346.771 -212.189C433.806 -199.714 530.352 -185.114 585.74 -116.863Z"
                    fill="#978EF4" fill-opacity="0.06" />
            </svg>
        </div>
        <div class="sign-up-bg-shaps-2 position-absolute">
            <svg xmlns="http://www.w3.org/2000/svg" width="683" height="478" viewBox="0 0 683 478" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M0.655098 414.343C3.70128 326.736 81.0781 265.057 133.841 195.03C187.066 124.388 223.429 26.0249 309.305 4.69912C394.818 -16.5365 470.425 58.4461 552.248 91.126C629.489 121.976 722.97 125.194 774.459 190.481C827.038 257.149 831.816 349.759 822.785 434.161C814.284 513.602 779.751 588.066 725.624 646.864C674.846 702.025 603.61 727.964 531.951 750.094C459.573 772.446 384.962 793.674 311.619 774.718C234.238 754.718 170.455 703.662 118.259 643.172C60.8176 576.604 -2.39936 502.188 0.655098 414.343Z"
                    fill="#978EF4" fill-opacity="0.06" />
            </svg>
        </div>

        <div class="sign-up-wrapper position-relative d-flex align-items-center justify-content-center flex-column rounded-8">
            <h3 class="text-center">Forget Password</h3>
            <form action={{ route('user.password.forget.email') }} method="POST" id="login" class="w-100">
                @csrf
                @method('POST')
                <div class="input-icon-wrapper position-relative">
                    <span class="input-icons position-absolute">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none">
                            <g clip-path="url(#clip0_46_2310)">
                                <path
                                    d="M21.3333 4H2.66665C2.31302 4 1.97389 4.14048 1.72384 4.39052C1.47379 4.64057 1.33331 4.97971 1.33331 5.33333V18.6667C1.33331 19.0203 1.47379 19.3594 1.72384 19.6095C1.97389 19.8595 2.31302 20 2.66665 20H21.3333C21.6869 20 22.0261 19.8595 22.2761 19.6095C22.5262 19.3594 22.6666 19.0203 22.6666 18.6667V5.33333C22.6666 4.97971 22.5262 4.64057 22.2761 4.39052C22.0261 4.14048 21.6869 4 21.3333 4ZM20.3066 18.6667H3.77331L8.43998 13.84L7.47998 12.9133L2.66665 17.8933V6.34667L10.9533 14.5933C11.2031 14.8417 11.5411 14.9811 11.8933 14.9811C12.2456 14.9811 12.5835 14.8417 12.8333 14.5933L21.3333 6.14V17.8067L16.4266 12.9L15.4866 13.84L20.3066 18.6667ZM3.53998 5.33333H20.2533L11.8933 13.6467L3.53998 5.33333Z"
                                    fill="#8C8C8C" />
                            </g>
                            <defs>
                                <clipPath id="clip0_46_2310">
                                    <rect width="24" height="24" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <input type="email" name="email" class="d-block w-100" placeholder="Enter Email"
                        value="{{ old('email', 'alijawaidofficial.pk@gmail.com') }}" required />
                </div>
                <button type="submit" class="btn d-block w-100 mt-4" type="submit">Login</button>
            </form>
        </div>
    </div>
    <!-- /Content -->
@endsection
