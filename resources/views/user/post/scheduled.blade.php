@extends('user.layouts.app')

{{-- Title --}}
@section('title', 'Scheduled Posts')

{{-- Styles --}}
@section('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        #calendar {
            max-width: 900px;
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
            padding: 5px 10px;
            cursor: pointer;
        }

        .schedule-button:hover {
            background-color: #0056b3;
        }

        .event-icons {
            display: inline-block;
            margin-right: 5px;
        }

        .event-icons i {
            font-size: 16px;
            margin-right: 2px;
        }
    </style>
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
                    left: 'title',
                    right: 'prev,today,next'
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
                }
            });

            calendar.render();

            function fetchEvents(start, end) {
                const apiUrl =
                    `{{ route('user.post.scheduled.all') }}?start=${start.toISOString()}&end=${end.toISOString()}`;

                fetch(apiUrl)
                    .then(response => response.json())
                    .then(data => {
                        const events = data.map((post, index) => {
                            const eventDate = new Date(post.scheduled_at);
                            let icons = '';

                            if (post.on_facebook) icons += '<i class="fab fa-facebook-f"></i>';
                            if (post.on_instagram) icons += '<i class="fab fa-instagram"></i>';
                            if (post.on_linkedin) icons += '<i class="fab fa-linkedin-in"></i>';

                            return {
                                id: post.id,
                                title: `${post.title}`,
                                start: eventDate.toISOString().split('T')[0],
                                description: post.description
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
                const assetUrl = '{{ asset('') }}';
                const apiUrl = `{{ route('user.post.index') }}/details/${eventId}`;

                fetch(apiUrl)
                    .then(response => response.json())
                    .then(response => {
                        html = `
                        <p><strong>Title:</strong> ${response.data.title}</p>
                        <p><strong>Description:</strong> ${response.data.description}</p>
                        <p><strong>Media:</strong> <img src="${assetUrl}${response.data.media}" class="w-100" style="max-width: 200px;" /></p>
                        <p><strong>Media Type:</strong> ${response.data.media_type}</p>
                    `;
                        document.getElementById('modalBody').innerHTML = html;
                        var myModal = new bootstrap.Modal(document.getElementById('scheduleModal'));
                        myModal.show();
                    })
                    .catch(error => console.error('Error fetching event details:', error));
            }

            function addScheduleButtons() {
                var calendarCells = document.querySelectorAll('.fc-daygrid-day');
                var now = new Date();

                calendarCells.forEach(function(cell) {
                    var cellDate = new Date(cell.getAttribute('data-date'));

                    if (cellDate >= now) {
                        var button = document.createElement('button');
                        button.innerHTML = '+';
                        button.className = 'schedule-button';
                        button.addEventListener('click', function() {
                            $("#schedulePostModal").modal("show");
                            $("#postDate").val(cellDate.toISOString().split('T')[0]);

                            $("#schedulePostModal").modal("show");
                        });

                        cell.appendChild(button);
                    }
                });
            }

            // Close modal using JavaScript
            document.querySelectorAll('.close, .btn-secondary').forEach(btn => {
                btn.addEventListener('click', function() {
                    $("#schedulePostModal").modal("hide");
                });
            });
        });


        // Schedule Form
        $("#schedulePostForm").submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: `{{ route('user.post.store') }}`,
                data: new FormData(this),
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $("#scheduleSaveBtn").attr('disabled', 'true');
                },
                success: function(response) {
                    if (response.status == 200) {
                        $("#schedulePostModal").modal('hide');
                        toastr.success("Post scheduled successfully");
                    } else {
                        toastr.error(response.error);
                    }

                    $("#scheduleSaveBtn").removeAttr('disabled');
                }
            });
        });
    </script>
@endsection

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">

        <div id="calendar"></div>

        {{-- Schedule Modal --}}
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
                        <div class="modal-body">
                            <div class="mb-3">
                                <input class="input-tag-title d-block h-100 w-100 form-control" id="postTitle"
                                    name="title" placeholder="Enter your title here" required />
                            </div>
                            <div class="textarea-wrapper mb-3">
                                <textarea class="input-tag-description d-block h-100 w-100 form-control" id="postDescription" name="description"
                                    placeholder="Enter your post description"></textarea>
                            </div>
                            <div class="mb-3 date-time-inputs">
                                <input type="date" class="form-control mb-3" id="postDate" name="schedule_date"
                                    required>
                                <input type="time" class="form-control" id="postTime" name="schedule_time" required>
                            </div>
                            <div class="mb-3">
                                <label for="postMedia" class="form-label">Media</label>
                                <input type="file" class="form-control" id="postMedia" name="media" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label>Platforms</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="on_facebook" value="1"
                                        id="postFacebook" @if (Auth::guard('web')->user()->meta_access_token == null) disabled @endif>
                                    <label class="form-check-label" for="postFacebook">
                                        <i class="fab fa-facebook-f"></i>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline mb-3">
                                    <input class="form-check-input" type="checkbox" name="on_instagram" value="1"
                                        id="postInstagram" @if (Auth::guard('web')->user()->linkedin_access_token == null) disabled @endif>
                                    <label class="form-check-label" for="postInstagram">
                                        <i class="fab fa-instagram"></i>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="on_linkedin" value="1"
                                        id="postLinkedin" @if (Auth::guard('web')->user()->meta_access_token == null) disabled @endif>
                                    <label class="form-check-label" for="postLinkedin">
                                        <i class="fab fa-linkedin-in"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div>
                                <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-custom" id="scheduleSaveBtn">Schedule</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
