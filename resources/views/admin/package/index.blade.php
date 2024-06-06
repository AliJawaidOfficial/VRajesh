{{-- Layout --}}
@extends('admin.layouts.app')

{{-- Title --}}
@section('title', 'Packages')

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
    </style>
@endsection

{{-- Vendor Scripts --}}
@section('scripts')
    <script>
        function deleteRecord(id) {
            const record = $("#record_" + id);
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
                        url: `{{ route('admin.package.index') }}/${id}/delete`,
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "DELETE",
                        },
                        success: function(response) {
                            console.log(response);
                            if (response.status == 200) {
                                swalWithBootstrapButtons.fire({
                                    title: "Deleted!",
                                    text: "Package deleted successfully.",
                                    icon: "success",
                                    showConfirmButton: false,
                                    timer: 700
                                }).then(() => {
                                    record.fadeOut();
                                    setTimeout(() => {
                                        location.reload();
                                    }, 200);
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: response.error,
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    $("#postDetail").modal("show");
                }
            });
        }

        function updateVisibility(id, visibility) {
            $.ajax({
                type: "POST",
                url: `{{ route('admin.package.index') }}/${id}/visibility`,
                data: {
                    _token: "{{ csrf_token() }}",
                    _method: "POST",
                    visibility: visibility
                },
                success: function(response) {
                    console.log(response);
                    if (response.status == 200) {
                        Swal.fire({
                            title: "Success!",
                            text: response.message,
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
        }
    </script>
@endsection

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">
        <div class="h3 p-3 mb-4 bg-white text-center rounded-6">
            <h3 class="mb-0">Packages</h3>
        </div>

        <form class="card mb-2">
            <div class="card-body">
                <div class="row d-flex justify-content-between">
                    <div class="col">
                        <a href="{{ route('admin.package.create') }}" class="btn btn-dark">Add New</a>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                                placeholder="Search by name, email" aria-label="Search by name, email"
                                aria-describedby="button-addon2">
                            <button class="btn btn-dark" type="submit" id="button-addon2"><i
                                    class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="table-wrapper bg-white rounded-6">
            <table class="table-wrapper bg-white rounded-6">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($dataSet) == 0)
                        <tr>
                            <td colspan="4">
                                <p class="text-center m-0">Nothing to show</p>
                            </td>
                        </tr>
                    @else
                        @foreach ($dataSet as $data)
                            <tr id="record_{{ $data->id }}">
                                <td>{{ ($dataSet->currentPage() - 1) * $dataSet->perPage() + $loop->iteration }}</td>
                                <td>{{ ucWords($data->name) }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        @if ($data->is_visible == 1)
                                            <button type="button" onclick="updateVisibility({{ $data->id }}, 0)"
                                                class="btn btn-dark btn-sm">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                        @else
                                            <button type="button" onclick="updateVisibility({{ $data->id }}, 1)"
                                                class="btn btn-dark btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.package.edit', $data->id) }}" class="btn btn-dark btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" onclick="deleteRecord({{ $data->id }})"
                                            class="btn btn-dark btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        {{ $dataSet->links('admin.layouts.pagination') }}
    </section>
@endsection
