@extends('layouts.app')

@section('title', 'Grooming Reports')

@section('content')

<style>

    .reports-page {
        min-height: calc(100vh - 70px);
        background:
            linear-gradient(
                135deg,
                #e8f4ff,
                #f7fbff,
                #dff3f0
            );
        padding: 50px 40px;
    }

    .reports-container {
        max-width: 1100px;
        margin: 0 auto;
    }

    .reports-header {
        margin-bottom: 30px;
    }

    .reports-header h1 {
        color: #285b94;
        font-size: 32px;
        margin-bottom: 8px;
    }

    .reports-header p {
        color: #666;
        font-size: 16px;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #e8f8ee;
        color: #218838;
        border: 1px solid #b8e6c8;
    }

    .alert-error {
        background: #fdecec;
        color: #c62828;
        border: 1px solid #f5b5b5;
    }

    .reports-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow:
            0 10px 30px rgba(0, 0, 0, 0.10);
        overflow-x: auto;
    }

    .reports-table {
        width: 100%;
        border-collapse: collapse;
    }

    .reports-table th {
        background: #f0f6fc;
        color: #285b94;
        padding: 14px;
        text-align: left;
        white-space: nowrap;
    }

    .reports-table td {
        padding: 14px;
        border-bottom: 1px solid #eee;
        color: #555;
        vertical-align: middle;
    }

    .reports-table tr:hover {
        background: #fafafa;
    }

    .status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-confirmed {
        background: #d4edda;
        color: #155724;
    }

    .status-completed {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }

    .action-button {
        display: inline-block;
        padding: 8px 14px;
        border-radius: 7px;
        text-decoration: none;
        font-weight: bold;
        font-size: 13px;
        margin-right: 5px;
    }

    .create-button {
        background: #3478c9;
        color: white;
    }

    .create-button:hover {
        background: #285b94;
    }

    .edit-button {
        background: #ff6b81;
        color: white;
    }

    .edit-button:hover {
        background: #ff4f68;
    }

    .completed-label {
        color: #218838;
        font-weight: bold;
    }

    .not-available-label {
        color: #888;
        font-weight: bold;
    }

    .no-reports {
        background: white;
        border-radius: 15px;
        padding: 60px 30px;
        text-align: center;
        box-shadow:
            0 10px 30px rgba(0, 0, 0, 0.10);
    }

    .no-reports .icon {
        font-size: 50px;
        margin-bottom: 15px;
    }

    .no-reports h2 {
        color: #333;
        margin-bottom: 10px;
    }

    .no-reports p {
        color: #777;
    }

    @media (max-width: 700px) {

        .reports-page {
            padding: 35px 20px;
        }

        .reports-header h1 {
            font-size: 27px;
        }

        .reports-card {
            padding: 15px;
        }

        .reports-table th,
        .reports-table td {
            padding: 10px;
            font-size: 13px;
        }

    }

</style>


<div class="reports-page">

    <div class="reports-container">

        <!-- =========================
             HEADER
        ========================== -->

        <div class="reports-header">

            <h1>
                Grooming Reports
            </h1>

            @if(strtoupper(session('role', '')) === 'CUSTOMER')

                <p>
                    View grooming reports for your pets.
                </p>

            @elseif(strtoupper(session('role', '')) === 'GROOMER')

                <p>
                    View your assigned appointments and manage grooming reports.
                </p>

            @endif

        </div>


        <!-- =========================
             SUCCESS MESSAGE
        ========================== -->

        @if(session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif


        <!-- =========================
             ERROR MESSAGE
        ========================== -->

        @if(session('error'))

            <div class="alert alert-error">
                {{ session('error') }}
            </div>

        @endif


        <!-- =========================
             APPOINTMENTS
        ========================== -->

        @if($appointments->count() > 0)

            <div class="reports-card">

                <table class="reports-table">

                    <thead>

                        <tr>

                            <th>
                                Appointment ID
                            </th>

                            <th>
                                Pet
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Time
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Report
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($appointments as $appointment)

                            @php

                                $report = $reports->get(
                                    $appointment->Appointment_ID
                                );

                                $status = strtolower(
                                    $appointment->Status ?? 'pending'
                                );

                            @endphp


                            <tr>

                                <!-- =========================
                                     APPOINTMENT ID
                                ========================== -->

                                <td>
                                    #{{ $appointment->Appointment_ID }}
                                </td>


                                <!-- =========================
                                     PET
                                ========================== -->

                                <td>

                                    @if($appointment->pet)

                                        {{ $appointment->pet->Name }}

                                    @else

                                        N/A

                                    @endif

                                </td>


                                <!-- =========================
                                     DATE
                                ========================== -->

                                <td>

                                    {{ \Carbon\Carbon::parse(
                                        $appointment->Appointment_Date
                                    )->format('d M Y') }}

                                </td>


                                <!-- =========================
                                     TIME
                                ========================== -->

                                <td>

                                    {{ \Carbon\Carbon::parse(
                                        $appointment->Appointment_Time
                                    )->format('h:i A') }}

                                </td>


                                <!-- =========================
                                     STATUS
                                ========================== -->

                                <td>

                                    <span
                                        class="status status-{{ $status }}"
                                    >

                                        {{ $appointment->Status ?? 'Pending' }}

                                    </span>

                                </td>


                                <!-- =========================
                                     REPORT ACTION
                                ========================== -->

                                <td>

                                    {{-- CUSTOMER --}}

                                    @if(strtoupper(session('role', '')) === 'CUSTOMER')

                                        @if($report)

                                            <a
                                                href="{{ route(
                                                    'grooming-reports.view',
                                                    $appointment->Appointment_ID
                                                ) }}"
                                                class="action-button create-button"
                                            >
                                                View Report
                                            </a>

                                        @else

                                            <span class="not-available-label">
                                                Report Not Available
                                            </span>

                                        @endif


                                    {{-- GROOMER --}}

                                    @elseif(strtoupper(session('role', '')) === 'GROOMER')

                                        @if($report)

                                            <a
                                                href="{{ route(
                                                    'grooming-reports.edit',
                                                    $appointment->Appointment_ID
                                                ) }}"
                                                class="action-button edit-button"
                                            >
                                                Edit Report
                                            </a>

                                        @else

                                            @if(
                                                strtolower(
                                                    $appointment->Status ?? ''
                                                ) !== 'cancelled'
                                            )

                                                <a
                                                    href="{{ route(
                                                        'grooming-reports.create',
                                                        $appointment->Appointment_ID
                                                    ) }}"
                                                    class="action-button create-button"
                                                >
                                                    Create Report
                                                </a>

                                            @else

                                                <span class="completed-label">
                                                    Cancelled
                                                </span>

                                            @endif

                                        @endif

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <!-- =========================
                 NO APPOINTMENTS
            ========================== -->

            <div class="no-reports">

                <div class="icon">
                    🐾
                </div>

                @if(strtoupper(session('role', '')) === 'CUSTOMER')

                    <h2>
                        No Appointments Found
                    </h2>

                    <p>
                        You currently have no appointments or grooming reports.
                    </p>

                @elseif(strtoupper(session('role', '')) === 'GROOMER')

                    <h2>
                        No Assigned Appointments
                    </h2>

                    <p>
                        You currently have no appointments assigned to you.
                    </p>

                @endif

            </div>

        @endif

    </div>

</div>

@endsection