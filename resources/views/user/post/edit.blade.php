{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', ucfirst($action) . ' Post')

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
    <!-- Include Emoji Picker Element library -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .editor-container {
            width: 100%;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .editor-container textarea {
            flex: 1;
            border: none;
            border-bottom: 1px solid #ccc;
            resize: none;
            background-color: #f5f5f5;
            outline: none;
        }

        .toolbar {
            display: flex;
            justify-content: flex-start;
            gap: 20px;
            background-color: #f5f5f5;
            padding: 10px;
            width: fit-content
        }

        .editor {
            border: 1px solid #ccc;
            padding: 10px;
            min-height: 200px;
            overflow-y: auto;
        }

        .toolbar button {
            margin-right: 5px;
            padding: 5px 10px;
            background-color: #000;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            margin-bottom: 10px;
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


        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                document.querySelectorAll('.like-count').forEach((likeCount) => {
                    likeCount.textContent = parseInt(likeCount.textContent) + 1;
                });
            }
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

            const mediatype = images[0].type === 'video/mp4' ? 'video' : 'image'

            $("#mediaType").val(mediatype);

            let formData = new FormData(this);

            // Append each file in the images array to the FormData
            images.forEach((file, index) => {
                formData.append('media[]', file);
            });

            if ($(this).valid()) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.post.new.store') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        showLoadingModal();
                    },
                    success: function(response) {
                        $("#post_schedule_date").val("");
                        $("#post_schedule_time").val("");
                        hideLoadingModal();

                        $("#scheduleModal").removeClass("show");
                        $("#scheduleModal").css("display", "none");

                        if (response.status == 200) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 700
                            }).then(() => {
                                window.location.href = "{{ route('user.post.index') }}";
                            });
                        } else if (response.status == 400) {
                            toastr.error(response.error);
                        } else {
                            let errorsHtml = ``;

                            $.each(response.success, function(index, alert) {
                                errorsHtml += `
                                    <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
                                        <strong>${alert.heading}</strong>: ${alert.message}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                `;
                            });

                            $.each(response.errors, function(index, alert) {
                                errorsHtml += `
                                    <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
                                        <strong>${alert.heading}</strong>: ${alert.message}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                `;
                            });
                            $("#errors").html(errorsHtml);
                        }
                    }
                });
            }
        });

        // Draft Form
        $('#postForm button[name="draft"]').click(function(e) {
            e.preventDefault();

            const mediatype = images[0].type === 'video/mp4' ? 'video' : 'image'
            $("#mediaType").val(mediatype);
            let form = $('#postForm')[0];
            let formData = new FormData(form);

            images.forEach((file, index) => {
                formData.append('media[]', file);
            });

            if ($(this).valid()) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.post.draft.update') }}",
                    data: formData,
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
                                window.location.href = "{{ route('user.post.draft') }}";
                            });
                        } else {
                            toastr.error(response.error);

                            hideLoadingModal()
                        }
                    }
                });
            }
        });

        // Save as Draft
        $('#postForm button[name="save_as_draft"]').click(function(e) {
            e.preventDefault();

            const mediatype = images[0].type === 'video/mp4' ? 'video' : 'image'
            $("#mediaType").val(mediatype);
            let form = $('#postForm')[0];
            let formData = new FormData(form);

            images.forEach((file, index) => {
                formData.append('media[]', file);
            });

            if ($(this).valid()) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.post.draft.store.new') }}",
                    data: formData,
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
                                window.location.href = "{{ route('user.post.draft') }}";
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

        // Google Business Profiles
        function getGoogleBusinessProfiles(element) {
            if (element.checked) {
                $("#googleBusinessProfileModal").modal('show');
                $("#googleBusinessProfileSelectSection").fadeIn();
                $.ajax({
                    type: "GET",
                    url: "{{ route('user.google.business.profiles') }}",
                    beforeSend: function() {
                        $("#googleBusinessProfileSelect").html(`<option value="">Loading...</option>`);
                    },
                    success: function(response) {
                        html = `<option value="">Select</option>`;

                        if (response.error) {
                            toastr.error(response.error);
                        } else {
                            if (response.length > 0) {
                                response.forEach((account) => {
                                    html +=
                                        `<option value="${account.account_id} - ${account.location_id} - ${account.location_name} - ${account.location_phone}"
                                    ${account.location_name == @json($post->business_profile_name) ? 'selected' : ''} 
                                        >${account.location_name} (${account.location_id.split('/')[1]})</option>`
                                })
                            } else {
                                html = `<option value="">No Business Profile Found</option>`;
                            }
                        }

                        $("#googleBusinessProfileSelect").html(html);
                    }
                });
            }
        }

        // Google Business Profile Additional Features
        $("#googleBusinessProfileModalBtn").change(function(e) {
            e.preventDefault();
            $("#googleBusinessProfileModalUrl").val("");
            ($(this).val() != "" && $(this).val() != "CALL") ? $("#googleBusinessProfileModalUrlSection").fadeIn():
                $("#googleBusinessProfileModalUrlSection").fadeOut();
        });

        $("#googleBusinessProfileForm").submit(function(e) {
            e.preventDefault();
            if ($(this).valid()) {
                $("#googleBusinessProfileModal").modal('hide');
                $("#post_business_profile_action_btn").val($("#googleBusinessProfileModalBtn").val());
                $("#post_business_profile_action_url").val($("#googleBusinessProfileModalUrl").val());
            }
        });


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
                                    ${account.id == @json($post->linkedin_company_id) ? 'selected' : ''}
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

            @if ($post->on_linkedin == 1)
                $("#onLinkedInCheckbox").click();
            @endif

            @if ($post->on_business_profile == 1)
                $("#onGoogleBusinessProfileCheckbox").click();
            @endif
        });

        // Pixel api functionality
        var currentPage = 1;
        var perPage = 12;
        var isLoading = false;
        var imagesLoaded = 0;
        var totalImages = 0;
        var selectedImages = [];
        var images = [];

        const asset = `{{ env('APP_URL') }}`;
        let postsData = @json($post->media);

        if (typeof postsData != null) {
            let posts = postsData.split(',');

            let newposts = posts.map((post) => {
                return asset + '/' + post;
            })

            function setImage() {
                const fetchPromises = newposts.map((image, index) => {
                    return fetch(image)
                        .then(response => response.blob())
                        .then(blob => {
                            const fileName = posts[index].split('http://vrajesh.localhost/')[0];
                            const file = new File([blob], fileName, {
                                type: blob.type
                            });
                            if (!validateFiles(images)) {
                                return;
                            }
                            images.push(file);
                        })
                        .catch(error => {
                            console.error('Error fetching image:', error);
                        });
                });

                // Wait for all fetch operations to complete
                Promise.all(fetchPromises).then(() => {

                    showPreview()
                    $('#imagesModal .btn-close').click(); // Close the modal
                });
            }

            setImage();
        }

        // Function to load more images or videos
        function loadMoreMedia() {
            if (!isLoading) {
                currentPage++;
                const mediaType = $("#mediaTypeSelect").val();
                getPixels(mediaType, $("#imagesModalSearchInput").val(), currentPage, perPage);
            }
        }

        // Function to fetch media from the server
        function getPixels(type, q, page = 1, per_page = 12) {
            const mediaInput = document.getElementById('mediaInput');

            if (q.length == 0) {
                q = 'green';
            }

            $.ajax({
                type: "GET",
                url: `{{ env('APP_URL') }}/pixels/${type}/${q}?page=${page}&per_page=${per_page}&q=${q}`,
                beforeSend: function() {
                    isLoading = true;
                    $("#loading-indicator").show();
                    $("#load-more-btn").hide();
                },
                success: function(response) {
                    if (response.status == 200) {
                        var html = '';
                        totalImages += response.data[type].length;
                        $.each(response.data[type], function(indexInArray, media) {
                            if (type === 'photos') {
                                html += `<div class="col-lg-4 p-0">
                            <img src="${media.src.landscape}" class="img-fluid selectable-image" data-image-id="${media.id}" data-src="${media.src.landscape}" />
                        </div>`;
                            } else if (type === 'videos') {
                                html += `<div class="col-lg-4 p-0">
                            <video src="${media.video_files[0].link}" class="img-fluid selectable-image" data-image-id="${media.id}" data-src="${media.video_files[0].link}" controls></video>
                        </div>`;
                            }
                        });
                        $("#images").append(html);

                    } else {
                        toastr.error(response.error);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error("An error occurred while fetching media.");
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
            const mediaType = $("#mediaTypeSelect").val();
            getPixels(mediaType, $("#imagesModalSearchInput").val(), currentPage, perPage);
        });

        // Attach click event handler to the "Load More" button
        $("#load-more-btn").click(loadMoreMedia);

        // Attach click event handler to the search button
        $("#searchButton").click(function() {
            currentPage = 1;
            totalImages = 0;
            imagesLoaded = 0;
            $("#images").html("");
            const mediaType = $("#mediaTypeSelect").val();
            getPixels(mediaType, $("#imagesModalSearchInput").val(), currentPage, perPage);
        });

        // Attach change event handler to the media type dropdown
        $("#mediaTypeSelect").change(function() {
            currentPage = 1;
            totalImages = 0;
            imagesLoaded = 0;
            $("#images").html("");
            const mediaType = $(this).val();
            getPixels(mediaType, $("#imagesModalSearchInput").val(), currentPage, perPage);
        });

        // Event delegation for image and video selection
        $(document).on('click', '.selectable-image', function() {
            var imageId = $(this).data('image-id');
            var imageUrl = $(this).data('src');
            var mediaType = $("#mediaTypeSelect").val();

            // Toggle selection on the clicked image
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
                $(this).closest('div').removeClass('selected-container');
                selectedImages = selectedImages.filter(img => img.id !== imageId);
            } else {
                if (mediaType === 'videos') {
                    // If media type is video, ensure only one video can be selected
                    selectedImages.forEach(img => {
                        $(`.selectable-image[data-image-id="${img.id}"]`).removeClass('selected');
                        $(`.selectable-image[data-image-id="${img.id}"]`).closest('div').removeClass(
                            'selected-container');
                    });
                    selectedImages = [];
                }
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

        // Function to update input files after selecting images or videos from modal
        function updateInputFiles() {
            const fetchPromises = selectedImages.map(image => {
                const cleanImageUrl = image.src.split('?')[0];
                return fetch(cleanImageUrl)
                    .then(response => response.blob())
                    .then(blob => {
                        const fileName = cleanImageUrl.split('/').pop();
                        let fileType = blob.type;

                        // If blob.type is empty, determine the type based on the file extension
                        if (!fileType) {
                            const extension = fileName.split('.').pop().toLowerCase();
                            switch (extension) {
                                case 'mp4':
                                    fileType = 'video/mp4';
                                    break;
                                case 'mov':
                                    fileType = 'video/quicktime';
                                    break;
                                case 'ogg':
                                    fileType = 'video/ogg';
                                    break;
                                case 'qt':
                                    fileType = 'video/quicktime';
                                    break;
                                case 'jpg':
                                case 'jpeg':
                                    fileType = 'image/jpeg';
                                    break;
                                case 'png':
                                    fileType = 'image/png';
                                    break;
                                case 'gif':
                                    fileType = 'image/gif';
                                    break;
                                default:
                                    fileType = 'application/octet-stream';
                            }
                        }

                        const file = new File([blob], fileName, {
                            type: fileType
                        });

                        if (!validateFiles(images)) {
                            return;
                        }
                        images.push(file);
                    })
                    .catch(error => {
                        console.error('Error fetching image:', error);
                    });
            });

            // Wait for all fetch operations to complete
            Promise.all(fetchPromises).then(() => {
                showPreview();
                $('#imagesModal .btn-close').click(); // Close the modal
            });

            selectedImages.length = 0;

            // Reset modal done button
            $("#imagesModalDoneBtn").prop("disabled", true);
            $("#imagesModalDoneBtn").html('<i class="fas fa-check"></i> Done');
        }

        // Event listener for the modal's done button click event
        $(document).on('click', '#imagesModalDoneBtn', function() {
            // Handle Done button click
            $("#imagesModalDoneBtn").prop("disabled", true); // Disable Done button
            // add loader in the done button
            $("#imagesModalDoneBtn").text('Loading...');

            if (selectedImages.length > 0) {
                updateInputFiles();
            } else {
                $('#imagesModal .btn-close').click(); // Close the modal if no images are selected
            }
        });

        // Function to validate file selection
        document.getElementById('mediaInput').addEventListener('change', function() {
            const mediaInput = this;
            const newFiles = Array.from(this.files);

            if (!validateFiles(newFiles)) {
                // Clear the input field by replacing it with a new input element
                const newInput = document.createElement('input');
                newInput.type = 'file';
                newInput.name = 'media[]';
                newInput.multiple = true;
                newInput.accept = 'video/*, image/*';
                newInput.id = 'mediaInput';
                newInput.className = 'd-block w-100 form-control d-none';

                // Add the event listener to the new input element
                newInput.addEventListener('change', arguments.callee);

                // Replace the old input element with the new one
                mediaInput.parentNode.replaceChild(newInput, mediaInput);
                return;
            }

            newFiles.forEach(file => {
                images.push(file);
            });

            // Clear the input field by replacing it with a new input element
            const newInput = document.createElement('input');
            newInput.type = 'file';
            newInput.name = 'media[]';
            newInput.multiple = true;
            newInput.accept = 'video/*, image/*';
            newInput.id = 'mediaInput';
            newInput.className = 'd-block w-100 form-control d-none';

            // Add the event listener to the new input element
            newInput.addEventListener('change', arguments.callee);

            // Replace the old input element with the new one
            mediaInput.parentNode.replaceChild(newInput, mediaInput);

            showPreview();
        });

        // Function to validate file selection
        function validateFiles(files) {
            let videoCount = 0;
            let imageCount = 0;

            for (let file of files) {
                if (file.type.startsWith('video/')) {
                    if (file.size > 500 * 1024 * 1024) { // 500MB limit for videos
                        toastr.error(`Video ${file.name} exceeds the 500MB size limit and will not be added.`);
                        continue;
                    }
                    videoCount++;
                } else if (file.type.startsWith('image/')) {
                    if (file.size > 5 * 1024 * 1024) { // 5MB limit for images
                        toastr.error(`Image ${file.name} exceeds the 5MB size limit and will not be added.`);
                        continue;
                    }
                    imageCount++;
                }
            }

            for (let file of images) {
                if (file.type.startsWith('video/')) {
                    if (file.size > 500 * 1024 * 1024) { // 500MB limit for videos
                        toastr.error(`Video ${file.name} exceeds the 500MB size limit and will not be added.`);
                        continue;
                    }
                    videoCount++;
                } else if (file.type.startsWith('image/')) {
                    if (file.size > 5 * 1024 * 1024) { // 5MB limit for images
                        toastr.error(`Image ${file.name} exceeds the 5MB size limit and will not be added.`);
                        continue;
                    }
                    imageCount++;
                }
            }

            if (videoCount > 1 || (videoCount === 1 && imageCount > 0)) {
                toastr.error("You can upload only one video or multiple images at a time.");
                return false;
            }

            return true;
        }

        // Function to show preview of uploaded images/videos
        function showPreview() {
            const uploadedImagesContainer = document.getElementById('uploadedImages');
            uploadedImagesContainer.innerHTML = ''; // Clear any existing previews

            images.forEach((file, index) => {
                const fileReader = new FileReader();
                fileReader.onload = function(e) {
                    const fileURL = e.target.result;

                    const container = document.createElement('div');
                    container.classList.add('preview-wrapper');
                    container.style.position = 'relative';

                    let mediaElement;
                    const fileType = file.type || file.name.split('.').pop().toLowerCase();

                    if (fileType.startsWith('image/') || fileType === 'jpg' || fileType === 'jpeg' ||
                        fileType === 'png' || fileType === 'gif') {
                        mediaElement = document.createElement('img');
                        mediaElement.src = fileURL;
                    } else if (fileType.startsWith('video/') || fileType === 'mp4' || fileType === 'webm' ||
                        fileType === 'ogg') {
                        mediaElement = document.createElement('video');
                        mediaElement.src = fileURL;
                        mediaElement.controls = false;
                    }

                    if (!mediaElement) {
                        console.error('Failed to create media element for file:', file);
                        return;
                    }

                    mediaElement.style.width = '100px';
                    mediaElement.style.height = '100px';
                    mediaElement.style.objectFit = 'cover';
                    mediaElement.style.borderRadius = '5px';
                    mediaElement.style.border = '2px solid #ddd';
                    mediaElement.style.padding = '5px';

                    // Add remove button
                    const removeBtn = document.createElement('button');
                    removeBtn.classList.add('remove-btn');
                    removeBtn.innerHTML = '&times;';
                    removeBtn.addEventListener('click', () => {
                        images.splice(index, 1); // Remove the file from the images array
                        showPreview(); // Refresh the preview
                    });

                    container.appendChild(mediaElement);
                    container.appendChild(removeBtn);
                    uploadedImagesContainer.appendChild(container);
                };
                fileReader.readAsDataURL(file);
            });
        }
    </script>
    <script>
        let stateStack = [];
        let isUndoing = false;

        const button = document.querySelector('#emoji-button');
        const picker = document.querySelector('#emoji-picker');

        button.addEventListener('click', () => {
            picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
        });

        picker.addEventListener('emoji-click', event => {
            const emoji = event.detail.unicode;
            insertAtCursor(emoji);
            picker.style.display = 'none';
        });

        function insertAtCursor(text) {
            const textarea = document.getElementById('content');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;

            textarea.setRangeText(text, start, end, 'end');
            one_way();
        }

        const upperDiffBold = "𝗔".codePointAt(0) - "A".codePointAt(0);
        const lowerDiffBold = "𝗮".codePointAt(0) - "a".codePointAt(0);

        const upperDiffItalic = "𝘈".codePointAt(0) - "A".codePointAt(0);
        const lowerDiffItalic = "𝘢".codePointAt(0) - "a".codePointAt(0);

        const upperDiffBoldItalic = "𝘼".codePointAt(0) - "A".codePointAt(0);
        const lowerDiffBoldItalic = "𝙖".codePointAt(0) - "a".codePointAt(0);

        const isUpper = (n) => n >= 65 && n < 91;
        const isLower = (n) => n >= 97 && n < 123;

        const transformChar = (char, upperDiff, lowerDiff) => {
            const n = char.charCodeAt(0);
            if (isUpper(n)) return String.fromCodePoint(n + upperDiff);
            if (isLower(n)) return String.fromCodePoint(n + lowerDiff);
            return char;
        };

        const transformWord = (word, upperDiff, lowerDiff) => [...word].map(char => transformChar(char, upperDiff,
            lowerDiff)).join('');

        const bolderize = (text) => transformWord(text, upperDiffBold, lowerDiffBold);
        const italicize = (text) => transformWord(text, upperDiffItalic, lowerDiffItalic);
        const boldItalicize = (text) => transformWord(text, upperDiffBoldItalic, lowerDiffBoldItalic);
        const underline = (text) => text.split('').map(char => char + '\u0332').join('');

        function applyFormatting(type) {
            const textarea = document.getElementById('content');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            let selectedText = textarea.value.slice(start, end);

            let formattedText = selectedText;
            switch (type) {
                case 'bold':
                    formattedText = bolderize(selectedText);
                    break;
                case 'italic':
                    formattedText = italicize(selectedText);
                    break;
                case 'underline':
                    formattedText = underline(selectedText);
                    break;
                default:
                    break;
            }

            textarea.setRangeText(formattedText, start, end, 'end');
            one_way();
        }

        function insertList(type) {
            const textarea = document.getElementById('content');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selectedText = textarea.value.slice(start, end);

            let listItems = selectedText.split('\n').map(item => item.trim());
            let formattedList = "";

            if (type === 'unordered') {
                formattedList = listItems.map(item => '• ' + item).join('\n');
            } else if (type === 'ordered') {
                formattedList = listItems.map((item, index) => `${index + 1}. ${item}`).join('\n');
            }

            textarea.setRangeText(formattedList, start, end, 'end');
        }

        function one_way() {
            if (!isUndoing) {
                const input = document.getElementById('content').value;
                stateStack.push(input);
            }
            isUndoing = false;
            const input = document.getElementById('content').value;
        }

        function undoChange() {
            if (stateStack.length > 1) {
                isUndoing = true;
                stateStack.pop();
                const lastState = stateStack[stateStack.length - 1];
                document.getElementById('content').value = lastState;
                one_way();
            }
        }

        function detectLineChange(event) {
            if (event.key === 'Enter') {
                one_way();
            }
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
                    <input type="date" name="schedule_date" id="post_schedule_date" class="d-none" />
                    <input type="time" name="schedule_time" id="post_schedule_time" class="d-none" />
                    <input type="hidden" name="post_id" value="{{ $post->id }}">

                    <div class="d-flex flex-column flex-grow-1">
                        <div class="mb-3">
                            <input class="input-tag-title d-block h-100 w-100 form-control" name="title"
                                value="{{ $post->title }}" placeholder="Enter your title here" />
                        </div>
                        <div class="editor-container">
                            <div class="toolbar">
                                <button type="button" id="bold-btn" onclick="applyFormatting('bold')"><i
                                        class="fas fa-bold"></i></button>
                                <button type="button" id="italic-btn" onclick="applyFormatting('italic')"><i
                                        class="fas fa-italic"></i></button>
                                <button type="button" id="underline-btn" onclick="applyFormatting('underline')"><i
                                        class="fas fa-underline"></i></button>
                                <button type="button" id="unordered-list-btn" onclick="insertList('unordered')"><i
                                        class="fas fa-list-ul"></i></button>
                                <button type="button" id="ordered-list-btn" onclick="insertList('ordered')"><i
                                        class="fas fa-list-ol"></i></button>
                                <button type="button" id="undo-btn" onclick="undoChange()"><i
                                        class="fas fa-undo"></i></button>
                                <div style="position: relative;">
                                    <button type="button" id="emoji-button">😊</button>
                                    <emoji-picker id="emoji-picker"
                                        style="position: absolute; display: none; top: 100%;"></emoji-picker>
                                </div>
                            </div>
                            <textarea id="content" name="description" style="width: 100%;" oninput="one_way()" onkeydown="detectLineChange(event)"
                                placeholder="Enter text here...">{{ $post->description }}</textarea>
                        </div>

                        <div class="w-100 my-1 d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <p class="mb-0">Check to share on:</p>

                                @if (Auth::guard('web')->user()->canAny(['meta_facebook_text_post', 'meta_facebook_image_post', 'meta_facebook_video_post']))
                                    @if (Auth::guard('web')->user()->meta_access_token != null)
                                        <label class="d-inline-block platform-checkbox">
                                            <input type="checkbox" id="onFacebookCheckbox" name="on_facebook"
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
                                            <input type="checkbox" id="onInstagramCheckbox" name="on_instagram"
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
                                            <input type="checkbox" id="onLinkedInCheckbox" name="on_linkedin"
                                                onchange="getLinkedInOrganizations(this)" value="1"
                                                data-bs-toggle="linkedin-post" class="form-check-input toggle-post" />
                                            -
                                            <span class="d-inline-block ms-1">
                                                <i class="fab fa-linkedin-in" style="font-size: 17px"></i>
                                            </span>
                                        </label>
                                    @endif
                                @endif

                                @if (Auth::guard('web')->user()->canAny(['google_text_post', 'google_image_post']))
                                    @if (Auth::guard('web')->user()->google_access_token != null)
                                        <label class="d-inline-block platform-checkbox">
                                            <input type="checkbox" name="on_business_profile"
                                                onchange="getGoogleBusinessProfiles(this)" value="1"
                                                data-bs-toggle="business-profile-post"
                                                id="onGoogleBusinessProfileCheckbox"
                                                class="form-check-input toggle-post" />
                                            -
                                            <span class="d-inline-block ms-1"><i class="fab fa-google"
                                                    style="font-size: 15px"></i></span>
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
                                    <button type="button" class="btn btn-dark btn-sm"
                                        style="padding: 5px 7px; font-size: 12px;" data-bs-toggle="modal"
                                        data-bs-target="#imagesModal">Free Media
                                    </button>
                                    <label for="mediaInput" class="btn btn-transparent text-dark"><i
                                            class="fas fa-paperclip" style="font-size: 20px"></i></label>
                                    <input class="d-block w-100 form-control d-none" type="file" name="media[]"
                                        multiple accept="video/*, image/*" id="mediaInput" />
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

                            {{-- Google Business Profiles --}}
                            @if (Auth::guard('web')->user()->canAny(['google_text_post', 'google_image_post']))
                                @if (Auth::guard('web')->user()->google_access_token != null)
                                    <div class="col-md-4">
                                        <div class="mb-2" style="display: none;"
                                            id="googleBusinessProfileSelectSection">
                                            <div class="d-flex flex-column gap-2">
                                                <p class="mb-0">Google Business Profiles:</p>
                                                <div class="d-flex flex-column gap-1 w-100">
                                                    <select name="google_business_profile"
                                                        id="googleBusinessProfileSelect" class="form-select w-100">
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
                                                class="fas fa-folder d-inline-block me-1"></i> Save changes</button>
                                    @else
                                        <button type="button" name="save_as_draft" class="btn btn-custom"><i
                                                class="fas fa-folder d-inline-block me-1"></i> Save as Draft</button>
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
                                            class="fas fa-share-square d-inline-block me-1"></i> Post</button>
                                @endif
                            @endcan
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-4">
                <div class="uploaded-images-container p-4 bg-white rounded-6 overflow-scroll"
                    style="height: calc(100vh - (0px + 73px + 40px));">
                    <h5>Uploaded Images</h5>
                    <div class="row" id="uploadedImages"></div>
                    <div class="d-flex flex-column gap-2" id="errors"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pixels Images Modal -->
    <div class="modal fade" id="imagesModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="imagesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header flex-column">
                    <div class="d-flex align-items-center w-100 justify-content-between">
                        <h5 class="modal-title" id="imagesModalLabel">Media</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="input-group mt-2">
                        <select class="form-select border-dark" id="mediaTypeSelect">
                            <option value="photos">Images</option>
                            <option value="videos">Videos</option>
                        </select>
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
                    <button type="button" class="btn btn-success" id="imagesModalDoneBtn" disabled><i
                            class="fas fa-check"></i> Done</button>
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
        <div class="modal fade" id="scheduleModal" data-bs-backdrop="static" tabindex="-1"
            aria-labelledby="scheduleModalLabel" aria-hidden="true">
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
