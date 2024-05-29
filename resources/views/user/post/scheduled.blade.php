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
                    .then(data => {
                        html = `
                        <p><strong>Title:</strong> ${data.title}</p>
                        <p><strong>Description:</strong> ${data.description}</p>
                        <p><strong>Media:</strong> <img src="${assetUrl}${data.media}" class="w-100" style="max-width: 200px;" /></p>
                        <p><strong>Media Type:</strong> ${data.media_type}</p>
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
                            document.getElementById('modalBody').innerHTML = `
                            <p>Schedule post for ${cellDate.toDateString()}</p>
                        `;
                            var myModal = new bootstrap.Modal(document.getElementById(
                                'scheduleModal'));
                            myModal.show();
                        });

                        cell.appendChild(button);
                    }
                });
            }

            // Close modal using JavaScript
            document.querySelectorAll('.close, .btn-secondary').forEach(btn => {
                btn.addEventListener('click', function() {
                    var modal = bootstrap.Modal.getInstance(document.getElementById(
                        'scheduleModal'));
                    modal.hide();
                });
            });
        });
    </script>
@endsection

{{-- Content --}}
@section('content')
    <section class="main-content-wrapper d-flex flex-column">

        <div id="calendar"></div>

        <!-- Modal -->
        <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="scheduleModalLabel">Schedule Post</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="modalBody">Schedule post</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
