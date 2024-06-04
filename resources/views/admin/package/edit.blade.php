{{-- Layout --}}
@extends('admin.layouts.app')

{{-- Title --}}
@section('title', $data->name . ' - Edit Package')

{{-- Styles --}}
@section('styles')
@endsection

{{-- Vendor Scripts --}}
@section('scripts')
@endsection

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">
        <form action="{{ route('admin.package.update', $data->id) }}" method="POST">
            @csrf
            @method('POST')

            <div class="card">
                <div class="card-header bg-dark text-light">
                    <h3 class="mb-0">Package Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" value="{{ old('name', $data->name) }}" id="name"
                                    class="form-control" placeholder="Enter name" required />
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="card mt-3">
                <div class="card-header bg-dark text-light">
                    <h3 class="mb-0">Scope</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($permissions as $permission)
                            <div class="col-lg-3 col-md-4">
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                        id="permission_{{ $permission->id }}" class="form-check-input"
                                        {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                        {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-dark">Save</button>
                </div>
            </div>
        </form>
    </section>
@endsection
