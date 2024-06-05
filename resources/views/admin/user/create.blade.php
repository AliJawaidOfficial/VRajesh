{{-- Layout --}}
@extends('admin.layouts.app')

{{-- Title --}}
@section('title', 'New')

{{-- Styles --}}
@section('styles')
@endsection

{{-- Vendor Scripts --}}
@section('scripts')
@endsection

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">
        {{-- <div class="h3 p-3 mb-4 bg-white text-center rounded-6">
            <h3 class="mb-0">Create New Users</h3>
        </div> --}}

        <form action="{{ route('admin.user.store') }}" method="POST">
            @csrf
            @method('POST')

            <div class="card">
                <div class="card-header bg-dark text-light">
                    <h3 class="mb-0">User Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" id="first_name"
                                    class="form-control" placeholder="Enter first name" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" id="last_name"
                                    class="form-control" placeholder="Enter last name" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" id="email"
                                    class="form-control" placeholder="Enter Email" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="meta_email" class="form-label">Meta Valid Email</label>
                                <input type="text" name="meta_email" value="{{ old('meta_email') }}" id="meta_email"
                                    class="form-control" placeholder="Enter Meta Email" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="linkedin_email" class="form-label">LinkedIn Valid Email</label>
                                <input type="text" name="linkedin_email" value="{{ old('linkedin_email') }}"
                                    id="linkedin_email" class="form-control" placeholder="Enter LinkedIn Email" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="package" class="form-label">Packages</label>
                                <select type="text" name="package" id="package" class="form-select">
                                    <option value="">Select</option>
                                    @foreach ($packages as $package)
                                        <option value="{{ $package->id }}"
                                            {{ old('package') == $package->id ? 'selected' : '' }}>{{ $package->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-dark text-light">
                    <h3 class="mb-0">Security</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" value="{{ old('password') }}" id="password"
                                    class="form-control" placeholder="Enter password" minlength="8" maxlength="20"
                                    required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation"
                                    value="{{ old('password_confirmation') }}" id="password_confirmation"
                                    class="form-control" placeholder="Enter password" minlength="8" maxlength="20"
                                    required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-dark">Save</button>
                </div>
            </div>


        </form>
    </section>
@endsection
