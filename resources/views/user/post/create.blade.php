{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Create New Post')

{{-- Styles --}}
@section('styles')
    <style>
        .preview-container {
            background-color: #1e1b1b;
            border-left: 4px solid #0073e6;
            padding: 16px;
            border-radius: 4px;
            height: 400px;
            /* Fixed height for consistency */
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .preview-container img,
        .preview-container video {
            max-width: 100%;
            max-height: 100%;
            border-radius: 4px;
            position: absolute;
        }
    </style>
@endsection

{{-- Scripts --}}
@section('scripts')
@endsection

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">
        <div class="h1 p-4 bg-white text-center">
            <h1>Create Post</h1>
        </div>

        <div class="row align-items-stretch mt-3">
            <div class="col-md-6">
                <form id="postForm" class="p-4 bg-white rounded" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="inventory-main-input-wrapper">
                        <div class="w-100 mb-4">
                            <label class="d-block w-100">
                                <span class="d-block w-100">Title</span>
                                <input class="d-block w-100 form-control" type="text" name="title"
                                    placeholder="Enter your title here" required />
                            </label>
                        </div>
                        <div class="w-100 my-4">
                            <label class="d-block w-100">
                                <span class="d-block w-100">Media</span>
                                <input class="d-block w-100 form-control" type="file" name="media"
                                    accept="video/*, image/*" id="mediaInput" />
                            </label>
                        </div>
                        <div class="w-100 my-4 d-flex align-items-center gap-3">
                            <label class="d-inline-block">
                                <input type="checkbox" name="on_facebook" value="1" class="form-check-input" />
                                <span>Facebook</span>
                            </label>

                            <label class="d-inline-block">
                                <input type="checkbox" name="on_linkedin" value="1" class="form-check-input" />
                                <span>LinkedIn</span>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-center gap-4">
                        <button type="reset" class="btn btn-secondary w-100">Discard</button>
                        <button type="submit" class="btn btn-primary w-100">Post</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content d-flex align-items-center justify-content-center p-5">
                <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Preview Script --}}
    <script>
        document.getElementById('mediaInput').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const imagePreview = document.getElementById('imagePreview');
            const videoPreview = document.getElementById('videoPreview');
            const previewText = document.getElementById('previewText');

            if (file) {
                const fileType = file.type;
                const fileURL = URL.createObjectURL(file);

                if (fileType.startsWith('image/')) {
                    imagePreview.src = fileURL;
                    imagePreview.style.display = 'block';
                    videoPreview.style.display = 'none';
                    previewText.style.display = 'none';
                } else if (fileType.startsWith('video/')) {
                    videoPreview.src = fileURL;
                    videoPreview.style.display = 'block';
                    imagePreview.style.display = 'none';
                    previewText.style.display = 'none';
                } else {
                    imagePreview.style.display = 'none';
                    videoPreview.style.display = 'none';
                    previewText.style.display = 'block';
                }
            } else {
                imagePreview.style.display = 'none';
                videoPreview.style.display = 'none';
                previewText.style.display = 'block';
            }
        });

        document.getElementById('postForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission
            const modal = new bootstrap.Modal(document.getElementById('exampleModal'));
            modal.show(); // Show the modal
            // After showing the modal, submit the form
            this.submit();
        });
    </script>
@endsection
