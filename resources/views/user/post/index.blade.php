{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Dashboard')

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
    </style>
@endsection

{{-- Vendor Scripts --}}
<script>
    // Function to show the post detail in a modal
    function showPostDetail(id) {
        $.ajax({
            type: "GET",
            url: `{{ route('user.post.index') }}/details/${id}`,
            dataType: "json",
            success: function(response) {
                if (response.status == 200) {
                    let mediaHtml = '';
                    let asset = `{{ asset('') }}`;
                    let mediaType = response.data.media_type;
                    let mediaContent = response.data.media;

                    if (mediaType == 'image') {
                        mediaHtml +=
                            `<img src="${asset}${mediaContent}" class="img-fluid w-100 rounded mb-1" />`;
                    } else if (mediaType == 'video') {
                        mediaHtml += `<video controls class="w-100 rounded mb-1">
                                <source src="${asset}${mediaContent}" type="video/mp4">
                                Your browser does not support the video tag.
                              </video>`;
                    } else {
                        mediaHtml +=
                            `<p class="text-center text-muted my-auto">No image/video published</p>`;
                    }

                    $('#postDetail .media-preview').html(mediaHtml);
                    $('#modalPostTitle').text(response.data.title);
                    $('#modalPostDate').text(response.data.created_at);
                    $('#modalPostDescription').html(response.data.description.replace(/\n/g, '<br>'));
                    $('#postDetail').modal('show');
                } else {
                    toast.error(response.data.message);
                }
            }
        });
    }

    // Function to transfer post data to the modal for editing, scheduling or reposting
    // Function to transfer post data to the modal for editing, scheduling, or reposting
    function transferPostData(modalId) {
        const title = $('#modalPostTitle').text();
        const description = $('#modalPostDescription').html().replace(/<br>/g, '\n');

        
        console.log($('#postDetail .platform-icons .fa-instagram'));

        // Extract platform information from the post detail modal
        const platforms = [];
        if ($('#postDetail .platform-icons .fa-facebook').length) {
            platforms.push('facebook');
        }
        if ($('#postDetail .platform-icons .fa-instagram').length) {
            platforms.push('instagram');
        }
        if ($('#postDetail .platform-icons .fa-linkedin-in').length) {
            platforms.push('linkedin');
        }

        // Set the platform checkboxes in the target modal
        $('#' + modalId + ' #draftPlatformFacebook').prop('checked', platforms.includes('facebook'));
        $('#' + modalId + ' #draftPlatformInstagram').prop('checked', platforms.includes('instagram'));
        $('#' + modalId + ' #draftPlatformLinkedIn').prop('checked', platforms.includes('linkedin'));

        $('#' + modalId + ' #schedulePlatformFacebook').prop('checked', platforms.includes('facebook'));
        $('#' + modalId + ' #schedulePlatformInstagram').prop('checked', platforms.includes('instagram'));
        $('#' + modalId + ' #schedulePlatformLinkedIn').prop('checked', platforms.includes('linkedin'));

        $('#' + modalId + ' #repostPlatformFacebook').prop('checked', platforms.includes('facebook'));
        $('#' + modalId + ' #repostPlatformInstagram').prop('checked', platforms.includes('instagram'));
        $('#' + modalId + ' #repostPlatformLinkedIn').prop('checked', platforms.includes('linkedin'));

        $('#' + modalId + ' #postTitle').val(title);
        $('#' + modalId + ' #postDescription').val(description);

        $('#postDetail').modal('hide');
        setTimeout(function() {
            $('#' + modalId).modal('show');
        }, 500);
    }
</script>


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
                                {{ $loop->iteration != 10 ? $dataSet->currentPage() - 1 . $loop->iteration : $dataSet->currentPage() * $loop->iteration }}
                            </td>
                            <td>
                                <p class="post-title mb-0">{{ $post->title }}</p>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if ($post->on_facebook)
                                        <i class="fab fa-facebook"></i>
                                    @endif
                                    @if ($post->on_instagram)
                                        <i class="fab fa-instagram"></i>
                                    @endif
                                    @if ($post->on_linkedin)
                                        <i class="fab fa-linkedin-in"></i>
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
                    <div class="modal-body d-flex">
                        <div class="media-preview w-50 p-3">
                            <p class="text-center text-muted my-auto">No image/video published</p>
                            {{-- Placeholder for media preview --}}
                        </div>
                        <div class="post-details ms-3 w-50"">
                            <h4 class="modal-post-title mb-2">Title: <span id="modalPostTitle"></span></h4>
                            <p class="modal-post-date mb-1"><strong>Published on:</strong> <span id="modalPostDate"></span>
                            </p>
                            <div class="modal-post-description" style="max-height: 200px; overflow-y: auto;">
                                <strong>Description:</strong> <span id="modalPostDescription"></span>
                            </div>


                            
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <div>
                            <button type="button" class="btn btn-custom"
                                onclick="transferPostData('draftPostModal')">Draft</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-custom"
                                onclick="transferPostData('schedulePostModal')">Schedule</button>
                            <button type="button" class="btn btn-custom"
                                onclick="transferPostData('repostModal')">Repost</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Draft Post Modal --}}
        <div class="modal fade" id="draftPostModal" tabindex="-1" aria-labelledby="editPostModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="editPostModalLabel">Draft Post</h5>
                        <button type="button" class="btn-close" style="filter: invert(1)" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="editPostForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <input class="input-tag-title d-block h-100 w-100 form-control" id="postTitle"
                                    name="title" placeholder="Enter your title here" required />
                            </div>
                            <div class="textarea-wrapper my-1">
                                <textarea class="input-tag-description d-block h-100 w-100 form-control" id="postDescription" name="description"
                                    placeholder="Enter your post description"></textarea>
                            </div>
                            <div class="mb-3">
                                <label>Platforms</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="draftPlatformFacebook">
                                    <label class="form-check-label" for="draftPlatformFacebook">
                                        <i class="fab fa-facebook-f"></i>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="draftPlatformInstagram">
                                    <label class="form-check-label" for="draftPlatformInstagram">
                                        <i class="fab fa-instagram"></i>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="draftPlatformLinkedIn">
                                    <label class="form-check-label" for="draftPlatformLinkedIn">
                                        <i class="fab fa-linkedin-in"></i>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="editPostImage" class="form-label">Upload Image</label>
                                <input type="file" class="form-control" id="editPostImage" name="image"
                                    accept="image/*">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-custom" id="editSaveButton">Draft Post</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Schedule Post Modal --}}
        <div class="modal fade" id="schedulePostModal" tabindex="-1" aria-labelledby="schedulePostModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="schedulePostModalLabel">Schedule Post</h5>
                        <button type="button" class="btn-close" style="filter: invert(1)" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="schedulePostForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <input class="input-tag-title d-block h-100 w-100 form-control" id="postTitle"
                                    name="title" placeholder="Enter your title here" required />
                            </div>
                            <div class="textarea-wrapper my-1">
                                <textarea class="input-tag-description d-block h-100 w-100 form-control" id="postDescription" name="description"
                                    placeholder="Enter your post description"></textarea>
                            </div>
                            <div class="mb-3">
                                <label>Platforms</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="schedulePlatformFacebook">
                                    <label class="form-check-label" for="schedulePlatformFacebook">
                                        <i class="fab fa-facebook-f"></i>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="schedulePlatformInstagram">
                                    <label class="form-check-label" for="schedulePlatformInstagram">
                                        <i class="fab fa-instagram"></i>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="schedulePlatformLinkedIn">
                                    <label class="form-check-label" for="schedulePlatformLinkedIn">
                                        <i class="fab fa-linkedin-in"></i>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3 date-time-inputs">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="schedulePostToggle"
                                        onchange="toggleScheduleInputs('schedulePostModal')">
                                    <label class="form-check-label" for="schedulePostToggle">Schedule Post</label>
                                </div>
                                <input type="date" class="form-control mb-2" id="schedulePostDate"
                                    name="schedule_date" disabled required>
                                <input type="time" class="form-control" id="schedulePostTime" name="schedule_time"
                                    disabled required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-custom" id="schedulePostSaveButton"
                                    disabled>Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Repost Modal --}}
        <div class="modal fade" id="repostModal" tabindex="-1" aria-labelledby="repostModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="repostModalLabel">Repost</h5>
                        <button type="button" class="btn-close" style="filter: invert(1)" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="repostForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <input class="input-tag-title d-block h-100 w-100 form-control" id="postTitle"
                                    name="title" placeholder="Enter your title here" required />
                            </div>
                            <div class="textarea-wrapper my-1">
                                <textarea class="input-tag-description d-block h-100 w-100 form-control" id="postDescription" name="description"
                                    placeholder="Enter your post description"></textarea>
                            </div>
                            <div class="mb-3">
                                <label>Platforms</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="repostPlatformFacebook">
                                    <label class="form-check-label" for="repostPlatformFacebook">
                                        <i class="fab fa-facebook-f"></i>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="repostPlatformInstagram">
                                    <label class="form-check-label" for="repostPlatformInstagram">
                                        <i class="fab fa-instagram"></i>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        id="repostPlatformLinkedIn">
                                    <label class="form-check-label" for="repostPlatformLinkedIn">
                                        <i class="fab fa-linkedin-in"></i>
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3 date-time-inputs">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="repostToggle"
                                        onchange="toggleScheduleInputs('repostModal')">
                                    <label class="form-check-label" for="repostToggle">Schedule Post</label>
                                </div>
                                <input type="date" class="form-control mb-2" id="repostDate" name="schedule_date"
                                    disabled required>
                                <input type="time" class="form-control" id="repostTime" name="schedule_time" disabled
                                    required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-custom" id="repostSaveButton"
                                    disabled>Repost</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{ $dataSet->links('user.layouts.pagination') }}
    </section>
@endsection
