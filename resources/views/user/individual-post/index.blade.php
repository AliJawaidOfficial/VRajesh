{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Published Posts')

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
            padding-bottom: 14px;
            padding-top: 0px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .modal-post-date {
            font-size: 14px;
            border-bottom: 1px solid #dee2e6;
            padding: 5px 0px;
        }

        .modal-post-description {
            font-size: 14px;
            border-bottom: 1px solid #dee2e6;
            padding: 5px 0px;
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
                url: `{{ route('user.individual.post.index') }}/details/${id}`,
                dataType: "json",
                success: function(response) {
                    if (response.status == 200) {
                        let html = '';
                        let asset = `{{ asset('') }}`;
                        let mediaType = response.data.media_type;
                        let mediaContent = response.data.media;

                        html += `
                            <div class="media-preview w-50 p-3">
                        `;

                        if (mediaType == 'image') {
                            html +=
                                `<img src="${asset}${mediaContent}" class="img-fluid w-100 rounded mb-1" />`;
                        } else if (mediaType == 'video') {
                            html += `<video controls class="w-100 rounded mb-1">
                                <source src="${asset}${mediaContent}" type="video/mp4">
                                Your browser does not support the video tag.
                              </video>`;
                        } else {
                            html += `<p class="text-center text-muted my-auto">No image/video published</p>`;
                        }
                        html += `
                            </div>
                            <div class="post-details d-flex flex-column align-items-stretch ms-3 w-50">
                                <h4 class="modal-post-title mb-2">Title: <span id="modalPostTitle">${response.data.title}</span></h4>
                                <p class="modal-post-date mb-1"><strong>Published on:</strong> <span id="modalPostDate">${standardDateTimeFormat(response.data.created_at)}</span></p>
                                <div class="modal-post-description flex-grow-1 d-flex align-items-stretch"
                                    style="max-height: 200px; overflow-y: auto;">
                                    <strong>Description:</strong> <span id="modalPostDescription">${response.data.description.replace(/\n/g, '<br>')}</span>
                                </div>
                                <input type="hidden" id="postDetailId" value="${response.data.id}"/>
                                <div class="py-2">
                                    <div class="mb-2">
                                        <strong>Platforms:</strong>
                                    </div>
                        `;
                        html += `<div class="d-flex flex-column gap-2">`;

                        if (response.data.on_facebook) {
                            html += `
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="checkbox" style="pointer-events: none; display: none"
                                        id="facebook-post-detail" ${(response.data.on_facebook) ? 'checked' : ''}>
                                    <i class="fab fa-facebook m-0"></i>
                                </div>
                            `;
                        } else {
                            html +=
                                `<input type="checkbox" style="pointer-events: none; display: none" id="facebook-post-detail" />`;
                        }

                        if (response.data.on_instagram) {
                            html += `
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="checkbox" style="pointer-events: none; display: none"
                                        id="instagram-post-detail" ${(response.data.on_facebook) ? 'checked' : ''}>
                                    <i class="fab fa-instagram m-0"></i>
                                </div>
                            `;
                        } else {
                            html +=
                                `<input type="checkbox" style="pointer-events: none; display: none" id="instagram-post-detail" />`;
                        }

                        if (response.data.on_linkedin) {
                            html += `
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="checkbox" style="pointer-events: none; display: none"
                                        id="linkedin-post-detail" ${(response.data.on_facebook) ? 'checked' : ''}>
                                    <i class="fab fa-linkedin m-0"></i>
                                </div>
                            `;
                        } else {
                            html +=
                                `<input type="checkbox" style="pointer-events: none; display: none" id="linkedin-post-detail" />`;
                        }

                        html += `
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
                        url: `{{ route('user.individual.post.index') }}/${postId}/delete`,
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
        // Function to transfer post data to the modal for editing, scheduling or reposting
        async function transferPostData(modalId) {
            const title = $('#modalPostTitle').text();
            const description = $('#modalPostDescription').html().replace(/<br>/g, '\n');

            $('#' + modalId + ' #postTitle').val(title);
            $('#' + modalId + ' #postDescription').val(description);
            $('#' + modalId + ' #PlatformFacebook').attr("checked", $("#facebook-post-detail").is(":checked"));
            $('#' + modalId + ' #PlatformInstagram').attr("checked", $("#instagram-post-detail").is(":checked"));
            $('#' + modalId + ' #PlatformLinkedIn').attr("checked", $("#linkedin-post-detail").is(":checked"));


            $('#' + modalId + ' #PlatformFacebook').change(async function() {
                console.log(this)
                if (this.checked) {
                    var facebook_pages = await getFacebookPages($('#' + modalId + ' #PlatformFacebook'));
                    $('#' + modalId + " #facebookPage").html(facebook_pages);
                } else {
                    $('#' + modalId + " #facebookPage").html('<option value="">Select</option>');
                }

            });

            $('#' + modalId + ' #PlatformInstagram').change(async function() {
                console.log(this)
                if (this.checked) {
                    var instagram_pages = await getInstagramAccounts($('#' + modalId +
                        ' #PlatformInstagram'));
                    $('#' + modalId + " #instagramPage").html(instagram_pages);
                } else {

                    $('#' + modalId + " #instagramPage").html('<option value="">Select</option>');
                }
            });

            $('#' + modalId + ' #PlatformLinkedIn').change(async function() {

                console.log(this)
                if (this.checked) {
                    var linkedin_organizations = await getLinkedInOrganizations($('#' + modalId +
                        ' #PlatformLinkedIn'));
                    $('#' + modalId + " #linkedInPage").html(linkedin_organizations);
                } else {

                    $('#' + modalId + " #linkedInPage").html('<option value="">Select</option>');
                }

            });

            $('#' + modalId + ' #postID').val($("#postDetailId").val());

            if ($("#facebook-post-detail").is(":checked")) {
                var facebook_pages = await getFacebookPages($("#facebook-post-detail"));
                $('#' + modalId + " #facebookPage").html(facebook_pages);
            } else {
                $('#' + modalId + " #facebookPage").html('<option value="">Select</option>');
            }
            if ($("#instagram-post-detail").is(":checked")) {
                var instagram_pages = await getInstagramAccounts($("#instagram-post-detail"));
                $('#' + modalId + " #instagramPage").html(instagram_pages);
            } else {

                $('#' + modalId + " #instagramPage").html('<option value="">Select</option>');
            }
            if ($("#linkedin-post-detail").is(":checked")) {
                var linkedin_organizations = await getLinkedInOrganizations($("#linkedin-post-detail"));
                $('#' + modalId + " #linkedInPage").html(linkedin_organizations);
            } else {

                $('#' + modalId + " #linkedInPage").html('<option value="">Select</option>');
            }


            $('#postDetail').modal('hide');
            $('#' + modalId).modal('show');
        }

        // @can('draft_post')
        //     // Draft Form
        //     $("#draftPostForm").submit(function(e) {
        //         e.preventDefault();
        //         $.ajax({
        //             type: "POST",
        //             url: `{{ route('user.post.draft.store.new') }}`,
        //             data: new FormData(this),
        //             processData: false,
        //             contentType: false,
        //             beforeSend: function() {
        //                 $("#draftSaveBtn").attr('disabled', 'true');
        //             },
        //             success: function(response) {
        //                 if (response.status == 200) {
        //                     $("#draftPostModal").modal('hide');
        //                     Swal.fire({
        //                         icon: 'success',
        //                         title: response.message,
        //                         showConfirmButton: false,
        //                         timer: 1500
        //                     });
        //                 } else {
        //                     toastr.error(response.error);
        //                 }

        //                 $("#draftSaveBtn").removeAttr('disabled');
        //             }
        //         });
        //     });
        // @endcan

        // @can('scheduled_post')
        //     // Schedule Form
        //     $("#schedulePostForm").submit(function(e) {
        //         e.preventDefault();
        //         $.ajax({
        //             type: "POST",
        //             url: `{{ route('user.post.new.store') }}`,
        //             data: new FormData(this),
        //             processData: false,
        //             contentType: false,
        //             beforeSend: function() {
        //                 $("#scheduleSaveBtn").attr('disabled', 'true');
        //             },
        //             success: function(response) {
        //                 if (response.status == 200) {
        //                     $("#schedulePostModal").modal('hide');
        //                     Swal.fire({
        //                         icon: 'success',
        //                         title: "Post scheduled successfully",
        //                         showConfirmButton: false,
        //                         timer: 1500
        //                     });
        //                 } else {
        //                     toastr.error(response.error);
        //                 }

        //                 $("#scheduleSaveBtn").removeAttr('disabled');
        //             }
        //         });
        //     });
        // @endcan

        // @can('re_post')
        //     // Repost Form
        //     $("#repostPostForm").submit(function(e) {
        //         e.preventDefault();
        //         $.ajax({
        //             type: "POST",
        //             url: `{{ route('user.post.new.store') }}`,
        //             data: new FormData(this),
        //             processData: false,
        //             contentType: false,
        //             beforeSend: function() {
        //                 $("#repostSaveBtn").attr('disabled', 'true');
        //             },
        //             success: function(response) {
        //                 if (response.status == 200) {
        //                     $("#repostModal").modal('hide');
        //                     Swal.fire({
        //                         icon: 'success',
        //                         title: "Post reposted successfully",
        //                         showConfirmButton: false,
        //                         timer: 1500
        //                     }).then(() => {
        //                         location.reload();
        //                     })
        //                 } else {
        //                     toastr.error(response.error);
        //                 }

        //                 $("#repostSaveBtn").removeAttr('disabled');
        //             }
        //         });
        //     });
        // @endcan
    </script>
@endsection


{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">
        <div class="h3 p-3 mb-4 bg-white text-center rounded-6">
            <h3 class="mb-0">Published Posts</h3>
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
                        <th class="text-nowrap">Published Date</th>
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
                                        </div>
                                    @endif
                                    @if ($post->on_instagram)
                                        <div
                                            class="badge p-1 bg-instagram rounded-2 text-light d-flex gap-1 align-items-start">
                                            <i class="fab fa-instagram"></i>
                                        </div>
                                    @endif
                                    @if ($post->on_linkedin)
                                        <div
                                            class="badge p-1 bg-linkedin rounded-2 text-light d-flex gap-1 align-items-start">
                                            <i class="fab fa-linkedin-in"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <p class="post-date mb-0">{{ standardDateTimeFormat($post->created_at) }}</p>
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
                            <button type="button" class="btn btn-custom" onclick="deletePost()">Delete</button>
                            {{-- @can('draft_post')
                                <button type="button" class="btn btn-custom"
                                    onclick="transferPostData('draftPostModal')">Draft</button>
                            @endcan --}}
                        </div>
                        <div>
                            {{-- @can('scheduled_post')
                                <button type="button" class="btn btn-custom"
                                    onclick="transferPostData('schedulePostModal')">Schedule</button>
                            @endcan --}}
                            {{-- @can('re_post')
                                <button type="button" class="btn btn-custom"
                                    onclick="transferPostData('repostModal')">Repost</button>
                            @endcan --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Draft Post Modal --}}
        {{-- @can('draft_post')
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
                                @if (Auth::guard('web')->user()->canAny(['meta_facebook_image_post', 'meta_facebook_video_post', 'meta_instagram_image_post', 'meta_instagram_video_post', 'linkedin_image_post', 'linkedin_video_post']))
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
        @endcan --}}

        {{-- Schedule Post Modal --}}
        {{-- @can('scheduled_post')
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
                                @if (Auth::guard('web')->user()->canAny(['meta_facebook_image_post', 'meta_facebook_video_post', 'meta_instagram_image_post', 'meta_instagram_video_post', 'linkedin_image_post', 'linkedin_video_post']))
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
                                    <button type="submit" class="btn btn-custom" id="scheduleSaveBtn">Schedule</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan --}}

        {{-- Repost Modal --}}
        {{-- @can('re_post')
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
                                @if (Auth::guard('web')->user()->canAny(['meta_facebook_image_post', 'meta_facebook_video_post', 'meta_instagram_image_post', 'meta_instagram_video_post', 'linkedin_image_post', 'linkedin_video_post']))
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
                                    <button type="submit" class="btn btn-custom" id="repostSaveBtn">Repost</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan --}}

        {{ $dataSet->links('user.layouts.pagination') }}
    </section>
@endsection