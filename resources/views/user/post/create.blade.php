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
                <span class="heading">Create New Post</span>
            </div>
        </div>

        <div class="preview-container mt-3" style="object-fit: contain;">
            <img id="imagePreview" src="" alt=""
                style="display:none; width: 100%; height: auto;border-radius: 12px">
            <video id="videoPreview" controls style="display:none; max-width: 100%; height: auto;"></video>
        </div>

        <div class="row">
            <div class="col-md-6">
                <form action="{{ route('user.post.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="inventory-main-input-wrapper">
                        <div class="row">
                            <div class="col-md-12">
                                <label>
                                    <span>Title</span>
                                    <input type="text" name="title" required />
                                </label>
                            </div>
                            <div class="col-md-12">
                                <label>
                                    <span>Media</span>
                                    <input type="file" name="media" accept="video/*, image/*" class="form-control" />
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="d-inline-flex align-items-center gap-1 justify-content-start w-auto">
                                    <input type="checkbox" name="on_facebook" value="1" />
                                    <span class="m-0">Facebook</span>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="d-inline-flex align-items-center gap-1 justify-content-start w-auto">
                                    <input type="checkbox" name="on_linkedin" value="1" />
                                    <span class="m-0">LinkedIn</span>
                                </label>
                            </div>
                            {{-- <div class="col-md-12">
                                <label class="d-inline-flex align-items-center gap-1 justify-content-start w-100">
                                    <input type="checkbox" name="post_now" value="1" />
                                    <span class="m-0 w-100">Post Now?</span>
                                </label>
                            </div>
                            <div class="col-md-12">
                                <label>
                                    <span>Post On</span>
                                    <input type="datetime-local" name="post_on" id="post_on"
                                        value="{{ now() }}" />
                                </label>
                            </div> --}}
                        </div>
                    </div>

                    <div class="perform-action-btn d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <button type="reset" class="btn btn-light">Discard</button>
                            <button type="submit" class="btn btn-primary btn-theme">Post</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <div class="card h-100 d-flex justify-content-center align-items-center bg-dark rounded-4">
                    <h6 class="text-light my-4">PREVIEW</h6>
                </div>
            </div>
        </div>
    </section>
@endsection
