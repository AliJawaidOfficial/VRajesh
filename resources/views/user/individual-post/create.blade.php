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

        .remove-media-btn {
            background: rgba(0, 0, 0);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }

        .form-check-input {
            width: 17px;
            height: 17px;
            margin-right: 5px;
            cursor: pointer;

        }

        .btn-custom {
            width: 140px;
            font-weight: bold;
            color: #fff;
            background-image: linear-gradient(90deg, #ff6600 0%, #d89e33 100%) !important;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-image: linear-gradient(90deg, #d67f45 0%, #d89e33 100%) !important;
            color: #fff;
        }

        .linkedin-post-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            border-top: 1px solid #ddd;
        }

        .linkedin-post-footer>div img {
            width: 25px;
        }

        /* Platform Checkbox Styles */
        .platform-checkbox {
            margin-right: 10px;
            /* Adds space between checkboxes */
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .platform-checkbox input {
            margin-right: 5px;
            /* Adds space between the checkbox and the icon */
        }

        .platform-checkbox span {
            margin-left: 5px;
            /* Adds space between the icon and the text */
        }

        /* Align the icon properly */
        .platform-checkbox i {
            vertical-align: middle;
        }


        #loadingModal {
            display: none;
            /* Initially hidden */
            position: fixed;
            z-index: 1000;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Semi-transparent background */
        }

        #loadingSpinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }
    </style>
@endsection

{{-- Scripts --}}
@section('scripts')
    <script>
        // Loading Modal (Custom)
        function showLoadingModal() {
            document.getElementById("loadingModal").style.display = "block";
        }

        function hideLoadingModal() {
            document.getElementById("loadingModal").style.display = "none";
        }

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

        if (document.getElementById('mediaInput')) {
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

                        // Show remove button
                        document.querySelectorAll('.remove-media-btn').forEach(btn => {
                            btn.style.display = 'flex';
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        document.querySelectorAll('.remove-media-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.post-media img, .post-media video').forEach(media => {
                    media.src = '';
                    media.classList.remove('active');
                    media.style.display = 'none';
                });
                document.querySelectorAll('.remove-media-btn').forEach(btn => {
                    btn.style.display = 'none';
                });
                document.getElementById('mediaInput').value = '';
            });
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
        // $("#scheduleForm").submit(function(e) {
        //     e.preventDefault();
        //     if ($(this).valid()) {
        //         $("#post_schedule_date").val($("#scheduleDate").val());
        //         $("#post_schedule_time").val($("#scheduleTime").val());
        //         $("#scheduleModal").modal("hide");
        //         $("#scheduleModal").css("display", "none");
        //         $("#postForm").submit();
        //         $("#exampleModal").modal("hide");
        //         $("#exampleModal").css("display", "none");
        //     }
        // });

        // Post Form
        $("#postForm").submit(function(e) {
            e.preventDefault();
            if ($(this).valid()) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.individual.post.store') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        showLoadingModal()
                    },
                    success: function(response) {

                        $("#post_schedule_date").val("");
                        $("#post_schedule_time").val("");
                        hideLoadingModal()

                        $("#scheduleModal").removeClass("show");
                        $("#scheduleModal").css("display", "none");

                        if (response.status == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 700
                            }).then(() => {
                                // location.reload();
                            });
                        } else {
                            toastr.error(response.error);
                        }
                    }
                });
            }
        });

        // Draft Form
        // $('#postForm button[name="draft"]').click(function(e) {
        //     e.preventDefault();
        //     let form = $('#postForm')[0];
        //     if ($(this).valid()) {
        //         $.ajax({
        //             type: "POST",
        //             url: "{{ route('user.post.draft.store') }}",
        //             data: new FormData(form),
        //             processData: false,
        //             contentType: false,
        //             beforeSend: function() {
        //                 showLoadingModal()
        //             },
        //             success: function(response) {
        //                 if (response.status == 200) {
        //                     hideLoadingModal()
        //                     Swal.fire({
        //                         icon: 'success',
        //                         title: "Post saved as draft",
        //                         showConfirmButton: false,
        //                         timer: 1500
        //                     }).then(() => {
        //                         location.reload();
        //                     });
        //                 } else {
        //                     toastr.error(response.error);

        //                     hideLoadingModal()
        //                 }
        //             }
        //         });
        //     }
        // });
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
                                placeholder="Enter your title here" />
                        </div>
                        <div class="textarea-wrapper my-1">
                            <textarea class="input-tag-description d-block h-100 w-100 form-control" id="postDescription" name="description"
                                placeholder="Enter your post description"></textarea>
                        </div>

                        <div class="w-100 my-1 d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <p class="mb-0">Check to share on:</p>

                                @if (Auth::guard('web')->user()->canAny(['linkedin_text_post', 'linkedin_image_post', 'linkedin_video_post']))
                                    @if (Auth::guard('web')->user()->linkedin_access_token != null)
                                        <label class="d-inline-block platform-checkbox">
                                            <input type="checkbox" name="on_linkedin"
                                                onchange="getLinkedInOrganizations(this)" value="1"
                                                data-bs-toggle="linkedin-post" class="form-check-input toggle-post" />
                                            -
                                            <span class="d-inline-block ms-1"><i class="fab fa-linkedin-in"
                                                    style="font-size: 17px"></i></span>
                                        </label>
                                    @endif
                                @endif
                            </div>

                            @if (Auth::guard('web')->user()->canAny([
                                        'linkedin_image_post',
                                        'linkedin_video_post',
                                    ]))
                                <div class="d-flex align-items-center">
                                    <label for="mediaInput" class="btn btn-transparent text-primary"><i
                                            class="fas fa-paperclip" style="font-size: 20px"></i></label>
                                    <input class="d-block w-100 form-control d-none" type="file" name="media"
                                        accept="video/*, image/*" id="mediaInput" />
                                    <button type="button" class="remove-media-btn" style="display: none;">&times;</button>
                                </div>
                            @endif
                        </div>

                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-1 gap-4">

                        <div>
                            {{-- @can('draft_post')
                                <button type="button" name="draft" class="btn btn-custom">Save as Draft</button>
                            @endcan --}}
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            {{-- @can('scheduled_post')
                                <button type="button" class="btn btn-custom" data-bs-toggle="modal"
                                    data-bs-target="#scheduleModal">Schedule</button>
                            @endcan --}}
                            @can('immediate_post')
                                <button type="submit" name="post" class="btn btn-custom">Post</button>
                            @endcan
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-4">
                <div class="w-100 bg-white p-4 rounded-6 overflow-scroll d-flex flex-column gap-1" id="postPreview">
                    {{-- LinkedIn Post Preview --}}
                    <div class="card rounded" style="display: none" id="linkedin-post">
                        <div
                            class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-2">
                            <div class="d-flex align-items-center">
                                <img src="{{ Auth::guard('web')->user()->linkedin_avatar }}" alt="Profile Picture"
                                    class="rounded-circle" style="max-width: 50px">
                                <div class="ms-2">
                                    <h6 class="mb-0">{{ Auth::guard('web')->user()->linkedin_name }}</h6>
                                    {{-- <small class="text-muted line-clap" style="-webkit-line-clamp: 1;">HR Professional | Head Hunter | Technical & Non Technical Recruiter | International...</small> --}}
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
                                    <source src="" type="video/mp4">
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
                        <div class="linkedin-post-footer">

                            <div class="">
                                <img
                                    src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAAA10lEQVR4nO2UrwrCUBSHP1DEbrL5CiaTD6DFImgSDAafQlbE6Eus+ARWq2VdTDoQuwZRvDI44eK/3bkdWPDAYXfb7/d9S4OcTh/wgYIGvAdcAQOMNOEGmGvBT3IdZwXvWvAJsJFzPWv4DKgAd+AMFNPCOxbck2dtuV+l/3bYP8GRs4nZqNeyQXEFe5YO+Wh3vwpc5qX3CWTyKGhIJ9QS+NKZagiqwAW4ATUNgSf5hSvIJBCUgIPkmxqCgWSDdy+zEKwlO/wmSLtHoJxEsJV1gYfyI/wPieYBtbOZBWuxK9EAAAAASUVORK5CYII=">
                                like
                            </div>

                            <div class="">
                                <img
                                    src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAADCklEQVR4nO2Zy29NURSHP7T1KAlK4jXy+gMoOmBAQlASERETjwEDaaJmjYaYlMYIKTERvZp0RBOphBkRA2aUjqoGQq4WbSQqVZPKTtaVlZVze88+9j1Hk/sle3Jz1mOfs/dav70vVKhQIQQzgQagFegB+oFR4LeMUfntPnAO2CI2mbMKaAc+AZOe4yNwGViZReJ1wC1gIkHidvwCbgKL00r+CPAtIpE8cBs4CmyQSVbLWAJsBI7JM/kI+6/A4XImXiVv3QZ+AuwFZnn4cs82Ak8j/N2QWEGZCzw0gQaAnQF87wbeGd+9EjMIVRHJdwLzQgUAaoGuiEkE+RJ22ZynfFwwsTpCbNi0ki82iUMkpM5Umxzp0aXifgEW/evSGZB1mha1ZmN3JOmwukntIn32mGa3wse4XRm7Wp0Vz1Qel3yajNY2ruFkxT6Vx4e4ArDByIPgXdGDKmBI5bM5jlGrMnC6pRhOSY4HEHPjJZZHp3q2Jc4EepSBE2ZRzAB+BEh+UobzVYzj6rl7cSbQrwycqixGW8Av0DZFnHr17Js4ExhRBq6ZZc1SI7lLout/Ddkz2/SDkuhlMYdpOIHPymAZ03AJ9SkDdwwMXUZLlU3LJt9NrJXgyTKV0anKpuWEbxltVgZ3ylBGS5VNS863ka1XBiNyq5CllBhW+bieEIu3yugA2bE/iZhznFWGL8mO5yoPn2XHAuC7MnZ3mWnTaPaN14HGMagcbCNd5gPvVfzrvg7WmpKXtqToVvGHkxzqm8wFU5pcNGX3YBInvcqBm0xWyV9L4qTGdNk1pLPmu03yDzwvjP+yXTlxdzNRuOa2NVB1ajQbtpB84svdK+aqu8Bq4LQ411/ohTS7as8O65qUrvO64iR68wVeKWdX5VZsIIbGGZED+ClRscvlPFEjf3DUizDLGXkwKWMo6Ya1/IwpygZNs0s6xuWtLyQQj4sEGpPq1CR9otCxm412ijuctmmTLxUU5/CRJPxa9sSOGM1sHXAGuCt2eXm7E7K8+kTPt8gh5b/4m7VCBaY5fwDlENrwg3MbPQAAAABJRU5ErkJggg==">
                                Comment
                            </div>

                            <div class="">
                                <img
                                    src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAqElEQVR4nO2UQQqDMBBF38obKO1desBcqi1UChVd1UVP4DksgQQ0NZMMzTIPBgTHPCckHyoFeAJrRi2AAZrYQh0wubLPnj5T4MvEFp83TXMgyeGymWRHC7wP/uQDnNHhv90xCOOOJQRXQXArIVA1JKiCJHWLDrkLx9xegb+P6SQIXiUErRA1p7B5cS9tcGnocsPSKKO5DySjq2gSN07iJ1kT9VBOWuGHLwJ0i8pnPs77AAAAAElFTkSuQmCC">
                                Repost
                            </div>

                            <div class="">
                                <img
                                    src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAABMUlEQVR4nO3UzypFURTH8Y90BwZKKQM8gDKRl2BoYsyMmRE3KblznoIXkIxkZGJigiFFYSYKUVxHp/bN6Xb+3XvPRPnWGuy1d7/fOWvttfnnlyns4AaHKmIMqzhHlIjbXkQHMI8DfLYJt2K7U9F+zGAXrymC3/hIrONylWISW6GuUUa8Yz+YxOvLItFRrOAsR7QV91jCWyIX9ySVWRyhWUI4wimmcZfINUPjU3kqKRyFXgwFk2Q+/sBMNksIN1FHXzBp318oqn8jR/wFc+FcPWU/7sNgkUGWyXW4Ua1efaWc2SsjnmZygpGQn8Bzxh/Gc9IRi1hHLayHcZUh/hCGsWtqOM7pT/zI9cRGwe0q/TRksZwjfqEiGhkGa1UZpJnEgzeuYhpln4Ze2MRjN3f/7/EDz12xqRgiakMAAAAASUVORK5CYII=">
                                Send
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Loading Modal -->
    <section class="main-content-wrapper d-flex flex-column">
        <div id="loadingModal">
            <div id="loadingSpinner"></div>
        </div>
    </section>

    <!-- Schedule Modal -->
    {{-- @can('scheduled_post')
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
                                <input type="date" min="{{ date('Y-m-d') }}" class="form-control" id="scheduleDate"
                                    name="schedule_date" required>
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
    @endcan --}}
@endsection