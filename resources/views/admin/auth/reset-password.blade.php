{{-- Layout --}}
@extends('admin.layouts.app')

{{-- Title --}}
@section('title', 'Reset Password')

{{-- Styles --}}
@section('styles')
@endsection

{{-- Scripts --}}
@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const showBtns = document.querySelectorAll(".show-password-btn");

            showBtns.forEach(btn => {
                btn.addEventListener("click", function() {
                    const passwordInput = this.previousElementSibling;

                    console.log(passwordInput);

                    if (passwordInput.type === "password") {
                        this.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" height="16" width="20" viewBox="0 0 640 512">
                            <path fill="#8c8c8c" d="M320 400c-75.9 0-137.3-58.7-142.9-133.1L72.2 185.8c-13.8 17.3-26.5 35.6-36.7 55.6a32.4 32.4 0 0 0 0 29.2C89.7 376.4 197.1 448 320 448c26.9 0 52.9-4 77.9-10.5L346 397.4a144.1 144.1 0 0 1 -26 2.6zm313.8 58.1l-110.6-85.4a331.3 331.3 0 0 0 81.3-102.1 32.4 32.4 0 0 0 0-29.2C550.3 135.6 442.9 64 320 64a308.2 308.2 0 0 0 -147.3 37.7L45.5 3.4A16 16 0 0 0 23 6.2L3.4 31.5A16 16 0 0 0 6.2 53.9l588.4 454.7a16 16 0 0 0 22.5-2.8l19.6-25.3a16 16 0 0 0 -2.8-22.5zm-183.7-142l-39.3-30.4A94.8 94.8 0 0 0 416 256a94.8 94.8 0 0 0 -121.3-92.2A47.7 47.7 0 0 1 304 192a46.6 46.6 0 0 1 -1.5 10l-73.6-56.9A142.3 142.3 0 0 1 320 112a143.9 143.9 0 0 1 144 144c0 21.6-5.3 41.8-13.9 60.1z"/>
                        </svg>
                    `;
                        passwordInput.type = "text";
                    } else {
                        this.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" height="16" width="18" viewBox="0 0 576 512">
                            <path fill="#8c8c8c" d="M572.5 241.4C518.3 135.6 410.9 64 288 64S57.7 135.6 3.5 241.4a32.4 32.4 0 0 0 0 29.2C57.7 376.4 165.1 448 288 448s230.3-71.6 284.5-177.4a32.4 32.4 0 0 0 0-29.2zM288 400a144 144 0 1 1 144-144 143.9 143.9 0 0 1 -144 144zm0-240a95.3 95.3 0 0 0 -25.3 3.8 47.9 47.9 0 0 1 -66.9 66.9A95.8 95.8 0 1 0 288 160z" />
                        </svg>
                    `;
                        passwordInput.type = "password";
                    }
                });
            });
        });
    </script>
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

        <div
            class="sign-up-wrapper position-relative d-flex align-items-center justify-content-center flex-column rounded-6">
            <h3 class="text-center">Reset Password</h3>
            <form action={{ route('admin.password.update') }} method="POST" id="login" class="w-100">
                @csrf
                @method('POST')

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="input-icon-wrapper input-show-password position-relative">
                    <span class="input-icons position-absolute">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none">
                            <path
                                d="M13.5 15C13.5 15.3978 13.342 15.7794 13.0607 16.0607C12.7794 16.342 12.3978 16.5 12 16.5C11.6022 16.5 11.2206 16.342 10.9393 16.0607C10.658 15.7794 10.5 15.3978 10.5 15C10.5 14.6022 10.658 14.2206 10.9393 13.9393C11.2206 13.658 11.6022 13.5 12 13.5C12.3978 13.5 12.7794 13.658 13.0607 13.9393C13.342 14.2206 13.5 14.6022 13.5 15ZM8 8V7C8 5.93913 8.42143 4.92172 9.17157 4.17157C9.92172 3.42143 10.9391 3 12 3C13.0609 3 14.0783 3.42143 14.8284 4.17157C15.5786 4.92172 16 5.93913 16 7V8H16.875C17.7038 8 18.4987 8.32924 19.0847 8.91529C19.6708 9.50134 20 10.2962 20 11.125V18.875C20 19.7038 19.6708 20.4987 19.0847 21.0847C18.4987 21.6708 17.7038 22 16.875 22H7.125C6.2962 22 5.50134 21.6708 4.91529 21.0847C4.32924 20.4987 4 19.7038 4 18.875V11.125C4 10.2962 4.32924 9.50134 4.91529 8.91529C5.50134 8.32924 6.2962 8 7.125 8H8ZM9.25 7V8H14.75V7C14.75 6.27065 14.4603 5.57118 13.9445 5.05546C13.4288 4.53973 12.7293 4.25 12 4.25C11.2707 4.25 10.5712 4.53973 10.0555 5.05546C9.53973 5.57118 9.25 6.27065 9.25 7ZM7.125 9.25C6.62772 9.25 6.15081 9.44754 5.79917 9.79917C5.44754 10.1508 5.25 10.6277 5.25 11.125V18.875C5.25 19.3723 5.44754 19.8492 5.79917 20.2008C6.15081 20.5525 6.62772 20.75 7.125 20.75H16.875C17.3723 20.75 17.8492 20.5525 18.2008 20.2008C18.5525 19.8492 18.75 19.3723 18.75 18.875V11.125C18.75 10.6277 18.5525 10.1508 18.2008 9.79917C17.8492 9.44754 17.3723 9.25 16.875 9.25H7.125Z"
                                fill="#8C8C8C" />
                        </svg>
                    </span>
                    <input type="password" name="password" id="password_input" class="d-block w-100 rounded"
                        placeholder="Password" required />
                    <button type="button" class="show-password-btn position-absolute">
                        <svg xmlns="http://www.w3.org/2000/svg" height="16" width="18" viewBox="0 0 576 512">
                            <path fill="#8c8c8c"
                                d="M572.5 241.4C518.3 135.6 410.9 64 288 64S57.7 135.6 3.5 241.4a32.4 32.4 0 0 0 0 29.2C57.7 376.4 165.1 448 288 448s230.3-71.6 284.5-177.4a32.4 32.4 0 0 0 0-29.2zM288 400a144 144 0 1 1 144-144 143.9 143.9 0 0 1 -144 144zm0-240a95.3 95.3 0 0 0 -25.3 3.8 47.9 47.9 0 0 1 -66.9 66.9A95.8 95.8 0 1 0 288 160z" />
                        </svg>
                    </button>
                </div>

                <div class="input-icon-wrapper input-show-password position-relative">
                    <span class="input-icons position-absolute">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none">
                            <path
                                d="M13.5 15C13.5 15.3978 13.342 15.7794 13.0607 16.0607C12.7794 16.342 12.3978 16.5 12 16.5C11.6022 16.5 11.2206 16.342 10.9393 16.0607C10.658 15.7794 10.5 15.3978 10.5 15C10.5 14.6022 10.658 14.2206 10.9393 13.9393C11.2206 13.658 11.6022 13.5 12 13.5C12.3978 13.5 12.7794 13.658 13.0607 13.9393C13.342 14.2206 13.5 14.6022 13.5 15ZM8 8V7C8 5.93913 8.42143 4.92172 9.17157 4.17157C9.92172 3.42143 10.9391 3 12 3C13.0609 3 14.0783 3.42143 14.8284 4.17157C15.5786 4.92172 16 5.93913 16 7V8H16.875C17.7038 8 18.4987 8.32924 19.0847 8.91529C19.6708 9.50134 20 10.2962 20 11.125V18.875C20 19.7038 19.6708 20.4987 19.0847 21.0847C18.4987 21.6708 17.7038 22 16.875 22H7.125C6.2962 22 5.50134 21.6708 4.91529 21.0847C4.32924 20.4987 4 19.7038 4 18.875V11.125C4 10.2962 4.32924 9.50134 4.91529 8.91529C5.50134 8.32924 6.2962 8 7.125 8H8ZM9.25 7V8H14.75V7C14.75 6.27065 14.4603 5.57118 13.9445 5.05546C13.4288 4.53973 12.7293 4.25 12 4.25C11.2707 4.25 10.5712 4.53973 10.0555 5.05546C9.53973 5.57118 9.25 6.27065 9.25 7ZM7.125 9.25C6.62772 9.25 6.15081 9.44754 5.79917 9.79917C5.44754 10.1508 5.25 10.6277 5.25 11.125V18.875C5.25 19.3723 5.44754 19.8492 5.79917 20.2008C6.15081 20.5525 6.62772 20.75 7.125 20.75H16.875C17.3723 20.75 17.8492 20.5525 18.2008 20.2008C18.5525 19.8492 18.75 19.3723 18.75 18.875V11.125C18.75 10.6277 18.5525 10.1508 18.2008 9.79917C17.8492 9.44754 17.3723 9.25 16.875 9.25H7.125Z"
                                fill="#8C8C8C" />
                        </svg>
                    </span>
                    <input type="password" name="password_confirmation" id="confirm_password_input"
                        class="d-block w-100 rounded" placeholder="Confirm Password" required />
                    <button type="button" class="show-password-btn position-absolute">
                        <svg xmlns="http://www.w3.org/2000/svg" height="16" width="18" viewBox="0 0 576 512">
                            <path fill="#8c8c8c"
                                d="M572.5 241.4C518.3 135.6 410.9 64 288 64S57.7 135.6 3.5 241.4a32.4 32.4 0 0 0 0 29.2C57.7 376.4 165.1 448 288 448s230.3-71.6 284.5-177.4a32.4 32.4 0 0 0 0-29.2zM288 400a144 144 0 1 1 144-144 143.9 143.9 0 0 1 -144 144zm0-240a95.3 95.3 0 0 0 -25.3 3.8 47.9 47.9 0 0 1 -66.9 66.9A95.8 95.8 0 1 0 288 160z" />
                        </svg>
                    </button>
                </div>

                <button type="submit" class="btn d-block w-100 mt-4" type="submit">Set New Password</button>
            </form>
        </div>
    </div>
    <!-- /Content -->
@endsection