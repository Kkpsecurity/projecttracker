@extends('adminlte::page')

@section('title', 'Inspection Schedule Calendar - ProjectTracker Fresh')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-calendar-alt"></i> Inspection Schedule Calendar
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.hb837.index') }}">HB837 Management</a></li>
                <li class="breadcrumb-item active">Inspection Calendar</li>
            </ol>
        </div>
    </div>
@stop

@section('css')
    <!-- Basic calendar styles (fallback for FullCalendar CSS issues) -->
    <style>
        /* Basic FullCalendar styles - fallback when CDN fails */
        .fc {
            direction: ltr;
            text-align: left;
            font-family: 'Source Sans Pro', sans-serif;
            font-size: 14px;
        }

        .fc table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
        }

        .fc th,
        .fc td {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: top;
        }

        .fc .fc-header-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .fc-toolbar-chunk {
            display: flex;
            align-items: center;
        }

        .fc-button {
            background: #007bff;
            border: 1px solid #007bff;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            margin: 0 2px;
            cursor: pointer;
            font-size: 14px;
        }

        .fc-button:hover {
            background: #0056b3;
            border-color: #0056b3;
        }

        .fc-button:disabled {
            background: #6c757d;
            border-color: #6c757d;
            cursor: not-allowed;
        }

        .fc-event {
            border: 1px solid #007bff;
            background-color: #007bff;
            color: #fff;
            font-size: 12px;
            cursor: pointer;
            border-radius: 3px;
            padding: 2px 4px;
            margin: 1px 0;
        }

        .fc-event:hover {
            opacity: 0.8;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .fc-daygrid-day {
            background: #fff;
            min-height: 100px;
        }

        .fc-daygrid-day:hover {
            background: #f8f9fa;
        }

        .fc-day-today {
            background-color: #fff3cd !important;
        }

        .fc-day-past {
            background-color: #f8f9fa;
            color: #6c757d;
        }

        .fc-day-future {
            background-color: #fff;
        }

        .fc-daygrid-event {
            margin: 2px;
            padding: 2px 4px;
            border-radius: 3px;
        }

        /* Custom calendar styles */
        .simple-calendar {
            width: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 8px 8px 0 0;
        }

        .calendar-grid {
            padding: 20px;
        }

        .calendar-weekdays {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            margin-bottom: 1px;
        }

        .weekday {
            padding: 15px 5px;
            text-align: center;
            font-weight: bold;
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
        }

        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
        }

        .calendar-day {
            min-height: 120px;
            border: 1px solid #dee2e6;
            background: white;
            padding: 8px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .calendar-day:hover {
            background: #f8f9fa;
        }

        .calendar-day.other-month {
            background: #f8f9fa;
            color: #6c757d;
        }

        .calendar-day.today {
            background: linear-gradient(135deg, rgba(255,193,7,0.1), rgba(255,193,7,0.05));
            border: 2px solid #ffc107;
        }

        .day-number {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .day-events {
            margin-top: 5px;
        }

        .event-item {
            font-size: 11px;
            padding: 2px 4px;
            margin: 1px 0;
            border-radius: 3px;
            cursor: pointer;
            color: white;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .event-item:hover {
            opacity: 0.8;
        }

        .event-item.status-not-started {
            background: #6f42c1;
        }

        .event-item.status-in-progress {
            background: #fd7e14;
        }

        .event-item.status-in-review {
            background: #20c997;
        }

        .event-item.status-completed {
            background: #28a745;
        }

        .event-item.status-on-hold {
            background: #dc3545;
        }

        .event-item.status-cancelled {
            background: #6c757d;
        }
        .fc-event.status-not-started {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .fc-event.status-in-progress {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }

        .fc-event.status-in-review {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        .fc-event.status-completed {
            background-color: #28a745;
            border-color: #28a745;
        }

        .fc-event.status-cancelled {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .fc-title {
            font-weight: bold;
            padding: 0.5rem;
            text-align: center;
            font-size: 1.2rem;
        }

        /* Custom enhancements */
        .fc {
            font-size: 14px;
            font-family: 'Source Sans Pro', sans-serif;
        }

        .fc-event {
            cursor: pointer;
            border-radius: 4px;
            font-size: 12px;
            padding: 3px 6px;
            font-weight: 500;
            border-width: 2px !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }

        .fc-event:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 999;
        }

        .fc-event-title {
            font-weight: 600;
        }

        .fc-daygrid-event {
            margin-bottom: 2px;
        }

        .fc-toolbar {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .fc-toolbar-title {
            font-size: 1.6em !important;
            font-weight: 700;
            color: #495057;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .fc-button {
            font-size: 13px;
            padding: 6px 12px;
        }

        .fc-button-primary {
            background: linear-gradient(135deg, #007bff, #0056b3) !important;
            border-color: #0056b3 !important;
            box-shadow: 0 2px 4px rgba(0,123,255,0.3);
            transition: all 0.2s ease;
        }

        .fc-button-primary:hover {
            background: linear-gradient(135deg, #0056b3, #004085) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,123,255,0.4);
        }

        .fc-button-primary:not(:disabled):active,
        .fc-button-primary:not(:disabled).fc-button-active {
            background: linear-gradient(135deg, #004085, #002752) !important;
        }

        .fc-day-today {
            background: linear-gradient(135deg, rgba(255,193,7,0.1), rgba(255,193,7,0.05)) !important;
            border: 2px solid #ffc107 !important;
        }

        .fc-day-number {
            font-weight: 600;
            color: #495057;
        }

        .fc-day-today .fc-day-number {
            color: #856404;
            font-weight: 700;
        }

        .fc-daygrid-event {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .calendar-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.3;
        }

        .calendar-header > * {
            position: relative;
            z-index: 1;
        }

        .calendar-stats {
            display: flex;
            gap: 30px;
        }

        .stat-item {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 8px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
            display: block;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .stat-label {
            font-size: 12px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 5px;
        }

        .filter-card {
            margin-bottom: 20px;
        }

        .filter-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #e3e6f0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .filter-section:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }

        .status-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }

        .status-legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 10px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .status-legend-item:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .status-color {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .calendar-container {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e3e6f0;
            transition: all 0.3s ease;
        }

        .calendar-container:hover {
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.12);
        }

        .project-modal .modal-dialog {
            max-width: 800px;
        }

        .project-detail-row {
            margin-bottom: 10px;
        }

        .project-detail-label {
            font-weight: 600;
            color: #495057;
        }

        .status-badge {
            font-size: 12px;
            padding: 4px 8px;
        }
    </style>
@stop

@section('content')
    <!-- Calendar Header with Stats -->
    <div class="calendar-header">
        <div>
            <h3 class="mb-0">
                <i class="fas fa-calendar-check"></i>
                HB837 Inspection Schedule
            </h3>
            <p class="mb-0 mt-1">View and manage all scheduled property inspections</p>
        </div>
        <div class="calendar-stats" id="calendarStats">
            <div class="stat-item">
                <span class="stat-number" id="totalInspections">-</span>
                <span class="stat-label">Total Inspections</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" id="thisWeek">-</span>
                <span class="stat-label">This Week</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" id="nextWeek">-</span>
                <span class="stat-label">Next Week</span>
            </div>
        </div>
    </div>

    <!-- Filters and Legend -->
    <div class="filter-card">
        <div class="filter-section">
            <div class="row">
                <div class="col-md-4">
                    <label for="statusFilter" class="form-label">
                        <i class="fas fa-filter"></i> Filter by Status:
                    </label>
                    <select id="statusFilter" class="form-control">
                        <option value="all">All Statuses</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button id="todayBtn" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-calendar-day"></i> Today
                    </button>
                    <button id="refreshBtn" class="btn btn-outline-secondary btn-sm ml-2">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status Legend:</label>
                    <div class="status-legend">
                        <div class="status-legend-item">
                            <div class="status-color" style="background-color: #6f42c1;"></div>
                            <small>Not Started</small>
                        </div>
                        <div class="status-legend-item">
                            <div class="status-color" style="background-color: #fd7e14;"></div>
                            <small>In Progress</small>
                        </div>
                        <div class="status-legend-item">
                            <div class="status-color" style="background-color: #20c997;"></div>
                            <small>In Review</small>
                        </div>
                        <div class="status-legend-item">
                            <div class="status-color" style="background-color: #28a745;"></div>
                            <small>Completed</small>
                        </div>
                        <div class="status-legend-item">
                            <div class="status-color" style="background-color: #dc3545;"></div>
                            <small>On Hold</small>
                        </div>
                        <div class="status-legend-item">
                            <div class="status-color" style="background-color: #6c757d;"></div>
                            <small>Cancelled</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="calendar-container">
        <div id="calendar"></div>
    </div>

    <!-- Project Details Modal -->
    <div class="modal fade project-modal" id="projectModal" tabindex="-1" aria-labelledby="projectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="projectModalLabel">
                        <i class="fas fa-building"></i> Project Details
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="projectModalBody">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Loading project details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row w-100">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-outline-primary btn-block" id="editProjectBtn">
                                <i class="fas fa-edit"></i> Edit Project
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">
                                <i class="fas fa-times"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Update Modal -->
    <div class="modal fade" id="dateUpdateModal" tabindex="-1" aria-labelledby="dateUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="dateUpdateModalLabel">
                        <i class="fas fa-calendar-edit"></i> Update Inspection Date
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="dateUpdateForm">
                    <div class="modal-body">
                        <input type="hidden" id="updateProjectId">
                        <div class="form-group">
                            <label for="newInspectionDate">New Inspection Date:</label>
                            <input type="date" class="form-control" id="newInspectionDate" required>
                        </div>
                        <div class="form-group">
                            <label>Project:</label>
                            <p id="updateProjectName" class="form-control-plaintext"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Update Date
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        let currentProjectId = null;
        let currentEvents = [];
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();

        document.addEventListener('DOMContentLoaded', function() {
            initializeSimpleCalendar();
            loadStatusFilter();
            setupEventListeners();
            loadCalendarEvents();
        });

        function initializeSimpleCalendar() {
            const calendarEl = document.getElementById('calendar');
            calendarEl.innerHTML = `
                <div class="simple-calendar">
                    <div class="calendar-header">
                        <button class="btn btn-outline-primary btn-sm" id="prevMonth">
                            <i class="fas fa-chevron-left"></i> Previous
                        </button>
                        <h4 id="currentMonthYear"></h4>
                        <button class="btn btn-outline-primary btn-sm" id="nextMonth">
                            Next <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <div id="calendarGrid" class="calendar-grid"></div>
                </div>
            `;

            document.getElementById('prevMonth').addEventListener('click', () => {
                changeMonth(-1);
            });

            document.getElementById('nextMonth').addEventListener('click', () => {
                changeMonth(1);
            });

            renderCalendar();
        }

        function renderCalendar() {
            const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"];

            document.getElementById('currentMonthYear').textContent =
                `${monthNames[currentMonth]} ${currentYear}`;

            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            let calendarHtml = `
                <div class="calendar-weekdays">
                    <div class="weekday">Sun</div>
                    <div class="weekday">Mon</div>
                    <div class="weekday">Tue</div>
                    <div class="weekday">Wed</div>
                    <div class="weekday">Thu</div>
                    <div class="weekday">Fri</div>
                    <div class="weekday">Sat</div>
                </div>
                <div class="calendar-days">
            `;

            for (let i = 0; i < 42; i++) {
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);

                const isCurrentMonth = date.getMonth() === currentMonth;
                const isToday = isDateToday(date);
                const dayEvents = getEventsForDate(date);

                calendarHtml += `
                    <div class="calendar-day ${isCurrentMonth ? 'current-month' : 'other-month'} ${isToday ? 'today' : ''}"
                         data-date="${date.toISOString().split('T')[0]}">
                        <div class="day-number">${date.getDate()}</div>
                        <div class="day-events">
                            ${dayEvents.map(event => `
                                <div class="event-item status-${event.extendedProps.status}"
                                     onclick="showProjectDetails(${event.id})"
                                     title="${event.title} - ${event.extendedProps.address}">
                                    ${event.title.substring(0, 15)}${event.title.length > 15 ? '...' : ''}
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            calendarHtml += '</div>';
            document.getElementById('calendarGrid').innerHTML = calendarHtml;
        }

        function changeMonth(direction) {
            currentMonth += direction;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            } else if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
            loadCalendarEvents();
        }

        function isDateToday(date) {
            const today = new Date();
            return date.toDateString() === today.toDateString();
        }

        function getEventsForDate(date) {
            const dateStr = date.toISOString().split('T')[0];
            return currentEvents.filter(event => {
                const eventDate = new Date(event.start).toISOString().split('T')[0];
                return eventDate === dateStr;
            });
        }

        function loadCalendarEvents() {
            const status = document.getElementById('statusFilter').value;
            const startDate = new Date(currentYear, currentMonth, 1);
            const endDate = new Date(currentYear, currentMonth + 1, 0);

            fetch('{{ route("admin.hb837.inspection-calendar.events") }}?' + new URLSearchParams({
                status: status,
                _token: '{{ csrf_token() }}',
                start: startDate.toISOString(),
                end: endDate.toISOString()
            }))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(events => {
                currentEvents = events;
                renderCalendar();
                updateCalendarStats();
            })
            .catch(error => {
                console.error('Error loading events:', error);
                showError('Failed to load calendar events. Please refresh the page.');
            });
        }

        function setupEventListeners() {
            document.getElementById('statusFilter').addEventListener('change', function() {
                loadCalendarEvents();
            });

            document.getElementById('todayBtn').addEventListener('click', function() {
                const today = new Date();
                currentMonth = today.getMonth();
                currentYear = today.getFullYear();
                renderCalendar();
                loadCalendarEvents();
            });

            document.getElementById('refreshBtn').addEventListener('click', function() {
                loadCalendarEvents();
            });

            document.getElementById('dateUpdateForm').addEventListener('submit', function(e) {
                e.preventDefault();
                updateInspectionDate();
            });
        }

        function loadStatusFilter() {
            fetch('{{ route("admin.hb837.inspection-calendar.statuses") }}')
                .then(response => response.json())
                .then(statuses => {
                    const select = document.getElementById('statusFilter');
                    statuses.forEach(status => {
                        if (status) {
                            const option = new Option(
                                status.charAt(0).toUpperCase() + status.slice(1).replace('-', ' '),
                                status
                            );
                            select.add(option);
                        }
                    });
                })
                .catch(error => {
                    console.error('Error loading statuses:', error);
                });
        }

        function showProjectDetails(projectId) {
            currentProjectId = projectId;
            const modal = $('#projectModal');
            const modalBody = $('#projectModalBody');

            // Show loading state
            modalBody.html(`
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Loading project details...</p>
                </div>
            `);

            modal.modal('show');

            // Fetch project details
            fetch(`{{ route("admin.hb837.inspection-calendar.project", ":id") }}`.replace(':id', projectId))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayProjectDetails(data.project);
                    } else {
                        modalBody.html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                Error loading project details: ${data.message}
                            </div>
                        `);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            Failed to load project details. Please try again.
                        </div>
                    `);
                });
        }

        function displayProjectDetails(project) {
            const statusBadge = getStatusBadge(project.report_status);
            const contractingBadge = getStatusBadge(project.contracting_status, 'info');

            const modalBody = $('#projectModalBody');
            modalBody.html(`
                <div class="row">
                    <div class="col-md-6">
                        <div class="project-detail-row">
                            <span class="project-detail-label">Property Name:</span><br>
                            <span>${project.property_name || 'N/A'}</span>
                        </div>
                        <div class="project-detail-row">
                            <span class="project-detail-label">Address:</span><br>
                            <span>${project.address || 'N/A'}</span><br>
                            <small class="text-muted">${project.city || ''}, ${project.state || ''} ${project.zip || ''}</small>
                        </div>
                        <div class="project-detail-row">
                            <span class="project-detail-label">Inspection Date:</span><br>
                            <span>${project.scheduled_date_of_inspection || 'Not scheduled'}</span>
                            <button class="btn btn-sm btn-outline-warning ml-2" onclick="showDateUpdateModal()">
                                <i class="fas fa-edit"></i> Change
                            </button>
                        </div>
                        <div class="project-detail-row">
                            <span class="project-detail-label">Report Status:</span><br>
                            ${statusBadge}
                        </div>
                        <div class="project-detail-row">
                            <span class="project-detail-label">Contracting Status:</span><br>
                            ${contractingBadge}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="project-detail-row">
                            <span class="project-detail-label">Quoted Price:</span><br>
                            <span>${project.quoted_price ? '$' + Number(project.quoted_price).toLocaleString() : 'N/A'}</span>
                        </div>
                        <div class="project-detail-row">
                            <span class="project-detail-label">Units:</span><br>
                            <span>${project.units || 'N/A'}</span>
                        </div>
                        <div class="project-detail-row">
                            <span class="project-detail-label">Assigned Consultant:</span><br>
                            <span>${project.consultant ? project.consultant.name : 'Unassigned'}</span>
                        </div>
                        <div class="project-detail-row">
                            <span class="project-detail-label">Created By:</span><br>
                            <span>${project.created_by}</span>
                        </div>
                        <div class="project-detail-row">
                            <span class="project-detail-label">Last Updated:</span><br>
                            <small class="text-muted">${project.updated_at}</small>
                        </div>
                    </div>
                </div>
                ${project.notes ? `
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="project-detail-row">
                                <span class="project-detail-label">Notes:</span><br>
                                <div class="bg-light p-2 rounded">
                                    ${project.notes}
                                </div>
                            </div>
                        </div>
                    </div>
                ` : ''}
            `);

            // Update edit button
            document.getElementById('editProjectBtn').onclick = function() {
                window.location.href = `{{ route("admin.hb837.edit", ":id") }}`.replace(':id', project.id);
            };
        }

        function getStatusBadge(status, type = 'secondary') {
            if (!status) return '<span class="badge badge-secondary">N/A</span>';

            const statusColors = {
                'not-started': 'secondary',
                'in-progress': 'warning',
                'completed': 'success',
                'on-hold': 'danger',
                'cancelled': 'dark',
                'quoted': 'info',
                'contracted': 'success'
            };

            const color = statusColors[status] || type;
            const label = status.charAt(0).toUpperCase() + status.slice(1).replace('-', ' ');

            return `<span class="badge badge-${color} status-badge">${label}</span>`;
        }

        function showDateUpdateModal() {
            document.getElementById('updateProjectId').value = currentProjectId;

            // Get current project data
            const event = currentEvents.find(e => e.id == currentProjectId);

            if (event) {
                document.getElementById('updateProjectName').textContent = event.title;
                if (event.start) {
                    document.getElementById('newInspectionDate').value = new Date(event.start).toISOString().split('T')[0];
                }
            }

            $('#dateUpdateModal').modal('show');
        }

        function updateInspectionDate() {
            const projectId = document.getElementById('updateProjectId').value;
            const newDate = document.getElementById('newInspectionDate').value;

            fetch(`{{ route("admin.hb837.inspection-calendar.update-date", ":id") }}`.replace(':id', projectId), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    scheduled_date_of_inspection: newDate
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#dateUpdateModal').modal('hide');
                    loadCalendarEvents();
                    alert('Inspection date updated successfully!');
                } else {
                    alert('Error updating date: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update inspection date. Please try again.');
            });
        }

        function showError(message) {
            alert('Error: ' + message);
        }

        function updateCalendarStats() {
            // Calculate stats from current events
            const now = new Date();
            const weekStart = new Date(now);
            weekStart.setDate(now.getDate() - now.getDay());
            const weekEnd = new Date(weekStart);
            weekEnd.setDate(weekStart.getDate() + 6);
            const nextWeekStart = new Date(weekEnd);
            nextWeekStart.setDate(weekEnd.getDate() + 1);
            const nextWeekEnd = new Date(nextWeekStart);
            nextWeekEnd.setDate(nextWeekStart.getDate() + 6);

            const thisWeekCount = currentEvents.filter(event => {
                const eventDate = new Date(event.start);
                return eventDate >= weekStart && eventDate <= weekEnd;
            }).length;

            const nextWeekCount = currentEvents.filter(event => {
                const eventDate = new Date(event.start);
                return eventDate >= nextWeekStart && eventDate <= nextWeekEnd;
            }).length;

            // Update stats with animation
            animateCounter('totalInspections', currentEvents.length);
            animateCounter('thisWeek', thisWeekCount);
            animateCounter('nextWeek', nextWeekCount);
        }

        function animateCounter(elementId, targetValue) {
            const element = document.getElementById(elementId);
            const startValue = parseInt(element.textContent) || 0;
            const duration = 800;
            const startTime = performance.now();

            function updateCounter(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const currentValue = Math.floor(startValue + (targetValue - startValue) * progress);

                element.textContent = currentValue;

                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                }
            }

            requestAnimationFrame(updateCounter);
        }
    </script>
@stop
