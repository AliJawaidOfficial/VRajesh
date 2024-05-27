{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Create New Post')

{{-- Styles --}}
@section('styles')
    <style>
        #postPreview,
        #postForm {
            height: calc(100vh - (75px + 96px + 40px));
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        #postPreview {
            justify-content: flex-start;
        }

        .form-control {
            flex: 1;
            resize: none;
        }

        .textarea-wrapper {
            flex: 1;
        }

        /* Instagram Post Preview */
        .instagram-post {
            max-width: 600px;
            margin: 20px auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
        }

        .instagram-post .post-header,
        .instagram-post .post-footer {
            padding: 15px;
        }

        .instagram-post .post-header {
            display: flex;
            align-items: center;
        }

        .instagram-post .post-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .instagram-post .post-header .username {
            font-weight: bold;
        }

        .instagram-post .post-image {
            width: 100%;
            height: auto;
        }

        .instagram-post .post-footer .actions {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .instagram-post .post-footer .actions span {
            font-size: 24px;
            margin-right: 15px;
            cursor: pointer;
        }

        .instagram-post .post-footer .likes,
        .instagram-post .post-footer .caption,
        .instagram-post .post-footer .comments,
        .instagram-post .post-footer .add-comment {
            margin-bottom: 10px;
        }

        .instagram-post .post-footer .likes {
            font-weight: bold;
        }

        .instagram-post .post-footer .add-comment input {
            width: 100%;
            border: none;
            border-top: 1px solid #ddd;
            padding: 10px 0;
        }

        .post-media img,
        .post-media video {
            width: 100%;
            display: none;
        }

        .post-media img.active,
        .post-media video.active {
            display: block;
        }
    </style>
@endsection

{{-- Scripts --}}
@section('scripts')
    <script>
        document.querySelectorAll('.toggle-post').forEach((toggleBtn) => {
            toggleBtn.addEventListener('click', (e) => {
                const target = document.getElementById(e.target.dataset.bsToggle);
                if (e.target.checked) {
                    target.style.display = "block";
                } else {
                    target.style.display = "none";
                }
            });
        });

        document.getElementById("postDescription").addEventListener("keyup", function(e) {
            const description = e.target.value.trim() ? e.target.value : "This is a default post description";
            document.querySelectorAll(".post-description p").forEach((postDescription) => {
                postDescription.textContent = description;
            });
        });

        document.getElementById('mediaInput').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const url = e.target.result;
                    const isImage = file.type.startsWith('image/');
                    document.querySelectorAll('.post-media img').forEach(img => img.classList.add('d-none'));
                    document.querySelectorAll('.post-media video').forEach(video => video.classList.add(
                        'd-none'));

                    if (isImage) {
                        document.querySelectorAll('.post-media img').forEach(img => {
                            img.src = url;
                            img.classList.remove('d-none');
                            img.classList.add('active');
                        });
                        document.querySelectorAll('.post-media video').forEach(video => {
                            video.classList.remove('active');
                        });
                    } else {
                        document.querySelectorAll('.post-media video').forEach(video => {
                            video.src = url;
                            video.classList.remove('d-none');
                            video.classList.add('active');
                        });
                        document.querySelectorAll('.post-media img').forEach(img => {
                            img.classList.remove('active');
                        });
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                document.querySelectorAll('.like-count').forEach((likeCount) => {
                    likeCount.textContent = parseInt(likeCount.textContent) + 1;
                });
            }
        });

        // Set default description on page load
        document.addEventListener('DOMContentLoaded', function() {
            const defaultDescription = "This is a default post description";
            document.querySelectorAll(".post-description p").forEach((postDescription) => {
                if (!postDescription.textContent.trim()) {
                    postDescription.textContent = defaultDescription;
                }
            });
        });

        // Schedule Form
        $("#scheduleForm").submit(function(e) {
            e.preventDefault();
            $("#post_schedule_date").val($("#scheduleDate").val());
            $("#post_schedule_time").val($("#scheduleTime").val());
            $("#scheduleModal").modal("hide");
            // $("#postForm").submit();
        });

        // Post Form
        $("#postForm").submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "{{ route('user.post.store') }}",
                data: new FormData(this),
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $("#exampleModal").modal("show");
                },
                success: function(response) {
                    if (response.status == 200) {
                        if ($("#post_schedule_date").val() != null) {
                            window.location.href = "{{ route('user.post.scheduled') }}";
                        } else {
                            window.location.href = "{{ route('user.post.index') }}";
                        }
                    } else {
                        toastr.error(response.error);
                    }

                    $("#exampleModal").modal("hide");
                }
            });
        });

        // Draft Form
        $('#postForm button[name="draft"]').click(function(e) {
            let form = $('#postForm')[0];
            $.ajax({
                type: "POST",
                url: "{{ route('user.post.draft.store') }}",
                data: new FormData(form),
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $("#exampleModal").modal("show");
                },
                success: function(response) {

                    if (response.status == 200) {
                        window.location.href = "{{ route('user.post.draft') }}";
                    } else {
                        toastr.error(response.error);
                    }

                    $("#exampleModal").modal("hide");
                }
            });
        });

    </script>
@endsection

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">

        <div class="row align-items-stretch mt-2">
            <div class="col-md-8">
                <div class="h3 p-3 mb-4 bg-white text-center rounded-8">
                    <h3>Create Post</h3>
                </div>

                <form id="postForm" class="p-4 bg-white rounded-8" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="date" name="schedule_date" id="post_schedule_date" class="d-none" />
                    <input type="time" name="schedule_time" id="post_schedule_time" class="d-none" />

                    <div class="d-flex flex-column flex-grow-1">
                        <div class="mb-4">
                            <input class="d-block h-100 w-100 form-control" name="title"
                                placeholder="Enter your title here" required />
                        </div>
                        <div class="textarea-wrapper my-2">
                            <textarea class="d-block h-100 w-100 form-control" id="postDescription" name="description"
                                placeholder="Enter your post description" required></textarea>
                        </div>

                        <div class="w-100 my-2 d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <label class="d-inline-block">
                                    <input type="checkbox" name="on_facebook" value="1" data-bs-toggle="facebook-post"
                                        class="form-check-input toggle-post" />
                                    <span class="d-inline-block ms-1">Facebook</span>
                                </label>

                                <label class="d-inline-block">
                                    <input type="checkbox" name="on_linkedin" value="1" data-bs-toggle="linkedin-post"
                                        class="form-check-input toggle-post" />
                                    <span class="d-inline-block ms-1">LinkedIn</span>
                                </label>

                                <label class="d-inline-block">
                                    <input type="checkbox" name="on_instagram" value="1"
                                        data-bs-toggle="instagram-post" class="form-check-input toggle-post" />
                                    <span class="d-inline-block ms-1">Instagram</span>
                                </label>
                            </div>

                            <div>
                                <label for="mediaInput" class="btn btn-transparent text-primary"><i class="fas fa-paperclip"
                                        style="font-size: 20px"></i></label>
                                <input class="d-block w-100 form-control d-none" type="file" name="media"
                                    accept="video/*, image/*" id="mediaInput" />
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-2 gap-4">
                        <div>
                            <button type="button" name="draft"
                                class="btn btn-secondary btn-custom btn-custom-secondary">Save as Draft</button>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-primary btn-custom btn-custom-primary"
                                data-bs-toggle="modal" data-bs-target="#scheduleModal">Schedule</button>
                            <button type="submit" name="post"
                                class="btn btn-primary btn-custom btn-custom-primary">Post</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-4">
                <div class="h3 p-3 mb-4 bg-white text-center rounded-8">
                    <h3>Post Preview</h3>
                </div>
                <div class="w-100 bg-white p-4 rounded-8 overflow-scroll d-flex flex-column gap-3" id="postPreview">
                    {{-- We will list facebook, instagram and linkedin post will look like --}}
                    <div class="card rounded" id="facebook-post" style="display: none">
                        <div
                            class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-2">
                            <div class="d-flex align-items-center">
                                <img src="https://via.placeholder.com/50" alt="Profile Picture" class="rounded-circle">
                                <div class="ms-2">
                                    <h6 class="mb-0 line-clap" style="-webkit-line-clamp: 1;">DUET Media - DM</h6>
                                    <small class="text-muted">1h · <img src="{{ asset('assets/images/icons/globe.png') }}"
                                            width="16" alt=""></small>
                                </div>
                            </div>

                            <div>
                                <img src="{{ asset('assets/images/icons/three-dot-icons.png') }}" width="25"
                                    style="min-width: 25px" alt="">
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="px-4 mb-3 post-description">
                                <p class="mb-0"></p>
                            </div>

                            <div class="post-media">
                                <img src="https://via.placeholder.com/500" alt="Post Image" class="img-fluid"
                                    id="postImage">
                                <video controls class="w-100 d-none" id="postVideo">
                                    <source src="#" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <div>
                                <span>&#128077;&#128514;&#128546;</span>
                                <span class="like-count">20</span>
                            </div>
                            <div>
                                <span>8 comments</span>
                                <span class="ms-3">44 shares</span>
                            </div>
                        </div>
                    </div>
                    <div class="card rounded mt-4" id="linkedin-post" style="display: none">
                        <div
                            class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-2">
                            <div class="d-flex align-items-center">
                                <img src="https://via.placeholder.com/50" alt="Profile Picture" class="rounded-circle">
                                <div class="ms-2">
                                    <h6 class="mb-0">Hania Mehdi</h6>
                                    <small class="text-muted line-clap" style="-webkit-line-clamp: 1;">HR Professional |
                                        Head Hunter | Technical & Non Technical Recruiter | International...</small>
                                    <div><small class="text-muted">25m · Edited</small></div>
                                </div>
                            </div>
                            <div style="width: fit-content">
                                <img src="{{ asset('assets/images/icons/three-dot-icons.png') }}" style="min-width: 25px"
                                    width="25" alt="">
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="px-4 mb-3 post-description">
                                <p class="mb-0"></p>
                            </div>
                            <div class="post-media">
                                <img src="https://via.placeholder.com/500" alt="Post Image" class="img-fluid"
                                    id="postImage">
                                <video controls class="w-100 d-none" id="postVideo">
                                    <source src="video.mp4" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <div>
                                <span>&#128077;&#128514;&#128546;</span>
                                <span class="like-count">21</span>
                            </div>
                            <div>1 Repost</div>
                        </div>
                    </div>

                    <div class="card rounded mt-4" id="instagram-post" style="display: none">
                        <div
                            class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-2">
                            <div class="d-flex align-items-center">
                                <img src="https://via.placeholder.com/50" alt="Profile Picture" class="rounded-circle">
                                <div class="ms-2">
                                    <h6 class="mb-0">karuneshtalwar</h6>
                                    <small class="text-muted">Original audio</small>
                                </div>
                            </div>
                            <div style="width: fit-content">
                                <img src="{{ asset('assets/images/icons/three-dot-icons.png') }}" style="min-width: 25px"
                                    width="25" alt="">
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="post-media">
                                <img src="https://via.placeholder.com/500" class="img-fluid active" id="postImage">
                                <video controls class="w-100 d-none" id="postVideo">
                                    <source src="#" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                        <div class="card-footer d-flex flex-column">
                            <div class="actions">
                                <span>&#9829;</span>
                                <span>&#128172;</span>
                                <span>&#9993;</span>
                            </div>
                            <div class="likes">789,187 likes</div>
                            <div class="caption post-description"><span class="username">karuneshtalwar</span>
                                <p class="d-inline-block">This is a default post description</p>
                            </div>
                            <div class="comments text-muted">View all 6,273 comments</div>
                            <div class="add-comment">
                                <input type="text" placeholder="Add a comment...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Loading Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content d-flex align-items-center justify-content-center p-5">
                <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Modal -->
    <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleModalLabel">Schedule Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="scheduleForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="scheduleDate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="scheduleDate" name="schedule_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="scheduleTime" class="form-label">Time</label>
                            <input type="time" class="form-control" id="scheduleTime" name="schedule_time" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Schedule</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
