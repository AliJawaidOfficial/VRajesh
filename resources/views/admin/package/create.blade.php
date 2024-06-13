{{-- Layout --}}
@extends('admin.layouts.app')

{{-- Title --}}
@section('title', 'Packages')

{{-- Styles --}}
@section('styles')
@endsection

{{-- Vendor Scripts --}}
@section('scripts')
@endsection

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">
        <form action="{{ route('admin.package.store') }}" method="POST">
            @csrf
            @method('POST')

            <div class="card">
                <div class="card-header bg-dark text-light">
                    <h3 class="mb-0">Package Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" id="name"
                                    class="form-control" placeholder="Enter name" required />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row d-flex px-3">
                                @php($i = 1)
                                <div class="col-xl-3 col-md-4 col-sm-6 p-0 border border-dark">
                                    <h2 class="bg-dark text-light h6 p-2">Facebook / Instagram</h2>
                                    @foreach ($facebookPermissions as $permission)
                                        <div class="ps-2">
                                            <div class="form-switch p-0 pe-2 d-flex flex-row-reverse flex-wrap justify-content-between mb-2">
                                                <input type="checkbox" name="permissions[]"
                                                    value="{{ $permission['name'] }}"
                                                    {{ old('permissions') ? (in_array($permission['name'], old('permissions')) ? 'checked' : '') : '' }}
                                                    id="permission_{{ $i }}" class="form-check-input" />
                                                <label class="form-check-label"
                                                    for="permission_{{ $i }}">{{ $permission['title'] }}</label>
                                            </div>
                                        </div>
                                        @php($i++)
                                    @endforeach
                                </div>
                                <div class="col-xl-3 col-md-4 col-sm-6 p-0 border border-dark">
                                    <h2 class="bg-dark text-light h6 p-2">Google</h2>
                                    @foreach ($googlePermissions as $permission)
                                        <div class="ps-2">
                                            <div class="form-switch p-0 pe-2 d-flex flex-row-reverse flex-wrap justify-content-between mb-2">
                                                <input type="checkbox" name="permissions[]"
                                                    value="{{ $permission['name'] }}"
                                                    {{ old('permissions') ? (in_array($permission['name'], old('permissions')) ? 'checked' : '') : '' }}
                                                    id="permission_{{ $i }}" class="form-check-input" />
                                                <label class="form-check-label"
                                                    for="permission_{{ $i }}">{{ $permission['title'] }}</label>
                                            </div>
                                        </div>
                                        @php($i++)
                                    @endforeach
                                </div>
                                <div class="col-xl-3 col-md-4 col-sm-6 p-0 border border-dark">
                                    <h2 class="bg-dark text-light h6 p-2">LinkedIn</h2>
                                    @foreach ($linkedInPermissions as $permission)
                                        <div class="ps-2">
                                            <div class="form-switch p-0 pe-2 d-flex flex-row-reverse flex-wrap justify-content-between mb-2">
                                                <input type="checkbox" name="permissions[]"
                                                    value="{{ $permission['name'] }}"
                                                    {{ old('permissions') ? (in_array($permission['name'], old('permissions')) ? 'checked' : '') : '' }}
                                                    id="permission_{{ $i }}" class="form-check-input" />
                                                <label class="form-check-label"
                                                    for="permission_{{ $i }}">{{ $permission['title'] }}</label>
                                            </div>
                                        </div>
                                        @php($i++)
                                    @endforeach
                                </div>
                                <div class="col-xl-3 col-md-4 col-sm-6 p-0 border border-dark">
                                    <h2 class="bg-dark text-light h6 p-2">Other</h2>
                                    @foreach ($otherPermissions as $permission)
                                        <div class="ps-2">
                                            <div class="form-switch p-0 pe-2 d-flex flex-row-reverse flex-wrap justify-content-between mb-2">
                                                <input type="checkbox" name="permissions[]"
                                                    value="{{ $permission['name'] }}"
                                                    {{ old('permissions') ? (in_array($permission['name'], old('permissions')) ? 'checked' : '') : '' }}
                                                    id="permission_{{ $i }}" class="form-check-input" />
                                                <label class="form-check-label"
                                                    for="permission_{{ $i }}">{{ $permission['title'] }}</label>
                                            </div>
                                        </div>
                                        @php($i++)
                                    @endforeach
                                </div>
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
