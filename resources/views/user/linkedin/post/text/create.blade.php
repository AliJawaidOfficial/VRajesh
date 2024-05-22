{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Create Facebook Post - ')

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
                <span class="heading">Create LinkedIn Post</span>
            </div>
        </div>

        <div class="preview-container mt-3" style="object-fit: contain;">
            <img id="imagePreview" src="" alt=""
                style="display:none; width: 100%; height: auto;border-radius: 12px">
            <video id="videoPreview" controls style="display:none; max-width: 100%; height: auto;"></video>
        </div>

        <form action="{{ route('user.linkedin.post.text.store') }}" method="POST">
            @csrf
            @method('POST')
            <div class="inventory-main-input-wrapper">
                <div class="row justify-content-between">
                    <div class="col-md-12">
                        <label for="description">
                            <span>Text</span>
                            <textarea name="text" id="description"></textarea>
                        </label>
                    </div>
                </div>
            </div>
    
            <div class="perform-action-btn d-flex align-items-center justify-content-end">
                <div class="d-flex align-items-center justify-content-center">
                    <button type="reset" class="btn btn-secondary">Discard</button>
                    <button type="submit" class="btn btn-dark">Post</button>
                </div>
            </div>
        </form>
    </section>
@endsection
