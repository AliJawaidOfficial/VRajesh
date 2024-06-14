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

        .preview-wrapper {
            position: relative;
            top: 0%;
            width: fit-content;
            height: fit-content;
            margin: 0 auto;
        }

        #uploadedImages {
            flex-grow: 1;
            overflow-y: auto;
            align-items: flex-start;
            align-content: flex-start;
            gap: 20px;
        }

        .uploaded-images-container {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 200px);
        }

        .uploaded-images-container img,
        .uploaded-images-container video {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            border: 2px solid #ddd;
            padding: 5px;
            position: relative;
        }

        .uploaded-images-container .remove-btn {
            position: absolute;
            top: 0;
            right: 0;
            background: rgba(255, 0, 0, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .uploaded-images-container .remove-btn:hover {
            background: red;
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

        function updateDescription(description) {
            const formattedDescription = description.replace(/\n/g, '<br>');
        }

        document.getElementById("postDescription").addEventListener("keyup", function(e) {
            const description = e.target.value.trim() ? e.target.value : "This is a default post description";
            updateDescription(description);
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
                                    `<option value="${page.id} - ${page.access_token} - ${page.name}"
                                    ${page.id == @json($post->facebook_page_id) ? 'selected' : ''}
                                    >
                                        ${page.name}
                                    </option>`
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
                                html += `
                                    <option value="${account.ig_business_account} - ${account.name}"
                                    ${account.ig_business_account == @json($post->instagram_account_id) ? 'selected' : ''}>
                                    ${account.name}
                                    </option>
                                `
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
                                html += `
                                    <option value="${account.id} - ${account.name}"
                                    ${account.id == @json($post->linkedin_organization_id) ? 'selected' : ''}
                                    >
                                    ${account.name} (${account.vanity_name})
                                    </option>
                                `
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

        $(document).ready(function() {
            @if ($post->on_facebook == 1)
                $("#onFacebookCheckbox").click();
            @endif

            @if ($post->on_instagram == 1)
                $("#onInstagramCheckbox").click();
            @endif

            @if ($post->linked_in == 1)
                $("#onLinkedinCheckbox").click();
            @endif
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
            console.log("submit");
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
                                // location.reload();
                            });
                        } else {
                            toastr.error(response.error);
                        }
                    }
                });
            }
        });

        // Save as Draft Form
        $('#postForm button[name="save_as_draft"]').click(function(e) {
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

        // Draft Form
        $('#postForm button[name="draft"]').click(function(e) {
            e.preventDefault();
            let form = $('#postForm')[0];
            if ($(this).valid()) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.post.draft.update') }}",
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
                                title: "Changes saved",
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                // location.reload();
                            });
                        } else {
                            toastr.error(response.error);

                            hideLoadingModal()
                        }
                    }
                });
            }
        });


        // Pixel API Functionality
        var currentPage = 1;
        var perPage = 12;
        var isLoading = false;
        var imagesLoaded = 0;
        var totalImages = 0;
        var selectedImages = [];

        // Function to load more images
        function loadMoreImages() {
            if (!isLoading) {
                currentPage++;
                getPixels('photos', $("#imagesModalSearchInput").val(), currentPage, perPage);
            }
        }

        // Function to fetch images from the server
        function getPixels(type, q, page = 1, per_page = 12) {
            console.log("selectedImages", selectedImages);
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

            // Toggle selection on the clicked image
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
                $(this).closest('div').removeClass('selected-container');
                selectedImages = selectedImages.filter(img => img.id !== imageId);
            } else {
                $(this).addClass('selected');
                $(this).closest('div').addClass('selected-container');
                selectedImages.push({
                    id: imageId,
                    src: imageUrl
                });
            }

            $("#imagesModalDoneBtn").prop("disabled", selectedImages.length ===
                0); // Enable Done button when images are selected
        });

        // Function to handle "Done" button click
        $(document).on('click', '#imagesModalDoneBtn', function() {
            // Handle Done button click
            $("#imagesModalDoneBtn").prop("disabled", true); // Disable Done button

            if (selectedImages.length > 0) {
                const mediaInput = document.getElementById('mediaInput');
                const dataTransfer = new DataTransfer();

                const fetchPromises = selectedImages.map(image => {
                    const cleanImageUrl = image.src.split('?')[0];
                    return fetch(cleanImageUrl)
                        .then(response => response.blob())
                        .then(blob => {
                            const fileName = cleanImageUrl.split('/').pop();
                            const file = new File([blob], fileName, {
                                type: blob.type
                            });
                            dataTransfer.items.add(file);
                        })
                        .catch(error => {
                            console.error('Error fetching image:', error);
                        });
                });

                // Wait for all fetch operations to complete
                Promise.all(fetchPromises).then(() => {
                    for (let i = 0; i < mediaInput.files.length; i++) {
                        dataTransfer.items.add(mediaInput.files[i]);
                    }
                    mediaInput.files = dataTransfer.files;
                    handleFileUploads(mediaInput);
                    $('#imagesModal .btn-close').click(); // Close the modal
                });

                selectedImages.length = 0;
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

        // Handle file uploads and previews
        let uploadedFiles = [];

        function handleFileUploads(input) {
            const fileInput = input;
            const files = Array.from(fileInput.files);

            const previewContainer = document.getElementById('uploadedImages');
            const imageTypes = ["jpeg", "jpg", "png", "gif"];
            const videoTypes = ["mp4", "avi", "mov", "mpeg"];
            let fileType = null;
            let isVideoFile = false;
            let isValid = true;
            let errorMessage = "";

            // Check file types and sizes
            let videoFileCount = 0;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileMimeType = file.type.split('/')[1];
                const fileMimeTypeCategory = file.type.split('/')[0];
                const fileSizeMB = file.size / (1024 * 1024); // size in MB

                if (fileMimeTypeCategory === 'image') {
                    if (!imageTypes.includes(fileMimeType) || fileSizeMB > 5) {
                        isValid = false;
                        errorMessage =
                            "Invalid image file. Accepted formats: jpeg, jpg, png, gif. Size must be less than 5 MB.";
                        break;
                    }
                } else if (fileMimeTypeCategory === 'video') {
                    if (!videoTypes.includes(fileMimeType) || fileSizeMB > 500) {
                        isValid = false;
                        errorMessage =
                            "Invalid video file. Accepted formats: mp4, avi, mov, mpeg. Size must be less than 5 MB.";
                        break;
                    }
                } else {
                    isValid = false;
                    errorMessage = "Unsupported file type.";
                    break;
                }

                if (fileType === null) {
                    fileType = fileMimeTypeCategory;
                    if (fileMimeTypeCategory === 'video') {
                        videoFileCount++;
                        isVideoFile = true;
                    }
                } else if (fileType !== fileMimeTypeCategory) {
                    isValid = false;
                    errorMessage = "You can only upload either images or a video at a time, not both.";
                    break;
                } else if (fileMimeTypeCategory === 'video') {
                    videoFileCount++;
                }
            }

            if (videoFileCount > 1) {
                isValid = false;
                errorMessage = "You can only upload one video at a time.";
            }

            if (!isValid) {
                alert(errorMessage);
                fileInput.value = ''; // Reset the file input
                return;
            }

            // If a video file is selected, remove all existing images
            if (isVideoFile) {
                removeExistingMedia('image');
            } else {
                // If images are selected, remove existing video
                removeExistingMedia('video');
            }

            if (fileType === 'image') {
                $("#mediaType").val('image');
            } else if (fileType === 'video') {
                $("#mediaType").val('video');
            }

            // Preview files
            files.forEach((file, index) => {
                const reader = new FileReader();
                const existingFileIndex = uploadedFiles.findIndex(uploadedFile => uploadedFile.name === file.name &&
                    uploadedFile.size === file.size);

                if (existingFileIndex !== -1) {
                    return;
                }

                reader.onload = function(event) {
                    const mediaElement = fileType === 'image' ? new Image() : document.createElement('video');
                    mediaElement.src = event.target.result;
                    mediaElement.classList.add('preview');
                    mediaElement.dataset.index = uploadedFiles.length;
                    // Remove controls attribute for video
                    if (fileType === 'video') {
                        mediaElement.controls = false;
                    }

                    const removeBtn = document.createElement('button');
                    removeBtn.innerHTML = '×';
                    removeBtn.classList.add('remove-btn');
                    removeBtn.onclick = function() {
                        removeFile(parseInt(mediaElement.dataset.index));
                    };

                    const previewWrapper = document.createElement('div');
                    previewWrapper.classList.add('preview-wrapper');
                    previewWrapper.appendChild(mediaElement);
                    previewWrapper.appendChild(removeBtn);
                    previewContainer.appendChild(previewWrapper);

                    uploadedFiles.push(file);
                };

                reader.readAsDataURL(file);
            });
        }

        function removeExistingMedia(mediaType) {
            uploadedFiles = uploadedFiles.filter(file => file.type.split('/')[0] !== mediaType);
            const previews = document.querySelectorAll('.preview-wrapper');
            previews.forEach(preview => {
                const mediaElement = preview.querySelector('.preview');
                if (mediaElement.tagName.toLowerCase() === (mediaType === 'image' ? 'img' : 'video')) {
                    preview.remove();
                }
            });
        }

        function removeFile(index) {
            const fileType = uploadedFiles[index].type.split('/')[0];
            uploadedFiles.splice(index, 1);

            // Remove from input files
            const mediaInput = document.getElementById('mediaInput');
            const dataTransfer = new DataTransfer();
            for (let i = 0; i < mediaInput.files.length; i++) {
                if (i !== index) {
                    dataTransfer.items.add(mediaInput.files[i]);
                }
            }
            mediaInput.files = dataTransfer.files;

            // Update selectedImages array
            selectedImages = selectedImages.filter((img, i) => i !== index);

            const previews = document.querySelectorAll('.preview-wrapper');
            previews.forEach(preview => {
                const mediaElement = preview.querySelector('.preview');
                const previewIndex = parseInt(mediaElement.dataset.index);
                if (previewIndex === index) {
                    preview.remove();
                } else if (previewIndex > index) {
                    mediaElement.dataset.index = previewIndex - 1;
                }
            });
        }

        document.getElementById('mediaInput').addEventListener('change', function() {
            handleFileUploads(this);
        });

        // Function to handle images from pixel selection
        function handlePixelSelection(selectedImagesFromPixel) {
            // Convert the selected images to File objects and handle them as file uploads
            const dataTransfer = new DataTransfer();
            selectedImagesFromPixel.forEach(image => {
                const cleanImageUrl = image.src.split('?')[0];
                fetch(cleanImageUrl)
                    .then(response => response.blob())
                    .then(blob => {
                        const fileName = cleanImageUrl.split('/').pop();
                        const file = new File([blob], fileName, {
                            type: blob.type
                        });
                        dataTransfer.items.add(file);
                        handleFileUploads({
                            files: dataTransfer.files
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching image:', error);
                    });
            });
        }
    </script>
@endsection

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">
        <div class="row align-items-stretch mt-2">
            <div class="col-md-8">
                <form id="postForm" class="p-4 bg-white rounded-6" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="date" name="schedule_date" id="post_schedule_date" class="d-none" value="" />
                    <input type="time" name="schedule_time" id="post_schedule_time" class="d-none" value="" />

                    <div class="d-flex flex-column flex-grow-1">
                        <div class="mb-3">
                            <input class="input-tag-title d-block h-100 w-100 form-control" name="title"
                                value="{{ $post->title }}" placeholder="Enter your title here" />
                        </div>
                        <div class="textarea-wrapper my-1">
                            <textarea class="input-tag-description d-block h-100 w-100 form-control" id="postDescription" name="description"
                                placeholder="Enter your post description">{{ $post->description }}</textarea>
                        </div>

                        <div class="w-100 my-1 d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <p class="mb-0">Check to share on:</p>

                                @if (Auth::guard('web')->user()->canAny(['meta_facebook_text_post', 'meta_facebook_image_post', 'meta_facebook_video_post']))
                                    @if (Auth::guard('web')->user()->meta_access_token != null)
                                        <label class="d-inline-block platform-checkbox">
                                            <input type="checkbox" name="on_facebook" id="onFacebookCheckbox"
                                                onchange="getFacebookPages(this)" value="1"
                                                data-bs-toggle="facebook-post" class="form-check-input toggle-post" />
                                            -
                                            <span class="d-inline-block ms-1"><i class="fab fa-facebook-f"
                                                    style="font-size: 17px"></i></span>
                                        </label>
                                    @endif
                                @endif

                                @if (Auth::guard('web')->user()->canAny(['meta_instagram_image_post', 'meta_instagram_video_post']))
                                    @if (Auth::guard('web')->user()->meta_access_token != null)
                                        <label class="d-inline-block platform-checkbox">
                                            <input type="checkbox" name="on_instagram" id="onInstagramCheckbox"
                                                onchange="getInstagramAccounts(this)" value="1"
                                                data-bs-toggle="instagram-post" class="form-check-input toggle-post" />
                                            -
                                            <span class="d-inline-block ms-1"><i class="fab fa-instagram"
                                                    style="font-size: 17px"></i></span>
                                        </label>
                                    @endif
                                @endif

                                @if (Auth::guard('web')->user()->canAny(['linkedin_text_post', 'linkedin_image_post', 'linkedin_video_post']))
                                    @if (Auth::guard('web')->user()->linkedin_access_token != null)
                                        <label class="d-inline-block platform-checkbox">
                                            <input type="checkbox" name="on_linkedin" id="onLinkedinCheckbox"
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
                                        <img src="{{ asset('assets/images/pixel-logo.png') }}" class="w-100"
                                            alt="" />
                                    </button>
                                    <label for="mediaInput" class="btn btn-transparent text-dark"><i
                                            class="fas fa-paperclip" style="font-size: 20px"></i></label>
                                    <input class="d-block w-100 form-control d-none" type="file" name="media[]" multiple
                                        accept="video/*, image/*" id="mediaInput" />
                                    <input type="hidden" name="media_type" value="image" id="mediaType" />
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

                            {{-- LinkedIn Account --}}
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
                                @if ($action == 'draft')
                                    @if ($post->draft == 1)
                                        <button type="button" name="draft" class="btn btn-custom"><i
                                                class="fas fa-folder d-inline-block me-1"></i> Save Changes</button>
                                    @else
                                        <button type="button" name="save_as_draft" class="btn btn-custom"><i class="fas fa-folder d-inline-block me-1"></i> Save as Draft</button>
                                    @endif
                                @endif
                            @endcan
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            @can('scheduled_post')
                                @if ($action == 'schedule')
                                    <button type="button" class="btn btn-custom" data-bs-toggle="modal"
                                        data-bs-target="#scheduleModal"><i
                                            class="fas fa-calendar-alt d-inline-block me-1"></i>
                                        Schedule</button>
                                @endif
                            @endcan
                            @can('re_post')
                                @if ($action == 'repost')
                                    <button type="submit" name="post" class="btn btn-custom"><i
                                            class="fas fa-share-square d-inline-block me-1"></i> Re Post</button>
                                @endcan
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-4">
                <div class="uploaded-images-container p-4 bg-white rounded-6 overflow-scroll"
                    style="height: calc(100vh - (0px + 73px + 40px));">
                    <h5>Uploaded Images</h5>
                    <div class="row" id="uploadedImages">
                        @if ($post->media != null)
                            @if ($post->media_type == 'image')
                                @foreach (explode(',', $post->media) as $index => $media)
                                    <div class="preview-wrapper">
                                        <img src="{{ asset($media) }}" alt="" class="preview"
                                            data-index="{{ $index }}">
                                        <button class="remove-btn">×</button>
                                    </div>
                                @endforeach
                            @endif
                        @endif
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