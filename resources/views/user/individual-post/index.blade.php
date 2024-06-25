{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Published Posts - Linkedin Self')

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
                        `;

                        if (response.data.scheduled_at != null) {
                            html +=
                                `<p class="modal-post-date mb-1"><strong>Created on:</strong> <span id="modalPostDate">${response.data.formatted_created_at}</span></p>`;
                            html +=
                                `<p class="modal-post-date mb-1"><strong>Published on:</strong> <span id="modalPostDate">${standardDateTimeFormat(convertUTCToLocalTime(response.data.scheduled_at + ' UTC'))}</span></p>`;
                        } else {
                            html +=
                                `<p class="modal-post-date mb-1"><strong>Published on:</strong> <span id="modalPostDate">${response.data.formatted_created_at}</span></p>`;
                        }

                        html += `
                                <div class="modal-post-description flex-grow-1 d-flex align-items-stretch flex-column"
                                    style="max-height: 200px; overflow-y: auto;">
                                    <p class="mb-0" style="position:sticky; top:0;background-color:#fff;padding: 10px 0px 5px;"><strong>Description:</strong></p> <span id="modalPostDescription">${response.data.description.replace(/\n/g, '<br>')}</span>
                                </div>
                                <input type="hidden" id="postDetailId" value="${response.data.id}"/>
                                <div class="py-2">
                                    <div class="mb-2 plaform-page-detail">
                                        <strong>Platforms:</strong>
                                    </div>
                        `;
                        html += `
                                <div class="d-flex flex-column gap-2">
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" style="pointer-events: none; display: none"
                                            id="facebook-post-detail" ${(response.data.on_facebook) ? 'checked' : ''}>
                        `;
                        if (response.data.on_facebook) html +=
                            `<i class="fab fa-facebook m-0"></i>`;
                        html += `
                                    </div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" style="pointer-events: none; display: none"
                                            id="instagram-post-detail" ${(response.data.on_instagram) ? 'checked' : ''}>
                        `;
                        if (response.data.on_instagram) html +=
                            `<i class="fab fa-instagram"></i>`;
                        html += `
                                    </div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="checkbox" style="pointer-events: none; display: none"
                                            id="linkedin-post-detail" ${(response.data.on_linkedin) ? 'checked' : ''}>
                        `;
                        if (response.data.on_linkedin) html +=
                            `<i class="fab fa-linkedin"></i>`;
                        html += `
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

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
                        url: `{{ route('user.individual-post.index') }}/${postId}/delete`,
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
            window.location.href = `{{ route('user.individual-post.index') }}/${id}/${action}`;
        }
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
                        <th class="text-left">Used</th>
                        <th class="text-nowrap">Created On</th>
                        <th class="text-nowrap">Published On</th>
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
                            <td>{{ $post->title }}</td>
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
                                <span class="badge p-1 rounded-circle bg-primary">{{ $post->post_count }}</span>
                            </td>
                            <td>
                                <p class="post-date mb-0">
                                    @if ($post->scheduled_at == null)
                                        -
                                    @else
                                        {{ date('d/M/Y', strtotime($post->created_at)) }}
                                        <span class="badge p-1 bg-warning rounded-2 text-dark">{{ date('h:i A', strtotime($post->created_at)) }}</span>
                                    @endif
                                </p>
                            </td>
                            <td>
                                <p class="post-date mb-0">
                                    @if ($post->scheduled_at != null)
                                        {{ date('d/M/Y', strtotime(convertUTCToLocalTime($post->scheduled_at, $timezone))) }}
                                        <span class="badge p-1 bg-warning rounded-2 text-dark">{{ date('h:i A', strtotime(convertUTCToLocalTime($post->scheduled_at, $timezone))) }}</span>
                                    @else
                                    {{ date('d/M/Y', strtotime($post->created_at)) }}
                                    <span class="badge p-1 bg-warning rounded-2 text-dark">{{ date('h:i A', strtotime($post->created_at)) }}</span>
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $dataSet->links('user.layouts.pagination') }}
    </section>

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
                                    class="fas fa-folder d-inline-block me-1"></i> Draft</a>
                        @endcan
                    </div>
                    <div>
                        @can('scheduled_post')
                            <button type="button" class="btn btn-custom" onclick="transferPostData('schedule')"><i
                                    class="fas fa-calendar-alt d-inline-block me-1"></i> Schedule</button>
                        @endcan
                        @can('re_post')
                            <button type="button" class="btn btn-custom" onclick="transferPostData('repost')"><i
                                    class="fas fa-share-square d-inline-block me-1"></i> Repost</button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
