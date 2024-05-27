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
        <div class="h3 p-3 mb-4 bg-white text-center rounded-8">
            <h3 class="mb-0">Published Posts</h3>
        </div>

        <div class="filter-section d-flex align-items-center justify-content-end bg-white rounded-8">
            <div class="p-3">
                <label for="filterMonth">Filter by Month: </label>
                <select id="filterMonth">
                    <option value="">Select Month</option>
                    <option value="2022-05">May 2022</option>
                    <option value="2022-06">June 2022</option>
                    <option value="2022-07">July 2022</option>
                    <!-- Add more months as needed -->
                </select>
            </div>
        </div>

        <div class="table-wrapper bg-white rounded-8">
            <table>
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th class="text-left">Title</th>
                        <th class="text-center text-nowrap">Published Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" data-bs-toggle="modal" data-bs-target="#postDetail">1</td>
                        <td data-bs-toggle="modal" data-bs-target="#postDetail">
                            <p class="post-title mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Delectus
                                ullam provident maiores
                                deleniti ratione explicabo, debitis accusantium, ab omnis itaque tempora! Hic dicta
                                veritatis consectetur fuga sit, eaque officia minus.
                            </p>
                        </td>
                        <td data-bs-toggle="modal" data-bs-target="#postDetail">
                            <p class="post-date mb-0">May 23, 2022 18:20</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center" data-bs-toggle="modal" data-bs-target="#postDetail">2</td>
                        <td data-bs-toggle="modal" data-bs-target="#postDetail">
                            <p class="post-title mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Delectus
                                ullam provident maiores
                                deleniti ratione explicabo, debitis accusantium, ab omnis itaque tempora! Hic dicta
                                veritatis consectetur fuga sit, eaque officia minus.
                            </p>
                        </td>
                        <td data-bs-toggle="modal" data-bs-target="#postDetail">
                            <p class="post-date mb-0">May 23, 2022 18:20</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center" data-bs-toggle="modal" data-bs-target="#postDetail">3</td>
                        <td data-bs-toggle="modal" data-bs-target="#postDetail">
                            <p class="post-title mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Delectus
                                ullam provident maiores
                                deleniti ratione explicabo, debitis accusantium, ab omnis itaque tempora! Hic dicta
                                veritatis consectetur fuga sit, eaque officia minus.
                            </p>
                        </td>
                        <td data-bs-toggle="modal" data-bs-target="#postDetail">
                            <p class="post-date mb-0">May 23, 2022 18:20</p>
                        </td>
                    </tr>

                    <!-- Add more rows as needed -->
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="postDetail" tabindex="-1" aria-labelledby="postDetailLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="postDetailLabel">POST Detail Here</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img src="https://images.pexels.com/photos/23842679/pexels-photo-23842679/free-photo-of-a-woman-in-a-black-jacket-leaning-against-a-wall.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" class="img-fluid"
                             id="postImage">
                        <video controls class="w-100" style="display: none" id="postVideo">
                            <source src="#" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                 
                        <p class="m-3">

                            Description here...
                        </p>
                    </div>

                </div>
            </div>
        </div>


        <div class="pagination bg-white rounded-8">
            <ul class="pagination">
                <li class="disabled"><a href="#">&laquo;</a></li>
                <li><a href="#" class="active">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">4</a></li>
                <li><a href="#">5</a></li>
                <li><a href="#">6</a></li>
                <li><a href="#">7</a></li>
                <li><a href="#">8</a></li>
                <li><a href="#">9</a></li>
                <li><a href="#">10</a></li>
                <li><a href="#">...</a></li>
                <li><a href="#">20</a></li>
                <li><a href="#">&raquo;</a></li>
            </ul>
        </div>
    </section>
@endsection
