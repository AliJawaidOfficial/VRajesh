@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Scheduled Posts')

{{-- Styles --}}
@section('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <style>
        #calendar {
            max-width: 100%;
            background-color: transparent;
            border-radius: 8px;
            padding: 20px;
        }

        #calendar a {
            color: #000;
            text-decoration: none;
        }

        .fc-header-toolbar {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
        }

        .fc-scrollgrid-section-header .fc-scroller-harness {
            padding: 0px 0px 0px;
        }

        .fc-scrollgrid,
        .fc-theme-standard td,
        .fc-theme-standard th {
            border: none !important;
        }

        .fc-col-header {
            background-color: #303030;
            border-top-right-radius: 8px;
            border-top-left-radius: 8px;
            border-bottom: 0px solid #e0e0e0 !important;
            width: 100% !important;
        }

        .fc-col-header th {
            padding: 10px 0px;
        }

        .fc-col-header th * {
            color: #fff !important;
        }

        .fc-scrollgrid-section td {
            background-color: #fff;
            border-bottom-right-radius: 8px;
            border-bottom-left-radius: 8px;
            border: 1px solid #e0e0e0 !important;
            position: relative;
            padding: 5px;
        }

        #calendar>.fc-view-harness-active>.fc-dayGridMonth-view>table>thead>tr.fc-scrollgrid-section-header>th>.fc-scroller-harness>.fc-scroller {
            overflow: hidden !important;
        }

        #calendar tbody table td {
            padding: 5px;
        }

        #calendar tbody table td:hover {
            background-color: #f0f0f0;
            border-radius: 0px;
        }

        .fc-event {
            font-size: 14px;
            padding: 5px;
            border-radius: 4px;
            cursor: pointer;
        }

        .fc-event:hover {
            background-color: #007bff !important;
            border-color: #007bff !important;
            color: white !important;
        }

        .fc-event-past {
            background-color: #d3d3d3 !important;
            border-color: #d3d3d3 !important;
        }

        .modal-header {
            background-color: #007bff;
            color: white;
        }

        .modal-body {
            font-size: 16px;
        }

        .modal-footer .btn {
            min-width: 100px;
        }

        .fc-toolbar-title {
            font-size: 24px;
            font-weight: bold;
        }

        .schedule-button {
            position: absolute;
            top: 5px;
            left: 5px;
            z-index: 10;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 4px 7px;
            cursor: pointer;
            font-size: 11px;
        }

        .schedule-button:hover {
            background-color: #0056b3;
        }

        .fc-event-main {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .fc-event-main i {
            font-size: 16px;
        }

        .fc-daygrid-day-events {
            display: flex;
            align-items: stretch;
            justify-content: center;
            flex-direction: column;
            gap: 10px;
        }
    </style>
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

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">

        <div id="calendar"></div>

        <!-- Modal for Post Details -->
        <div class="modal fade" id="postDetail" tabindex="-1" aria-labelledby="postDetailLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-6">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="postDetailLabel">Post Details</h5>
                        <button type="button" class="btn-close" style="filter: invert(1)" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex">
                        <div class="media-preview w-50">
                            <p class="text-center text-muted my-auto">No image/video published</p>
                            <!-- Placeholder for media preview -->
                        </div>
                        <div class="post-details d-flex flex-column align-items-stretch w-50">
                            <h4 class="modal-post-title">Title: <span id="modalPostTitle"></span></h4>
                            <p class="modal-post-date mb-1"><strong>Published on:</strong> <span
                                    id="modalPostDate"></span>
                            </p>
                            <div class="modal-post-description flex-grow-1" style="max-height: 200px; overflow-y: auto;">
                                <p class="mb-0"><strong>Description:</strong></p> <span id="modalPostDescription"></span>
                            </div>
                            <input type="hidden" id="postDetailId">
                            <div class="d-flex align-items-center gap-3 py-2">
                                <div>
                                    <strong>Platforms:</strong>
                                </div>
                                <div class="d-flex gap-2">
                                    <input type="checkbox" style="pointer-events: none;display: none"
                                        id="facebook-post-detail">
                                    <i class="fab fa-facebook"></i>
                                </div>
                                <div class="d-flex gap-2">
                                    <input type="checkbox" style="pointer-events: none;display: none"
                                        id="instagram-post-detail">
                                    <i class="fab fa-instagram"></i>
                                </div>
                                <div class="d-flex gap-2">
                                    <input type="checkbox" style="pointer-events: none;display: none"
                                        id="linkedin-post-detail">
                                    <i class="fab fa-linkedin"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <div>
                            <button type="button" class="btn btn-custom" onclick="deletePost()"><i class="fas fa-trash d-inline-block me-1"></i> Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


{{-- Vendor Scripts --}}
@section('scripts')
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@5.11.3/main.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@5.11.3/main.global.min.js'></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                customButtons: {
                    monthSelect: {
                        text: 'Month',
                        click: function() {
                            // Empty, just a placeholder
                        }
                    },
                    yearSelect: {
                        text: 'Year',
                        click: function() {
                            // Empty, just a placeholder
                        }
                    }
                },
                datesSet: function(info) {
                    fetchEvents(info.start, info.end);
                    addScheduleButtons();
                },
                eventDidMount: function(info) {
                    var eventDate = new Date(info.event.start);
                    var now = new Date();
                    if (eventDate < now) {
                        info.el.classList.add('fc-event-past');
                    }

                    info.el.addEventListener('click', function() {
                        fetchEventDetails(info.event.id);
                    });
                },
                eventContent: function(arg) {
                    let icons = '';
                    if (arg.event.extendedProps.on_facebook) icons +=
                        '<i class="fab fa-facebook-f"></i>';
                    if (arg.event.extendedProps.on_instagram) icons +=
                        '<i class="fab fa-instagram"></i>';
                    if (arg.event.extendedProps.on_linkedin) icons +=
                        '<i class="fab fa-linkedin-in"></i>';

                    return {
                        html: icons + ' -' + arg.event.title
                    };
                }
            });

            calendar.render();

            function addMonthYearSelects() {
                var headerToolbar = document.querySelector('.fc-toolbar-chunk:last-child');
                
                var monthSelect = document.createElement('select');
                monthSelect.id = 'monthSelect';
                monthSelect.className = 'form-select d-inline-block w-auto';
                monthSelect.innerHTML = `${Array.from({ length: 12 }, (_, i) => 
                    `<option value="${i}">${new Date(0, i).toLocaleString('default', { month: 'long' })}</option>`
                ).join('')}`;

                var yearSelect = document.createElement('select');
                yearSelect.id = 'yearSelect';
                yearSelect.className = 'form-select d-inline-block w-auto';
                yearSelect.innerHTML = `${Array.from({ length: 11 }, (_, i) => 
                    `<option value="${new Date().getFullYear() - 5 + i}">${new Date().getFullYear() - 5 + i}</option>`
                ).join('')}`;

                headerToolbar.appendChild(monthSelect);
                headerToolbar.appendChild(yearSelect);

                // Set default values to current month and year
                var currentDate = new Date();
                monthSelect.value = currentDate.getMonth(); // getMonth() returns 0-11
                yearSelect.value = currentDate.getFullYear();

                // Add event listener to update calendar on change
                document.addEventListener('change', function(event) {
                    if (event.target.id === 'monthSelect' || event.target.id === 'yearSelect') {
                        const month = document.getElementById('monthSelect').value;
                        const year = document.getElementById('yearSelect').value;
                        const date = new Date(year, month, 1);
                        calendar.gotoDate(date); // Assuming 'calendar' is your FullCalendar instance
                    }
                });
            }

            addMonthYearSelects();

            function fetchEvents(start, end) {
                const apiUrl =
                    `{{ route('user.post.scheduled.all') }}?start=${start.toISOString()}&end=${end.toISOString()}`;
                fetch(apiUrl)
                    .then(response => response.json())
                    .then(data => {
                        const events = data.map((post) => {
                            const eventDate = new Date(post.scheduled_at);
                            return {
                                id: post.id,
                                title: post.title,
                                start: eventDate.toISOString().split('T')[0],
                                description: post.description,
                                on_facebook: post.on_facebook,
                                on_instagram: post.on_instagram,
                                on_linkedin: post.on_linkedin
                            };
                        });
                        calendar.removeAllEvents();
                        calendar.addEventSource(events);
                    })
                    .catch(error => {
                        console.error('Error fetching events:', error);
                    });
            }

            function fetchEventDetails(eventId) {
                const apiUrl = `{{ route('user.post.index') }}/details/show/${eventId}`;
                fetch(apiUrl)
                    .then(response => response.json())
                    .then(response => {
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
                                        <p class="modal-post-date mb-1"><strong>Created on:</strong> <span id="modalPostDate">${response.data.formatted_created_at}</span></p>
                                        <p class="modal-post-date mb-1"><strong>Published on:</strong> <span id="modalPostDate">${standardDateTimeFormat(convertUTCToLocalTime(response.data.scheduled_at + ' UTC'))}</span></p>
                                        <div class="modal-post-description flex-grow-1 d-flex align-items-stretch flex-column"
                                            style="max-height: 200px; overflow-y: auto;">
                                            <p class="mb-0" style="position:sticky; top:0;background-color:#fff;padding: 10px 0px 5px;"><strong>Description:</strong></p> <span id="modalPostDescription">${response.data.description.replace(/\n/g, '<br>')}</span>
                                        </div>
                                        <input type="hidden" id="postDetailId" value="${response.data.id}"/>
                                        <div class="py-2">
                                            <div class="mb-2 plaform-page-detail">
                                                <strong>Platforms & Pages:</strong>
                                            </div>
                                    `;

                            html += `<div class="d-flex flex-column gap-2">
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="checkbox" style="pointer-events: none; display: none"
                                                id="facebook-post-detail" ${(response.data.on_facebook) ? 'checked' : ''}>
                                        `;
                            if (response.data.on_facebook) html +=
                                `<i class="fab fa-facebook m-0"></i><span class="m-0 ms-2">${response.data.facebook_page_name}</span>`;
                            html += `</div>
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="checkbox" style="pointer-events: none; display: none"
                                                id="instagram-post-detail" ${(response.data.on_instagram) ? 'checked' : ''}>
                                        `;
                            if (response.data.on_instagram) html +=
                                `<i class="fab fa-instagram"></i><span class="m-0 ms-2">${response.data.instagram_account_name}</span>`;
                            html += `</div>
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="checkbox" style="pointer-events: none; display: none"
                                                id="linkedin-post-detail" ${(response.data.on_linkedin) ? 'checked' : ''}>
                                        `;
                            if (response.data.on_linkedin) html +=
                                `<i class="fab fa-linkedin"></i><span class="m-0 ms-2">${response.data.linkedin_company_name}</span>`;
                            html += `</div>
                                    </div>
                                </div>
                            </div>`;

                            html +=
                                `<input type="hidden" id="facebook_page_access_token" value="${response.data.facebook_page_access_token}"/>
                                    <input type="hidden" id="facebook_page_id" value="${response.data.facebook_page_id}"/>
                                    <input type="hidden" id="facebook_page_name" value="${response.data.facebook_page_name}"/>
                                    <input type="hidden" id="instagram_account_id" value="${response.data.instagram_account_id}"/>
                                    <input type="hidden" id="instagram_account_name" value="${response.data.instagram_account_name}"/>
                                    <input type="hidden" id="linkedin_company_id" value="${response.data.linkedin_company_id}"/>
                                    <input type="hidden" id="linkedin_company_name" value="${response.data.linkedin_company_name}"/>`

                            $('#postDetail .modal-body').html(html);
                            $('#postDetail').modal('show');
                        } else {
                            toastr.error(response.error);
                        }

                        $('#postDetail').modal('show');
                    })
                    .catch(error => console.error('Error fetching event details:', error));
            }

            function addScheduleButtons() {
                var calendarCells = document.querySelectorAll('.fc-daygrid-day');
                var now = new Date();
                now.setHours(now.getHours(), now.getMinutes(), now.getSeconds(), 0); // Reset time part to midnight

                calendarCells.forEach(function(cell) {
                    var cellDate = new Date(cell.getAttribute('data-date'));
                    cellDate.setHours(now.getHours(), now.getMinutes(), now.getSeconds(), 0); // Reset time part to midnight

                    if (cellDate >= now) {
                        var button = document.createElement('button');
                        button.innerHTML = '<i class="fas fa-plus"></i>';
                        button.className = 'schedule-button';
                        button.addEventListener('click', function() {
                            let date = cellDate.toISOString().split('T')[0];
                            window.location.href = `{{ route('user.post.create') }}?schedule_date=${date}`;
                        });
                    
                        console.log(cellDate >= now);
                        cell.appendChild(button);
                    }
                });
            }

            document.querySelectorAll('.close, .btn-secondary').forEach(btn => {
                btn.addEventListener('click', function() {
                    $("#schedulePostModal").modal("hide");
                });
            });
        });

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
    </script>

@endsection
