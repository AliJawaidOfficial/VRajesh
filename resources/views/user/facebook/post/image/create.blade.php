{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Add New Post - ')

{{-- Styles --}}
@section('styles')
@endsection

{{-- Vendor Scripts --}}
@section('scripts')
    <script>
        document.getElementById('fileInput').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const imagePreview = document.getElementById('imagePreview');
            const videoPreview = document.getElementById('videoPreview');

            if (file) {
                const fileType = file.type;
                const fileURL = URL.createObjectURL(file);

                if (fileType.startsWith('image/')) {
                    imagePreview.src = fileURL;
                    imagePreview.style.display = 'block';
                    videoPreview.style.display = 'none';
                } else if (fileType.startsWith('video/')) {
                    videoPreview.src = fileURL;
                    videoPreview.style.display = 'block';
                    imagePreview.style.display = 'none';
                } else {
                    imagePreview.style.display = 'none';
                    videoPreview.style.display = 'none';
                }
            }
        });
    </script>
@endsection

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">
        <div class="main-inventory-heading d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center justify-content-center">
                <span class="heading">Create Facebook Post</span>
            </div>
        </div>

        <div class="preview-container mt-3" style="object-fit: contain;">
            <img id="imagePreview" src="" alt=""
                style="display:none; width: 100%; height: auto;border-radius: 12px">
            <video id="videoPreview" controls style="display:none; max-width: 100%; height: auto;"></video>
        </div>

        <form action="{{ route('user.facebook.post.image.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')
            <div class="inventory-main-input-wrapper">
                <div class="row justify-content-between">
                    <div class="col-md-12">
                        <label>
                            <span>Image</span>
                            <input type="file" name="image" accept="image/*" id="fileInput" required />
                        </label>
                    </div>
                </div>
                <div class="row justify-content-between">
                    <div class="col-md-12">
                        <label for="description">
                            <span>Description</span>
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
