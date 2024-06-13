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
            width: 145px;
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


        #imagesModal .input-group {
            position: sticky;
            top: 0;
            z-index: 10;
            background: white;
        }

        #images * {
            box-sizing: border-box;
            -webkit-user-select: none;
            /* Chrome, Safari, Edge */
            -moz-user-select: none;
            /* Firefox */
            -ms-user-select: none;
            /* Internet Explorer/Edge */
            user-select: none;
            /* Non-prefixed version, supported by most browsers */
        }

        .selectable-image {
            position: relative;
            cursor: pointer;
        }

        .selected-container {
            outline: 2px solid #007bff;
            position: relative;
            z-index: 10;
        }

        .selected-container::after {
            content: "✓";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2em;
            color: white;
            background: rgba(0, 0, 0, 0.8);
            padding: 0.8em 1.2em;
            border-radius: 50%;
        }

        .btn-pxel {
            width: 30px;
            height: 30px;
            padding: 0px
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
        $("#scheduleForm").submit(function(e) {
            e.preventDefault();
            if ($(this).valid()) {
                $("#post_schedule_date").val($("#scheduleDate").val());
                $("#post_schedule_time").val($("#scheduleTime").val());
                $("#scheduleModal").modal("hide");
                $("#scheduleModal").css("display", "none");
                $("#postForm").submit();
                $("#exampleModal").modal("hide");
                $("#exampleModal").css("display", "none");
            }
        });

        // Post Form
        $("#postForm").submit(function(e) {
            e.preventDefault();
            if ($(this).valid()) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.post.store') }}",
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
                                location.reload();
                            });
                        } else {
                            toastr.error(response.error);
                        }
                    }
                });
            }
        });

        // Draft Form
        $('#postForm button[name="draft"]').click(function(e) {
            e.preventDefault();
            let form = $('#postForm')[0];
            if ($(this).valid()) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.post.draft.store') }}",
                    data: new FormData(form),
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        showLoadingModal()
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            hideLoadingModal()
                            Swal.fire({
                                icon: 'success',
                                title: "Post saved as draft",
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            toastr.error(response.error);

                            hideLoadingModal()
                        }
                    }
                });
            }
        });

        // Facbook Pages
        function getFacebookPages(element) {
            if (element.checked) {
                $("#facebookSelectSection").fadeIn();
                $.ajax({
                    type: "GET",
                    url: "{{ route('user.facebook.pages') }}",
                    beforeSend: function() {
                        $("#facebookPagesSelect").html(`<option value="">Loading...</option>`);
                    },
                    success: function(response) {
                        html = `<option value="">Select</option>`;

                        if (response.length > 0) {
                            response.forEach((page) => {
                                html +=
                                    `<option value="${page.id} - ${page.access_token} - ${page.name}">${page.name}</option>`
                            })
                        } else {
                            html = `<option value="">No Page Found</option>`;
                        }
                        $("#facebookPagesSelect").html(html);
                    }
                });
            } else {
                $("#facebookSelectSection").fadeOut();
            }
        }

        // Instagram Accounts
        function getInstagramAccounts(element) {
            if (element.checked) {
                $("#instagramSelectSection").fadeIn();
                $.ajax({
                    type: "GET",
                    url: "{{ route('user.instagram.accounts') }}",
                    beforeSend: function() {
                        $("#instagramAccountSelect").html(`<option value="">Loading...</option>`);
                    },
                    success: function(response) {
                        html = `<option value="">Select</option>`;

                        if (response.length > 0) {
                            response.forEach((account) => {
                                html +=
                                    `<option value="${account.ig_business_account} - ${account.name}">${account.name}</option>`
                            })
                        } else {
                            html = `<option value="">No Account Found</option>`;
                        }
                        $("#instagramAccountSelect").html(html);
                    }
                });
            } else {
                $("#instagramSelectSection").fadeOut();
            }
        }

        // LinkedIn Organizations
        function getLinkedInOrganizations(element) {
            if (element.checked) {
                $("#linkedinSelectSection").fadeIn();
                $.ajax({
                    type: "GET",
                    url: "{{ route('user.linkedin.organizations') }}",
                    beforeSend: function() {
                        $("#linkedinOrganizationsSelect").html(`<option value="">Loading...</option>`);
                    },
                    success: function(response) {
                        html = `<option value="">Select</option>`;

                        if (response.length > 0) {
                            response.forEach((account) => {
                                html +=
                                    `<option value="${account.id} - ${account.name}">${account.name} (${account.vanity_name})</option>`
                            })
                        } else {
                            html = `<option value="">No Account Found</option>`;
                        }
                        $("#linkedinOrganizationsSelect").html(html);
                    }
                });
            } else {
                $("#linkedinSelectSection").fadeOut();
            }
        }

        // Pixel API Functionality
        var currentPage = 1;
        var perPage = 12;
        var isLoading = false;
        var imagesLoaded = 0;
        var totalImages = 0;
        var selectedImage = null;

        // Function to load more images
        function loadMoreImages() {
            if (!isLoading) {
                currentPage++;
                getPixels('photos', $("#imagesModalSearchInput").val(), currentPage, perPage);
            }
        }

        // Function to fetch images from the server
        function getPixels(type, q, page = 1, per_page = 12) {
            console.log("Fetching images. Page:", page);
            if (q.length == 0) {
                q = 'green';
            }

            $.ajax({
                type: "GET",
                url: `{{ env('APP_URL') }}/pixels/photos/${q}?page=${page}&per_page=${per_page}&q=${q}`,
                beforeSend: function() {
                    isLoading = true;
                    $("#loading-indicator").show();
                    $("#load-more-btn").hide();
                },
                success: function(response) {
                    console.log("Success response:", response);
                    if (response.status == 200) {
                        var html = '';
                        totalImages += response.data.photos.length;
                        $.each(response.data.photos, function(indexInArray, photo) {
                            html += `<div class="col-lg-4 p-0">
                        <img src="${photo.src.landscape}" class="img-fluid selectable-image" data-image-id="${photo.id}" data-src="${photo.src.landscape}" />
                     </div>`;
                        });
                        $("#images").append(html);

                        if (response.data.total_pages > page) {
                            console.log("More pages available. Showing load more button.");
                            // $("#loading-indicator").hide();
                        } else {
                            console.log("All images loaded.");
                        }
                    } else {
                        toastr.error(response.error);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error("An error occurred while fetching images.");
                },
                complete: function() {
                    isLoading = false;
                    $("#loading-indicator").hide();
                    $("#load-more-btn").show();
                }
            });
        }

        // Initial load of images when modal is shown
        $("#imagesModal").on('show.bs.modal', function() {
            currentPage = 1;
            totalImages = 0;
            imagesLoaded = 0;
            $("#images").html("");
            getPixels('photos', $("#imagesModalSearchInput").val(), currentPage, perPage);
        });

        // Attach click event handler to the "Load More" button
        $("#load-more-btn").click(loadMoreImages);

        // Event delegation for image selection
        $(document).on('click', '.selectable-image', function() {
            var imageId = $(this).data('image-id');
            var imageUrl = $(this).data('src');

            // Remove selection from previously selected image
            if (selectedImage) {
                selectedImage.removeClass('selected');
                selectedImage.closest('div').removeClass('selected-container');
            }

            // Toggle selection on the clicked image
            if (!selectedImage || selectedImage.data('image-id') !== imageId) {
                selectedImage = $(this);
                selectedImage.addClass('selected');
                selectedImage.closest('div').addClass('selected-container');
                $("#imagesModalDoneBtn").prop("disabled", false); // Enable Done button when an image is selected
            } else {
                selectedImage = null;
                $("#imagesModalDoneBtn").prop("disabled", true); // Disable Done button when no image is selected
            }
        });

        // Function to handle "Done" button click
        $(document).on('click', '#imagesModalDoneBtn', function() {
            if (selectedImage) {
                var imageUrl = selectedImage.data('src');

                // Strip query parameters from the image URL
                var cleanImageUrl = imageUrl.split('?')[0];

                const doneBtn = document.getElementById("imagesModalDoneBtn")

                doneBtn.innerHTML = '<i class="fa fa-spinner fa-pulse"></i>';

                doneBtn.disabled = true;

                // Fetch image as blob using the cleaned URL
                fetch(cleanImageUrl)
                    .then(response => response.blob())
                    .then(blob => {
                        var fileName = cleanImageUrl.split('/').pop();
                        var file = new File([blob], fileName, {
                            type: blob.type
                        });

                        // Create a DataTransfer object and add the file
                        var dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);

                        console.log(file);

                        // Get the file input element and set its files property
                        var mediaInput = document.getElementById('mediaInput');
                        if (mediaInput) {
                            mediaInput.files = dataTransfer.files;
                        }

                        // Trigger the file input change event
                        mediaInput.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));

                        // Close the modal
                        $('#imagesModal').modal('hide');

                        doneBtn.innerHTML = 'Done';
                    })
                    .catch(error => {
                        console.error('Error fetching image:', error);
                    });
            }
        });

        // Attach click event handler to the search button
        $("#searchButton").click(function() {
            currentPage = 1;
            totalImages = 0;
            imagesLoaded = 0;
            $("#images").html("");
            getPixels('photos', $("#imagesModalSearchInput").val(), currentPage, perPage);
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
                                placeholder="Enter your title here" />
                        </div>
                        <div class="textarea-wrapper my-1">
                            <textarea class="input-tag-description d-block h-100 w-100 form-control" id="postDescription" name="description"
                                placeholder="Enter your post description"></textarea>
                        </div>

                        <div class="w-100 my-1 d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <p class="mb-0">Check to share on:</p>

                                @if (Auth::guard('web')->user()->canAny(['meta_facebook_text_post', 'meta_facebook_image_post', 'meta_facebook_video_post']))
                                    @if (Auth::guard('web')->user()->meta_access_token != null)
                                        <label class="d-inline-block platform-checkbox">
                                            <input type="checkbox" name="on_facebook" onchange="getFacebookPages(this)"
                                                value="1" data-bs-toggle="facebook-post"
                                                class="form-check-input toggle-post" />
                                            -
                                            <span class="d-inline-block ms-1"><i class="fab fa-facebook-f"
                                                    style="font-size: 17px"></i></span>
                                        </label>
                                    @endif
                                @endif

                                @if (Auth::guard('web')->user()->canAny(['meta_instagram_image_post', 'meta_instagram_video_post']))
                                    @if (Auth::guard('web')->user()->meta_access_token != null)
                                        <label class="d-inline-block platform-checkbox">
                                            <input type="checkbox" name="on_instagram" onchange="getInstagramAccounts(this)"
                                                value="1" data-bs-toggle="instagram-post"
                                                class="form-check-input toggle-post" />
                                            -
                                            <span class="d-inline-block ms-1"><i class="fab fa-instagram"
                                                    style="font-size: 17px"></i></span>
                                        </label>
                                    @endif
                                @endif

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
                                        'meta_facebook_image_post',
                                        'meta_facebook_video_post',
                                        'meta_instagram_image_post',
                                        'meta_instagram_video_post',
                                        'linkedin_image_post',
                                        'linkedin_video_post',
                                    ]))
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-transparent btn-pxel" data-bs-toggle="modal"
                                        data-bs-target="#imagesModal">
                                        <img src="{{ asset('assets/images/pixel-logo.png') }}" class="w-100" alt=""/>
                                    </button>
                                    <label for="mediaInput" class="btn btn-transparent text-dark"><i
                                            class="fas fa-paperclip" style="font-size: 20px"></i></label>
                                    <input class="d-block w-100 form-control d-none" type="file" name="media[]"
                                        accept="video/*, image/*" id="mediaInput" />
                                    <button type="button" class="remove-media-btn" style="display: none;">&times;</button>

                                </div>
                            @endif
                        </div>

                        <div class="row">
                            {{-- Facebook Pages --}}
                            @if (Auth::guard('web')->user()->canAny(['meta_facebook_text_post', 'meta_facebook_image_post', 'meta_facebook_video_post']))
                                @if (Auth::guard('web')->user()->meta_access_token != null)
                                    <div class="col-md-4">
                                        <div class="mb-2" style="display: none;" id="facebookSelectSection">
                                            <div class="d-flex flex-column gap-2">
                                                <p class="mb-0">Facebook Pages:</p>

                                                <div class="d-flex flex-column gap-1 w-100">
                                                    <select name="facebook_page" id="facebookPagesSelect"
                                                        class="form-select w-100">
                                                        <option value="">Select</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif

                            {{-- Instagram Account --}}
                            @if (Auth::guard('web')->user()->canAny(['meta_instagram_image_post', 'meta_instagram_video_post']))
                                @if (Auth::guard('web')->user()->meta_access_token != null)
                                    <div class="col-md-4">
                                        <div class="mb-2" style="display: none;" id="instagramSelectSection">
                                            <div class="d-flex flex-column gap-2">
                                                <p class="mb-0">Instagram Account:</p>

                                                <div class="d-flex flex-column gap-1 w-100">
                                                    <select name="instagram_account" id="instagramAccountSelect"
                                                        class="form-select w-100">
                                                        <option value="">Select</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif

                            {{-- Instagram Account --}}
                            @if (Auth::guard('web')->user()->canAny(['linkedin_text_post', 'linkedin_image_post', 'linkedin_video_post']))
                                @if (Auth::guard('web')->user()->linkedin_access_token != null)
                                    <div class="col-md-4">
                                        <div class="mb-2" style="display: none;" id="linkedinSelectSection">
                                            <div class="d-flex flex-column gap-2">
                                                <p class="mb-0">LinkedIn Organizations:</p>

                                                <div class="d-flex flex-column gap-1 w-100">
                                                    <select name="linkedin_organization" id="linkedinOrganizationsSelect"
                                                        class="form-select w-100">
                                                        <option value="">Select</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-1 gap-4">

                        <div>
                            @can('draft_post')
                                <button type="button" name="draft" class="btn btn-custom"><i
                                        class="fas fa-folder d-inline-block me-1"></i> Save as Draft</button>
                            @endcan
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            @can('scheduled_post')
                                <button type="button" class="btn btn-custom" data-bs-toggle="modal"
                                    data-bs-target="#scheduleModal"><i class="fas fa-calendar-alt d-inline-block me-1"></i>
                                    Schedule</button>
                            @endcan
                            @can('immediate_post')
                                <button type="submit" name="post" class="btn btn-custom"><i
                                        class="fas fa-share-square d-inline-block me-1"></i> Post</button>
                            @endcan
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-4">
                <div class="w-100 bg-white p-4 rounded-6 overflow-scroll d-flex flex-column gap-1" id="postPreview">
                    {{-- Facebook Post Preview --}}
                    <div class="card rounded" style="display: none" id="facebook-post">
                        <div
                            class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-2">
                            <div class="d-flex align-items-center">
                                <img src="{{ Auth::guard('web')->user()->meta_avatar }}" alt="Profile Picture"
                                    class="rounded-circle">
                                <div class="ms-2">
                                    <h6 class="mb-0 line-clap" style="-webkit-line-clamp: 1;">
                                        {{ Auth::guard('web')->user()->meta_name }}</h6>
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

                    {{-- Instagram Post Preview --}}
                    <div class="card rounded" style="display: none" id="instagram-post">
                        <div
                            class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-2">
                            <div class="d-flex align-items-center">
                                <img src="{{ Auth::guard('web')->user()->meta_avatar }}" alt="Profile Picture"
                                    class="rounded-circle">
                                <div class="ms-2">
                                    <h6 class="mb-0">{{ Auth::guard('web')->user()->meta_name }}</h6>
                                    <small class="text-muted">Original audio</small>
                                </div>
                            </div>
                            <div style="width: fit-content">
                                <img src="{{ asset('assets/images/icons/three-dot-icons.png') }}" style="min-width: 20px"
                                    width="20px" alt="">
                            </div>
                        </div>
                        <div class="card-body p-0 position-relative">
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
                                        height="24" role="img" vie wBox="0 0 24 24" width="24">
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

    <!-- Pixels Images Modal -->
    <div class="modal fade" id="imagesModal" tabindex="-1" aria-labelledby="imagesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-column">
                    <div class="d-flex align-items-center w-100 justify-content-between">
                        <h5 class="modal-title" id="imagesModalLabel">Images</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="input-group mt-2">
                        <input type="text" class="form-control border-dark" id="imagesModalSearchInput"
                            placeholder="Search...">
                        <button class="btn btn-dark px-5" id="searchButton"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                <div class="modal-body">

                    <div class="row m-1" id="images"></div>
                    <div class="text-center mt-3">
                        <button id="load-more-btn" class="btn btn-dark rounded-circle p-3"><i
                                class="fas fa-undo"></i></button>
                    </div>
                    <div id="loading-indicator" class="text-center mt-3" style="display: none;">
                        <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="imagesModalDoneBtn" disabled>Done</button>
                </div>
            </div>
        </div>
    </div>


    {{-- ... (Rest of your Blade content) ... --}}
    <div id="loadingModal">
        <div id="loadingSpinner"></div>
    </div>

    <!-- Schedule Modal -->
    @can('scheduled_post')
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
                            <button type="submit" class="btn btn-primary"><i
                                    class="fas fa-calendar-alt d-inline-block me-1"></i> Schedule</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection
