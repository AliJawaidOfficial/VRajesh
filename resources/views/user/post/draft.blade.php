{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Draft Posts')

{{-- Styles --}}
@section('styles')
    <style>
        /* Table Styles */
        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #303030;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
            border: 0px solid #ddd;
        }

        tr {
            border-bottom: 1px solid #ddd;
        }

        table tbody tr:last-child {
            border-bottom: none;
        }

        /* Post Styles */
        .post-title {
            font-size: 14px;
            -webkit-line-clamp: 1;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .post-date {
            text-wrap: nowrap;
            font-size: 14px;
        }

        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            list-style-type: none;
            padding: 0;
            margin-top: 20px;
        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination li a {
            display: block;
            padding: 4px 12px;
            color: #303030;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .pagination li a.active,
        .pagination li a:hover {
            background-color: #303030;
            color: white;
        }

        .pagination li.disabled a {
            color: #999;
            pointer-events: none;
            border-color: #ddd;
        }

        /* Filter Section Styles */
        .filter-section {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filter-section select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* Media Preview Styles */
        .media-preview {
            width: 40%;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-right: 1px solid #ddd;
            padding-right: 20px;
        }

        .post-details {
            flex-grow: 1;
            padding-left: 20px;
        }

        .platform-icons i {
            font-size: 24px;
            margin-right: 10px;
        }

        /* Button Styles */
        .btn-custom {
            width: 140px;
            font-weight: bold;
            color: #fff;
            background-image: linear-gradient(90deg, #ff6600 0%, #d89e33 100%);
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-image: linear-gradient(90deg, #d67f45 0%, #d89e33 100%);
            color: #fff;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 8px;
            border: none;
        }

        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }



        .form-check {
            height: max-content;
        }

        .modal-post-title {
            font-size: 20px;
            font-weight: bold;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
            padding-top: 0px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .modal-post-date {
            font-size: 14px;
            border-bottom: 1px solid #dee2e6;
            padding: 5px 0px 12px;
        }

        .modal-post-description {
            font-size: 14px;
            padding: 0px;
            position: relative;
            top: 0%;
        }

        .plaform-page-detail {
            border-top: 1px solid #dee2e6;
            margin-top: 10px;
            padding-top: 10px;
        }
    </style>
@endsection

{{-- Vendor Scripts --}}
@section('scripts')
    <script>
        // Function to show the post detail in a modal
        function showPostDetail(id) {
            $.ajax({
                type: "GET",
                url: `{{ route('user.post.index') }}/details/show/${id}`,
                dataType: "json",
                success: function(response) {
                    if (response.status == 200) {
                        let html = '';
                        let asset = `{{ asset('') }}`;
                        let mediaType = response.data.media_type;
                        let mediaContent = response.data.media;

                        html += `
                            <div class="media-preview w-50">
                                <div class="d-flex flex-wrap justify-content-start align-items-start w-100 overflow-auto">
                        `;

                        if (mediaContent != null) {
                            mediaContent = mediaContent.split(',');
                            if (mediaType == 'image') {
                                $.each(mediaContent, function(index, image) {
                                    html +=
                                        `<img src="${asset}${image}" class="img-fluid rounded mb-1" style="width: 100px; height: 100px;" />`;
                                });
                            } else if (mediaType == 'video') {
                                $.each(mediaContent, function(index, video) {
                                    html +=
                                        `<video controls class="w-100 rounded mb-1">
                                        <source src="${asset}${video}" type="video/mp4">Your browser does not support the video tag.</video>`;
                                });
                            }
                        } else {
                            html += `<p class="text-center text-muted my-auto">No image/video published</p>`;
                        }


                        html += `
                                </div>
                            </div>
                            <div class="post-details d-flex flex-column align-items-stretch w-50">
                                <h4 class="modal-post-title">Title: <span id="modalPostTitle">${response.data.title}</span></h4>
                                <p class="modal-post-date mb-1"><strong>Saved on:</strong> <span id="modalPostDate">${standardDateTimeFormat(response.data.created_at)}</span></p>
                                <div class="modal-post-description flex-grow-1 d-flex align-items-stretch flex-column"
                                    style="max-height: 200px; overflow-y: auto;">
                                    <p class="mb-0"  style="position:sticky; top:0;background-color:#fff;padding: 10px 0px 5px;"><strong>Description:</strong></p> <span id="modalPostDescription">${response.data.description.replace(/\n/g, '<br>')}</span>
                                </div>
                                <input type="hidden" id="postDetailId" value="${response.data.id}"/>
                                <div class="py-2">
                                    <div class="mb-2 plaform-page-detail">
                                        <strong>Platforms & Pages:</strong>
                                    </div>
                        `;
                        html += `
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" style="pointer-events: none; display: none"
                                            id="facebook-post-detail" ${(response.data.on_facebook) ? 'checked' : ''}>
                        `;
                        if (response.data.on_facebook) html +=
                            `<i class="fab fa-facebook m-0"></i><span class="m-0 ms-2">${response.data.facebook_page_name}</span>`;
                        html += `
                                    </div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" style="pointer-events: none; display: none"
                                            id="instagram-post-detail" ${(response.data.on_instagram) ? 'checked' : ''}>
                        `;
                        if (response.data.on_instagram) html +=
                            `<i class="fab fa-instagram"></i><span class="m-0 ms-2">${response.data.instagram_account_name}</span>`;
                        html += `
                                    </div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" style="pointer-events: none; display: none"
                                            id="linkedin-post-detail" ${(response.data.on_linkedin) ? 'checked' : ''}>
                        `;
                        if (response.data.on_linkedin) html +=
                            `<i class="fab fa-linkedin"></i><span class="m-0 ms-2">${response.data.linkedin_company_name}</span>`;
                        html += `
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        html += ` 
                            <input type="hidden" id="facebook_page_access_token" value="${response.data.facebook_page_access_token}"/>
                            <input type="hidden" id="facebook_page_id" value="${response.data.facebook_page_id}"/>
                            <input type="hidden" id="facebook_page_name" value="${response.data.facebook_page_name}"/>
                            <input type="hidden" id="instagram_account_id" value="${response.data.instagram_account_id}"/>
                            <input type="hidden" id="instagram_account_name" value="${response.data.instagram_account_name}"/>
                            <input type="hidden" id="linkedin_company_id" value="${response.data.linkedin_company_id}"/>
                            <input type="hidden" id="linkedin_company_name" value="${response.data.linkedin_company_name}"/>
                        `

                        $('#postDetail .modal-body').html(html);
                        $('#postDetail').modal('show');
                    } else {
                        toastr.error(response.error);
                    }
                }
            });
        }

        function deletePost() {
            $("#postDetail").modal("hide");
            const postId = $("#postDetailId").val();
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: `{{ route('user.post.index') }}/${postId}/delete`,
                        data: {
                            _token: "{{ csrf_token() }}",
                            post_id: postId
                        },
                        success: function(response) {
                            if (response.status == 200) {
                                swalWithBootstrapButtons.fire({
                                    title: "Deleted!",
                                    text: "Post has been deleted.",
                                    icon: "success",
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
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    $("#postDetail").modal("show");
                }
            });
        }

        async function transferPostData(action) {
            let id = $("#postDetailId").val();
            window.location.href = `{{ route('user.post.index') }}/${id}/${action}`;
        }

        @can('draft_post')
            // Draft Form
            $("#draftPostForm").submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: `{{ route('user.post.draft.update') }}`,
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $("#draftSaveBtn").attr('disabled', 'true');
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            $("#draftPostModal").modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            toastr.error(response.error);
                        }

                        $("#draftSaveBtn").removeAttr('disabled');
                    }
                });
            });
        @endcan

        @can('scheduled_post')
            // Schedule Form
            $("#schedulePostForm").submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: `{{ route('user.post.new.store') }}`,
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $("#scheduleSaveBtn").attr('disabled', 'true');
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            $("#schedulePostModal").modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: "Post scheduled successfully",
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            toastr.error(response.error);
                        }

                        $("#scheduleSaveBtn").removeAttr('disabled');
                    }
                });
            });
        @endcan

        @can('re_post')
            // Repost Form
            $("#repostPostForm").submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: `{{ route('user.post.new.store') }}`,
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $("#repostSaveBtn").attr('disabled', 'true');
                    },
                    success: function(response) {
                        if (response.status == 200) {
                            $("#repostModal").modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: "Post reposted successfully",
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            })
                        } else {
                            toastr.error(response.error);
                        }

                        $("#repostSaveBtn").removeAttr('disabled');
                    }
                });
            });
        @endcan

        // Facebook Pages
        function getFacebookPages(element) {
            return new Promise((resolve, reject) => {
                if (element.is(":checked")) {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('user.facebook.pages') }}",
                        success: function(response) {
                            let html = `<option value="">Select</option>`;

                            let pageId = $('#facebook_page_id').val();


                            if (response.length > 0) {
                                response.forEach((page) => {
                                    html +=
                                        `<option value="${page.id} - ${page.access_token} - ${page.name}"`;
                                    if (page.id == pageId) html += ` selected `;
                                    html += `>${page.name}</option>`;
                                });
                            } else {
                                html = `<option value="">No Page Found</option>`;
                            }
                            resolve(html);
                        },
                        error: function(err) {
                            reject("Failed to load Facebook pages: " + err);
                        }
                    });
                } else {
                    reject("Checkbox is not checked");
                }
            });
        }

        // Instagram Accounts
        function getInstagramAccounts(element) {
            return new Promise((resolve, reject) => {
                if (element.is(":checked")) {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('user.instagram.accounts') }}",
                        success: function(response) {
                            let html = `<option value="">Select</option>`;


                            let account_id = $('#instagram_account_id').val();

                            if (response.length > 0) {
                                response.forEach((account) => {
                                    html +=
                                        `<option value="${account.ig_business_account} - ${account.name}"`
                                    if (account.ig_business_account == account_id) html +=
                                        ` selected `
                                    html += `>${account.name}</option>`;
                                });
                            } else {
                                html = `<option value="">No Account Found</option>`;
                            }
                            resolve(html);
                        },
                        error: function(err) {
                            reject("Failed to load Instagram accounts: " + err);
                        }
                    });
                } else {
                    reject("Checkbox is not checked");
                }
            });
        }

        // LinkedIn Organizations
        function getLinkedInOrganizations(element) {
            return new Promise((resolve, reject) => {
                if (element.is(":checked")) {
                    $.ajax({
                        type: "GET",
                        url: "{{ route('user.linkedin.organizations') }}",
                        success: function(response) {
                            let html = `<option value="">Select</option>`;


                            let pageId = $('#linkedin_company_id').val();


                            if (response.length > 0) {
                                response.forEach((account) => {
                                    html +=
                                        `<option value="${account.id} - ${account.name}"`
                                    if (account.id == pageId) html +=
                                        ` selected `
                                    html +=
                                        `>${account.name} (${account.vanity_name})</option>`;
                                });
                            } else {
                                html = `<option value="">No Account Found</option>`;
                            }
                            resolve(html);
                        },
                        error: function(err) {
                            reject("Failed to load LinkedIn organizations: " + err);
                        }
                    });
                } else {
                    reject("Checkbox is not checked");
                }
            });
        }
    </script>
@endsection


{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">
        <div class="h3 p-3 mb-4 bg-white text-center rounded-6">
            <h3 class="mb-0">Draft Posts</h3>
        </div>

        <form id="filterForm" class="filter-section d-flex align-items-center justify-content-end bg-white rounded-6">
            <div class="p-3">
                <label for="filterMonth">Filter by Month: </label>
                {{-- current month selected by default --}}
                <select id="filterMonth" name="month" onchange="this.form.submit()">
                    <option value="">All</option>
                    @foreach ($postMonths as $month)
                        <option value="{{ $month }}" {{ request()->get('month') == $month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($month)->format('F Y') }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <div class="table-wrapper bg-white rounded-6">
            <table>
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th class="text-left">Title</th>
                        <th class="text-nowrap">Platforms</th>
                        <th class="text-nowrap">Saved Date</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($dataSet) == 0)
                        <tr>
                            <td colspan="4" class="text-center">Nothing to show you</td>
                        </tr>
                    @endif
                    @foreach ($dataSet as $post)
                        <tr onclick="showPostDetail({{ $post->id }})">
                            <td class="text-center">
                                {{ ($dataSet->currentPage() - 1) * $dataSet->perPage() + $loop->iteration }}</td>
                            <td>
                                <p class="post-title mb-0">{{ $post->title }}</p>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if ($post->on_facebook)
                                        <div
                                            class="badge p-1 bg-facebook rounded-2 text-light d-flex gap-1 align-items-start">
                                            <i class="fab fa-facebook"></i>
                                            <span
                                                class="m-0 lh-1 border-start border-light ps-1">{{ $post->facebook_page_name }}</span>
                                        </div>
                                    @endif
                                    @if ($post->on_instagram)
                                        <div
                                            class="badge p-1 bg-instagram rounded-2 text-light d-flex gap-1 align-items-start">
                                            <i class="fab fa-instagram"></i>
                                            <span
                                                class="m-0 lh-1 border-start border-light ps-1">{{ $post->instagram_account_name }}</span>
                                        </div>
                                    @endif
                                    @if ($post->on_linkedin)
                                        <div
                                            class="badge p-1 bg-linkedin rounded-2 text-light d-flex gap-1 align-items-start">
                                            <i class="fab fa-linkedin-in"></i>
                                            <span
                                                class="m-0 lh-1 border-start border-light ps-1">{{ $post->linkedin_company_name }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <p class="post-date mb-0">
                                    {{ date('d/M/Y', strtotime($post->created_at)) }}
                                    <span
                                        class="badge p-1 bg-warning rounded-2 text-dark">{{ date('h:i A', strtotime($post->created_at)) }}</span>
                                </p>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Modal for Post Details --}}
        <div class="modal fade" id="postDetail" tabindex="-1" aria-labelledby="postDetailLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-6">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="postDetailLabel">Post Details</h5>
                        <button type="button" class="btn-close" style="filter: invert(1)" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex"></div>
                    <div class="modal-footer d-flex justify-content-between">
                        <div>
                            <button type="button" class="btn btn-custom" onclick="deletePost()"><i
                                    class="fas fa-trash d-inline-block me-1"></i> Delete</button>
                            @can('draft_post')
                                <a type="button" class="btn btn-custom" onclick="transferPostData('draft')"><i
                                        class="fas fa-pen d-inline-block me-1"></i> Edit</a>
                            @endcan
                        </div>
                        <div>
                            @can('scheduled_post')
                                <button type="button" class="btn btn-custom" onclick="transferPostData('schedule')"><i
                                        class="fas fa-calendar-alt d-inline-block me-1"></i> Schedule</button>
                            @endcan
                            @can('immediate_post')
                                <button type="button" class="btn btn-custom" onclick="transferPostData('repost')"><i
                                        class="fas fa-share-square d-inline-block me-1"></i> Repost</button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Draft Post Modal --}}
        @can('draft_post')
            <div class="modal fade" id="draftPostModal" tabindex="-1" aria-labelledby="editPostModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title" id="editPostModalLabel">Draft Post</h5>
                            <button type="button" class="btn-close" style="filter: invert(1)" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="draftPostForm" enctype="multipart/form-data" method="POST">
                            @method('POST')
                            @csrf
                            <input type="hidden" name="post_id" id="postID">
                            <div class="modal-body d-flex align-items-stretch flex-column gap-3">
                                <div>
                                    <label for="postTitle" class="form-label">Title</label>
                                    <input class="input-tag-title d-block h-100 w-100 form-control" id="postTitle"
                                        name="title" placeholder="Enter your title here" required />
                                </div>
                                <div
                                    class="textarea-wrapper my-1 flex-grow-1 d-flex flex-column align-items-stretch justify-content-center">
                                    <label for="postDescription" class="form-label">Description</label>
                                    <textarea class="input-tag-description d-block h-100 w-100 form-control" id="postDescription" name="description"
                                        placeholder="Enter your post description"></textarea>
                                </div>
                                @if (Auth::guard('web')->user()->canAny([
                                            'meta_facebook_image_post',
                                            'meta_facebook_video_post',
                                            'meta_instagram_image_post',
                                            'meta_instagram_video_post',
                                            'linkedin_image_post',
                                            'linkedin_video_post',
                                        ]))
                                    <div>
                                        <label for="media" class="form-label">Media</label>
                                        <input type="file" class="form-control" id="media" name="media"
                                            accept="image/*">
                                    </div>
                                @endif
                                <div class="d-flex align-items-center gap-3 py-2">
                                    <div><strong>Platforms</strong></div>
                                    @if (Auth::guard('web')->user()->canAny(['meta_facebook_text_post', 'meta_facebook_image_post', 'meta_facebook_video_post']))
                                        @if (Auth::guard('web')->user()->meta_access_token != null)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="on_facebook"
                                                    value="1" id="PlatformFacebook">
                                                <label class="form-check-label" for="PlatformFacebook">
                                                    <i class="fab fa-facebook-f"></i>
                                                </label>
                                            </div>
                                        @endif
                                    @endif
                                    @if (Auth::guard('web')->user()->canAny(['meta_instagram_image_post', 'meta_instagram_video_post']))
                                        @if (Auth::guard('web')->user()->meta_access_token != null)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="on_instagram"
                                                    value="1" id="PlatformInstagram">
                                                <label class="form-check-label" for="PlatformInstagram">
                                                    <i class="fab fa-instagram"></i>
                                                </label>
                                            </div>
                                        @endif
                                    @endif
                                    @if (Auth::guard('web')->user()->canAny(['linkedin_text_post', 'linkedin_image_post', 'linkedin_video_post']))
                                        @if (Auth::guard('web')->user()->linkedin_access_token != null)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="on_linkedin"
                                                    value="1" id="PlatformLinkedIn">
                                                <label class="form-check-label" for="PlatformLinkedIn">
                                                    <i class="fab fa-linkedin-in"></i>
                                                </label>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-3 w-100">
                                    @if (Auth::guard('web')->user()->canAny(['meta_facebook_text_post', 'meta_facebook_image_post', 'meta_facebook_video_post']))
                                        @if (Auth::guard('web')->user()->meta_access_token != null)
                                            <div class="w-100 d-flex align-items-center gap-1 flex-column">
                                                <label for="page" class="form-label d-block w-100 mb-0">Facebook</label>
                                                <select name="facebook_page" class="d-block w-100 form-select text-black"
                                                    id="facebookPage">
                                                    <option value="">Select Page</option>
                                                </select>
                                            </div>
                                        @endif
                                    @endif
                                    @if (Auth::guard('web')->user()->canAny(['meta_instagram_image_post', 'meta_instagram_video_post']))
                                        @if (Auth::guard('web')->user()->meta_access_token != null)
                                            <div class="w-100 d-flex align-items-center gap-1 flex-column">
                                                <label for="page" class="form-label d-block w-100 mb-0">Instagram</label>
                                                <select name="instagram_account" class="d-block w-100 form-select text-black"
                                                    id="instagramPage">
                                                    <option value="">Select Page</option>
                                                </select>
                                            </div>
                                        @endif
                                    @endif
                                    @if (Auth::guard('web')->user()->canAny(['linkedin_text_post', 'linkedin_image_post', 'linkedin_video_post']))
                                        @if (Auth::guard('web')->user()->linkedin_access_token != null)
                                            <div class="w-100 d-flex align-items-center gap-1 flex-column">
                                                <label for="page" class="form-label d-block w-100 mb-0">LinkedIn</label>
                                                <select name="linkedin_organization"
                                                    class="d-block w-100 form-select text-black" id="linkedInPage">
                                                    <option value="">Select Page</option>
                                                </select>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div>
                                    <button type="submit" class="btn btn-custom" id="draftSaveBtn">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan

        {{-- Schedule Post Modal --}}
        @can('scheduled_post')
            <div class="modal fade" id="schedulePostModal" tabindex="-1" aria-labelledby="schedulePostModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title" id="schedulePostModalLabel">Schedule Post</h5>
                            <button type="button" class="btn-close" style="filter: invert(1)" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="schedulePostForm" enctype="multipart/form-data" method="POST">
                            @method('POST')
                            @csrf
                            <input type="hidden" name="post_id" id="postID">
                            <div class="modal-body d-flex align-items-stretch flex-column gap-3">
                                <div>
                                    <label for="postTitle" class="form-label">Title</label>
                                    <input class="input-tag-title d-block h-100 w-100 form-control" id="postTitle"
                                        name="title" placeholder="Enter your title here" required />
                                </div>
                                <div
                                    class="textarea-wrapper my-1 flex-grow-1 d-flex flex-column align-items-stretch justify-content-center">
                                    <label for="postDescription" class="form-label">Description</label>
                                    <textarea class="input-tag-description d-block h-100 w-100 form-control" id="postDescription" name="description"
                                        placeholder="Enter your post description"></textarea>
                                </div>
                                <div class="date-time-inputs">
                                    <label for="schedulePostDate" class="form-label">Schedule Date & Time</label>
                                    <input type="date" min="{{ date('Y-m-d') }}" class="form-control mb-3"
                                        id="schedulePostDate" name="schedule_date" required>
                                    <label for="schedulePostTime" class="form-label">Time</label>
                                    <input type="time" class="form-control" id="schedulePostTime" name="schedule_time"
                                        required>
                                </div>
                                @if (Auth::guard('web')->user()->canAny([
                                            'meta_facebook_image_post',
                                            'meta_facebook_video_post',
                                            'meta_instagram_image_post',
                                            'meta_instagram_video_post',
                                            'linkedin_image_post',
                                            'linkedin_video_post',
                                        ]))
                                    <div>
                                        <label for="media" class="form-label">Media</label>
                                        <input type="file" class="form-control" id="media" name="media"
                                            accept="image/*">
                                    </div>
                                @endif
                                <div class="d-flex align-items-center gap-3 py-2">
                                    <div><strong>Platforms</strong></div>
                                    @if (Auth::guard('web')->user()->canAny(['meta_facebook_text_post', 'meta_facebook_image_post', 'meta_facebook_video_post']))
                                        @if (Auth::guard('web')->user()->meta_access_token != null)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="on_facebook"
                                                    value="1" id="PlatformFacebook">
                                                <label class="form-check-label" for="PlatformFacebook">
                                                    <i class="fab fa-facebook-f"></i>
                                                </label>
                                            </div>
                                        @endif
                                    @endif
                                    @if (Auth::guard('web')->user()->canAny(['meta_instagram_image_post', 'meta_instagram_video_post']))
                                        @if (Auth::guard('web')->user()->meta_access_token != null)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="on_instagram"
                                                    value="1" id="PlatformInstagram">
                                                <label class="form-check-label" for="PlatformInstagram">
                                                    <i class="fab fa-instagram"></i>
                                                </label>
                                            </div>
                                        @endif
                                    @endif
                                    @if (Auth::guard('web')->user()->canAny(['linkedin_text_post', 'linkedin_image_post', 'linkedin_video_post']))
                                        @if (Auth::guard('web')->user()->linkedin_access_token != null)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="on_linkedin"
                                                    value="1" id="PlatformLinkedIn">
                                                <label class="form-check-label" for="PlatformLinkedIn">
                                                    <i class="fab fa-linkedin-in"></i>
                                                </label>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-3 w-100">
                                    @if (Auth::guard('web')->user()->canAny(['meta_facebook_text_post', 'meta_facebook_image_post', 'meta_facebook_video_post']))
                                        @if (Auth::guard('web')->user()->meta_access_token != null)
                                            <div class="w-100 d-flex align-items-center gap-1 flex-column">
                                                <label for="page" class="form-label d-block w-100 mb-0">Facebook</label>
                                                <select name="facebook_page" class="d-block w-100 form-select text-black"
                                                    id="facebookPage">
                                                    <option value="">Select Page</option>
                                                </select>
                                            </div>
                                        @endif
                                    @endif
                                    @if (Auth::guard('web')->user()->canAny(['meta_instagram_image_post', 'meta_instagram_video_post']))
                                        @if (Auth::guard('web')->user()->meta_access_token != null)
                                            <div class="w-100 d-flex align-items-center gap-1 flex-column">
                                                <label for="page" class="form-label d-block w-100 mb-0">Instagram</label>
                                                <select name="instagram_account" class="d-block w-100 form-select text-black"
                                                    id="instagramPage">
                                                    <option value="">Select Page</option>
                                                </select>
                                            </div>
                                        @endif
                                    @endif
                                    @if (Auth::guard('web')->user()->canAny(['linkedin_text_post', 'linkedin_image_post', 'linkedin_video_post']))
                                        @if (Auth::guard('web')->user()->linkedin_access_token != null)
                                            <div class="w-100 d-flex align-items-center gap-1 flex-column">
                                                <label for="page" class="form-label d-block w-100 mb-0">LinkedIn</label>
                                                <select name="linkedin_organization"
                                                    class="d-block w-100 form-select text-black" id="linkedInPage">
                                                    <option value="">Select Page</option>
                                                </select>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div>
                                    <button type="submit" class="btn btn-custom" id="scheduleSaveBtn"><i
                                            class="fas fa-calendar-alt d-inline-block me-1"></i> Schedule</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan

        {{-- Repost Modal --}}
        @can('immediate_post')
            <div class="modal fade" id="repostModal" tabindex="-1" aria-labelledby="repostModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title" id="repostModalLabel">Repost</h5>
                            <button type="button" class="btn-close" style="filter: invert(1)" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="repostPostForm" enctype="multipart/form-data" method="POST">
                            @method('POST')
                            @csrf
                            <input type="hidden" name="post_id" id="postID">
                            <div class="modal-body d-flex align-items-stretch flex-column gap-3">
                                <div>
                                    <label for="postTitle" class="form-label">Title</label>
                                    <input class="input-tag-title d-block h-100 w-100 form-control" id="postTitle"
                                        name="title" placeholder="Enter your title here" required />
                                </div>
                                <div
                                    class="textarea-wrapper my-1 flex-grow-1 d-flex flex-column align-items-stretch justify-content-center">
                                    <label for="postDescription" class="form-label">Description</label>
                                    <textarea class="input-tag-description d-block h-100 w-100 form-control" id="postDescription" name="description"
                                        placeholder="Enter your post description"></textarea>
                                </div>

                                @if (Auth::guard('web')->user()->canAny([
                                            'meta_facebook_image_post',
                                            'meta_facebook_video_post',
                                            'meta_instagram_image_post',
                                            'meta_instagram_video_post',
                                            'linkedin_image_post',
                                            'linkedin_video_post',
                                        ]))
                                    <div>
                                        <label for="media" class="form-label">Media</label>
                                        <input type="file" class="form-control" id="media" name="media"
                                            accept="image/*">
                                    </div>
                                @endif
                                <div class="d-flex align-items-center gap-3 py-2">
                                    <div><strong>Platforms</strong></div>
                                    @if (Auth::guard('web')->user()->canAny(['meta_facebook_text_post', 'meta_facebook_image_post', 'meta_facebook_video_post']))
                                        @if (Auth::guard('web')->user()->meta_access_token != null)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="on_facebook"
                                                    value="1" id="PlatformFacebook">
                                                <label class="form-check-label" for="PlatformFacebook">
                                                    <i class="fab fa-facebook-f"></i>
                                                </label>
                                            </div>
                                        @endif
                                    @endif
                                    @if (Auth::guard('web')->user()->canAny(['meta_instagram_image_post', 'meta_instagram_video_post']))
                                        @if (Auth::guard('web')->user()->meta_access_token != null)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="on_instagram"
                                                    value="1" id="PlatformInstagram">
                                                <label class="form-check-label" for="PlatformInstagram">
                                                    <i class="fab fa-instagram"></i>
                                                </label>
                                            </div>
                                        @endif
                                    @endif
                                    @if (Auth::guard('web')->user()->canAny(['linkedin_text_post', 'linkedin_image_post', 'linkedin_video_post']))
                                        @if (Auth::guard('web')->user()->linkedin_access_token != null)
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="on_linkedin"
                                                    value="1" id="PlatformLinkedIn">
                                                <label class="form-check-label" for="PlatformLinkedIn">
                                                    <i class="fab fa-linkedin-in"></i>
                                                </label>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-3 w-100">
                                    @if (Auth::guard('web')->user()->canAny(['meta_facebook_text_post', 'meta_facebook_image_post', 'meta_facebook_video_post']))
                                        @if (Auth::guard('web')->user()->meta_access_token != null)
                                            <div class="w-100 d-flex align-items-center gap-1 flex-column">
                                                <label for="page" class="form-label d-block w-100 mb-0">Facebook</label>
                                                <select name="facebook_page" class="d-block w-100 form-select text-black"
                                                    id="facebookPage">
                                                    <option value="">Select Page</option>
                                                </select>
                                            </div>
                                        @endif
                                    @endif
                                    @if (Auth::guard('web')->user()->canAny(['meta_instagram_image_post', 'meta_instagram_video_post']))
                                        @if (Auth::guard('web')->user()->meta_access_token != null)
                                            <div class="w-100 d-flex align-items-center gap-1 flex-column">
                                                <label for="page" class="form-label d-block w-100 mb-0">Instagram</label>
                                                <select name="instagram_account" class="d-block w-100 form-select text-black"
                                                    id="instagramPage">
                                                    <option value="">Select Page</option>
                                                </select>
                                            </div>
                                        @endif
                                    @endif
                                    @if (Auth::guard('web')->user()->canAny(['linkedin_text_post', 'linkedin_image_post', 'linkedin_video_post']))
                                        @if (Auth::guard('web')->user()->linkedin_access_token != null)
                                            <div class="w-100 d-flex align-items-center gap-1 flex-column">
                                                <label for="page" class="form-label d-block w-100 mb-0">LinkedIn</label>
                                                <select name="linkedin_organization"
                                                    class="d-block w-100 form-select text-black" id="linkedInPage">
                                                    <option value="">Select Page</option>
                                                </select>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div>
                                    <button type="submit" class="btn btn-custom" id="repostSaveBtn"><i
                                            class="fas fa-share-square d-inline-block me-1"></i> Repost</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan

        {{ $dataSet->links('user.layouts.pagination') }}
    </section>
@endsection
