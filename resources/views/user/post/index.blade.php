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
        // document.getElementById('filterMonth').addEventListener('change', function() {
        //     // Fetch posts based on the selected month
        //     const month = this.value;
        //     console.log(`Filter by month: ${month}`);
        //     // Implement your filtering logic here
        // });

        function showPostDetail(id) {
            $.ajax({
                type: "GET",
                url: `{{ route('user.post.index') }}/details/${id}`,
                dataType: "json",
                success: function(response) {

                    if (response.status == 200) {
                        let html = ``;
                        let asset = `{{ asset('') }}`;

                        console.table(response.data);

                        if (response.data.media_type == 'image') html +=
                            `<img src="${asset}${response.data.media}" class="img-fluid w-100 rounded mb-1" />`;
                        if (response.data.media_type == 'video') html += `
                            <video controls class="w-100 rounded mb-1" id="postVideo">
                                <source src="${asset}${response.data.media}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        `;
                        if (response.data.description != null) html +=
                            `<p class="m-0 m-1">${response.data.description}</p>`;

                        $("#postDetailLabel").html(response.data.title);
                        $("#postDetail .modal-body").html(html);
                        $("#postDetail").modal("show");
                    } else {
                        toast.error(response.data.message);
                    }
                }
            });

        }
    </script>
@endsection

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">
        <div class="h3 p-3 mb-4 bg-white text-center rounded-8">
            <h3 class="mb-0">Published Posts</h3>
        </div>

        <form id="filterForm" class="filter-section d-flex align-items-center justify-content-end bg-white rounded-8">
            <div class="p-3">
                <label for="filterMonth">Filter by Month: </label>
                {{-- current month selected by default --}}
                <select id="filterMonth" name="month" onchange="this.form.submit()">
                    <option value="">All</option>
                    @foreach ($postMonths as $month)
                        <option value="{{ $month }}" {{ request()->get('month') == $month ? 'selected' : ''}}>
                            {{ \Carbon\Carbon::parse($month)->format('F Y') }}</option>
                    @endforeach
                </select>
            </div>
        </form>

        <div class="table-wrapper bg-white rounded-8">
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
                            <td colspan="3" class="text-center">Nothing to show you</td>
                        </tr>
                    @endif
                    @foreach ($dataSet as $post)
                        <tr onclick="showPostDetail({{ $post->id }})">
                            <td class="text-center">{{ ($loop->iteration != 10)? $dataSet->currentPage() - 1 . $loop->iteration : $dataSet->currentPage() * $loop->iteration}}</td>
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
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="postDetailLabel">POST Detail Here</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>

        {{ $dataSet->links('user.layouts.pagination') }}

    </section>
@endsection
