{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Create New Post')

{{-- Styles --}}
@section('styles')
    <style>
        #postPreview,
        #postForm {
            height: calc(100vh - (0px + 73px + 40px));
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        #postPreview {
            justify-content: flex-start;
            overflow-y: auto;
            /* Add scrolling to the preview section if content overflows */
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

        .form-control:focus {
            box-shadow: none;
            outline: none;
        }

        .input-tag-title {
            padding: 10px 0px;
            border: none;
            border-bottom: 1px solid #ddd;
            border-radius: 0px;
            font-size: 35px
        }

        .input-tag-description {
            padding: 10px 0px;
            border: none;
            border-bottom: 1px solid #ddd;
            border-radius: 0px;
            font-size: 20px
        }

        .post-description .username {
            font-weight: bold;
            margin-right: 5px;
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

        function updateDescription(description) {
            const formattedDescription = description.replace(/\n/g, '<br>');
            document.querySelectorAll(".post-description").forEach((postDescription) => {
                const isInstagram = postDescription.closest('#instagram-post');
                if (isInstagram) {
                    const usernameSpan = postDescription.querySelector('.username');
                    const username = usernameSpan ? usernameSpan.textContent : 'karuneshtalwar';
                    postDescription.innerHTML = `<span class="username">${username}</span> ${formattedDescription}`;
                } else {
                    postDescription.innerHTML = formattedDescription;
                }
            });
        }

        document.getElementById("postDescription").addEventListener("keyup", function(e) {
            const description = e.target.value.trim() ? e.target.value : "This is a default post description";
            updateDescription(description);
        });

        document.getElementById('mediaInput').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const url = e.target.result;
                    const isImage = file.type.startsWith('image/');

                    if (isImage) {
                        document.querySelectorAll('.post-media img').forEach(img => {
                            img.src = url;
                            img.classList.add('active');
                            img.style.display = 'block';
                        });
                        document.querySelectorAll('.post-media video').forEach(video => {
                            video.classList.remove('active');
                            video.style.display = 'none';
                        });
                    } else {
                        document.querySelectorAll('.post-media video').forEach(video => {
                            video.src = url;
                            video.classList.add('active');
                            video.style.display = 'block';
                        });
                        document.querySelectorAll('.post-media img').forEach(img => {
                            img.classList.remove('active');
                            img.style.display = 'none';
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

        document.getElementById("postDescription").addEventListener("keydown", function(e) {
            if (e.ctrlKey && e.key === "Enter") {
                e.preventDefault(); // Prevent default behavior
                const cursorPosition = e.target.selectionStart;
                const textBefore = e.target.value.substring(0, cursorPosition);
                const textAfter = e.target.value.substring(cursorPosition);
                e.target.value = textBefore + "\n" + textAfter;

                // Update the preview
                const description = e.target.value.trim() ? e.target.value : "This is a default post description";
                updateDescription(description);

                // Move the cursor to the new line
                e.target.selectionEnd = cursorPosition + 1;
            }
        });

        // Set default description on page load
        document.addEventListener('DOMContentLoaded', function() {
            const defaultDescription = "This is a default post description";
            document.querySelectorAll(".post-description").forEach((postDescription) => {
                const isInstagram = postDescription.closest('#instagram-post');
                if (isInstagram) {
                    postDescription.innerHTML =
                        `<span class="username">karuneshtalwar</span> ${defaultDescription.replace(/\n/g, '<br>')}`;
                } else {
                    postDescription.innerHTML = defaultDescription.replace(/\n/g, '<br>');
                }
            });
        });

        // Schedule Form
        $("#scheduleForm").submit(function(e) {
            e.preventDefault();
            $("#post_schedule_date").val($("#scheduleDate").val());
            $("#post_schedule_time").val($("#scheduleTime").val());
            $("#scheduleModal").modal("hide");
            $("#postForm").submit();
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
                <form id="postForm" class="p-4 bg-white rounded-6" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="date" name="schedule_date" id="post_schedule_date" class="d-none" />
                    <input type="time" name="schedule_time" id="post_schedule_time" class="d-none" />

                    <div class="d-flex flex-column flex-grow-1">
                        <div class="mb-3">
                            <input class="input-tag-title d-block h-100 w-100 form-control" name="title"
                                placeholder="Enter your title here" required />
                        </div>
                        <div class="textarea-wrapper my-1">
                            <textarea class="input-tag-description d-block h-100 w-100 form-control" id="postDescription" name="description"
                                placeholder="Enter your post description" required></textarea>
                        </div>

                        <div class="w-100 my-1 d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <label class="d-inline-block">
                                    <input type="checkbox" name="on_facebook" value="1" data-bs-toggle="facebook-post"
                                        class="form-check-input toggle-post"
                                        @if (Auth::guard()->user()->meta_access_token == null) disabled @endif />
                                    <span class="d-inline-block ms-1"><i class="fab fa-facebook-f"></i></span>
                                </label>

                                <label class="d-inline-block">
                                    <input type="checkbox" name="on_linkedin" value="1" data-bs-toggle="linkedin-post"
                                        class="form-check-input toggle-post"
                                        @if (Auth::guard()->user()->linkedin_access_token == null) disabled @endif />
                                    <span class="d-inline-block ms-1"><i class="fab fa-linkedin-in"></i></span>
                                </label>

                                <label class="d-inline-block">
                                    <input type="checkbox" name="on_instagram" value="1"
                                        data-bs-toggle="instagram-post" class="form-check-input toggle-post" />
                                    <span class="d-inline-block ms-1"><i class="fab fa-instagram"></i></span>
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

                    <div class="d-flex align-items-center justify-content-between mt-1 gap-4">
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
                <div class="w-100 bg-white p-4 rounded-6 overflow-scroll d-flex flex-column gap-3" id="postPreview">
                    {{-- Facebook Post Preview --}}
                    <div class="card rounded" id="facebook-post">
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
                                <img src="{{ asset('assets/images/icons/three-dot-icons.png') }}" width="20px"
                                    style="min-width: 20px" alt="">
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="px-4 mb-3 post-description"></div>
                            <div class="post-media">
                                <img src="#" alt="Post Image" class="img-fluid" style="display: none"
                                    id="postImage">
                                <video controls class="w-100" style="display: none" id="postVideo">
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

                    {{-- LinkedIn Post Preview --}}
                    <div class="card rounded mt-4" id="linkedin-post">
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
                                <img src="{{ asset('assets/images/icons/three-dot-icons.png') }}" style="min-width: 20px"
                                    width="20px" alt="">
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="px-4 mb-3 post-description"></div>
                            <div class="post-media">
                                <img src="#" alt="Post Image" style="display: none" class="img-fluid"
                                    id="postImage">
                                <video controls class="w-100" style="display: none" id="postVideo">
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

                    {{-- Instagram Post Preview --}}
                    <div class="card rounded mt-4" id="instagram-post">
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
                                <img src="{{ asset('assets/images/icons/three-dot-icons.png') }}" style="min-width: 20px"
                                    width="20px" alt="">
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="post-media">
                                <img src="#" class="img-fluid" style="display: none" id="postImage">
                                <video controls class="w-100" style="display: none" id="postVideo">
                                    <source src="#" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                        <div class="card-footer d-flex flex-column">
                            <div class="actions">
                                <span><svg aria-label="Like" class="x1lliihq x1n2onr6 xyb1xck" fill="currentColor"
                                        height="24" role="img" viewBox="0 0 24 24" width="24">
                                        <title>Like</title>
                                        <path
                                            d="M16.792 3.904A4.989 4.989 0 0 1 21.5 9.122c0 3.072-2.652 4.959-5.197 7.222-2.512 2.243-3.865 3.469-4.303 3.752-.477-.309-2.143-1.823-4.303-3.752C5.141 14.072 2.5 12.167 2.5 9.122a4.989 4.989 0 0 1 4.708-5.218 4.21 4.21 0 0 1 3.675 1.941c.84 1.175.98 1.763 1.12 1.763s.278-.588 1.11-1.766a4.17 4.17 0 0 1 3.679-1.938m0-2a6.04 6.04 0 0 0-4.797 2.127 6.052 6.052 0 0 0-4.787-2.127A6.985 6.985 0 0 0 .5 9.122c0 3.61 2.55 5.827 5.015 7.97.283.246.569.494.853.747l1.027.918a44.998 44.998 0 0 0 3.518 3.018 2 2 0 0 0 2.174 0 45.263 45.263 0 0 0 3.626-3.115l.922-.824c.293-.26.59-.519.885-.774 2.334-2.025 4.98-4.32 4.98-7.94a6.985 6.985 0 0 0-6.708-7.218Z">
                                        </path>
                                    </svg></span>
                                <span><svg aria-label="Comment" class="x1lliihq x1n2onr6 x5n08af" fill="currentColor"
                                        height="24" role="img" viewBox="0 0 24 24" width="24">
                                        <title>Comment</title>
                                        <path d="M20.656 17.008a9.993 9.993 0 1 0-3.59 3.615L22 22Z" fill="none"
                                            stroke="currentColor" stroke-linejoin="round" stroke-width="2"></path>
                                    </svg></span>
                                <span><svg aria-label="Share Post" class="x1lliihq x1n2onr6 x5n08af" fill="currentColor"
                                        height="24" role="img" viewBox="0 0 24 24" width="24">
                                        <title>Share Post</title>
                                        <line fill="none" stroke="currentColor" stroke-linejoin="round"
                                            stroke-width="2" x1="22" x2="9.218" y1="3"
                                            y2="10.083"></line>
                                        <polygon fill="none"
                                            points="11.698 20.334 22 3.001 2 3.001 9.218 10.084 11.698 20.334"
                                            stroke="currentColor" stroke-linejoin="round" stroke-width="2"></polygon>
                                    </svg></span>
                            </div>
                            <div class="likes">789,187 likes</div>
                            <div class="caption post-description">
                                <span class="username">karuneshtalwar</span> This is a default post description
                            </div>
                            <div class="comments text-muted">View all 6,273 comments</div>
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
