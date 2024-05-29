{{-- Layout --}}
@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Dashboard')

{{-- Styles --}}
@section('styles')
    <style>
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
    </style>
@endsection

{{-- Vendor Scripts --}}
@section('scripts')
    <script>
        document.getElementById('filterMonth').addEventListener('change', function() {
            // Fetch posts based on the selected month
            const month = this.value;
            console.log(`Filter by month: ${month}`);
            // Implement your filtering logic here
        });
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
                        <th class="text-nowrap">Saved On</th>
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

        <div class="modal fade" id="postDetail" tabindex="-1" aria-labelledby="postDetailLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="postDetailLabel">POST Detail Here</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="row align-items-stretch mt-2">
                        <div class="col-md-8">
                            <div class="h3 p-3 mb-4 bg-white text-center rounded-6">
                                <h3>Create Post</h3>
                            </div>

                            <form id="postForm" class="p-4 bg-white rounded-6" method="POST"
                                enctype="multipart/form-data">
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
                                                <input type="checkbox" name="on_facebook" value="1"
                                                    data-bs-toggle="facebook-post" class="form-check-input toggle-post" />
                                                <span class="d-inline-block ms-1">Facebook</span>
                                            </label>

                                            <label class="d-inline-block">
                                                <input type="checkbox" name="on_linkedin" value="1"
                                                    data-bs-toggle="linkedin-post" class="form-check-input toggle-post" />
                                                <span class="d-inline-block ms-1">LinkedIn</span>
                                            </label>

                                            <label class="d-inline-block">
                                                <input type="checkbox" name="on_instagram" value="1"
                                                    data-bs-toggle="instagram-post" class="form-check-input toggle-post" />
                                                <span class="d-inline-block ms-1">Instagram</span>
                                            </label>
                                        </div>

                                        <div>
                                            <label for="mediaInput" class="btn btn-transparent text-primary"><i
                                                    class="fas fa-paperclip" style="font-size: 20px"></i></label>
                                            <input class="d-block w-100 form-control d-none" type="file" name="media"
                                                accept="video/*, image/*" id="mediaInput" />
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mt-2 gap-4">
                                    <div>
                                        <button type="button" name="draft"
                                            class="btn btn-secondary btn-custom btn-custom-secondary">Save as
                                            Draft</button>
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
                            <div class="h3 p-3 mb-4 bg-white text-center rounded-6">
                                <h3>Post Preview</h3>
                            </div>
                            <div class="w-100 bg-white p-4 rounded-6 overflow-scroll d-flex flex-column gap-3"
                                id="postPreview">
                                {{-- We will list facebook, instagram and linkedin post will look like --}}
                                <div class="card rounded" id="facebook-post" style="display: none">
                                    <div
                                        class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-2">
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/50" alt="Profile Picture"
                                                class="rounded-circle">
                                            <div class="ms-2">
                                                <h6 class="mb-0 line-clap" style="-webkit-line-clamp: 1;">DUET Media - DM
                                                </h6>
                                                <small class="text-muted">1h · <img
                                                        src="{{ asset('assets/images/icons/globe.png') }}" width="16"
                                                        alt=""></small>
                                            </div>
                                        </div>

                                        <div>
                                            <img src="{{ asset('assets/images/icons/three-dot-icons.png') }}"
                                                width="25" style="min-width: 25px" alt="">
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="px-4 mb-3 post-description">
                                            <p class="mb-0"></p>
                                        </div>

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
                                <div class="card rounded mt-4" id="linkedin-post" style="display: none">
                                    <div
                                        class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-2">
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/50" alt="Profile Picture"
                                                class="rounded-circle">
                                            <div class="ms-2">
                                                <h6 class="mb-0">Hania Mehdi</h6>
                                                <small class="text-muted line-clap" style="-webkit-line-clamp: 1;">HR
                                                    Professional |
                                                    Head Hunter | Technical & Non Technical Recruiter |
                                                    International...</small>
                                                <div><small class="text-muted">25m · Edited</small></div>
                                            </div>
                                        </div>
                                        <div style="width: fit-content">
                                            <img src="{{ asset('assets/images/icons/three-dot-icons.png') }}"
                                                style="min-width: 25px" width="25" alt="">
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="px-4 mb-3 post-description">
                                            <p class="mb-0"></p>
                                        </div>
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

                                <div class="card rounded mt-4" id="instagram-post" style="display: none">
                                    <div
                                        class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between pt-2">
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/50" alt="Profile Picture"
                                                class="rounded-circle">
                                            <div class="ms-2">
                                                <h6 class="mb-0">karuneshtalwar</h6>
                                                <small class="text-muted">Original audio</small>
                                            </div>
                                        </div>
                                        <div style="width: fit-content">
                                            <img src="{{ asset('assets/images/icons/three-dot-icons.png') }}"
                                                style="min-width: 25px" width="25" alt="">
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
                </div>
            </div>
        </div>


        {{ $dataSet->links('user.layouts.pagination') }}
    </section>
@endsection
