@extends('layouts.app')

@section('title', 'My Appointments')

@section('content')

<style>

    .appointments-page {
        width: 100%;
    }

    .appointments-header {
        text-align: center;
        margin-bottom: 35px;
    }

    .appointments-header h1 {
        font-size: 32px;
        margin-bottom: 10px;
        color: #333;
    }

    .appointments-header p {
        color: #777;
        font-size: 16px;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 8px;
        margin-bottom: 25px;
        font-weight: 500;
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

    .appointments-table-container {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        overflow-x: auto;
    }

    .appointments-table {
        width: 100%;
        border-collapse: collapse;
    }

    .appointments-table th {
        background: #f8f9fb;
        padding: 15px;
        text-align: left;
        color: #555;
        font-size: 14px;
        white-space: nowrap;
    }

    .appointments-table td {
        padding: 16px 15px;
        border-top: 1px solid #eee;
        color: #555;
        vertical-align: middle;
    }

    .appointments-table tr:hover {
        background: #fafafa;
    }

    .service-name {
        color: #285b94;
        font-weight: bold;
        margin-bottom: 4px;
    }

    .duration {
        color: #666;
        white-space: nowrap;
        margin-bottom: 4px;
    }

    .status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
        white-space: nowrap;
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

    .view-btn {
        display: inline-block;
        padding: 8px 14px;
        background: #ff6b81;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: bold;
        white-space: nowrap;
    }

    .view-btn:hover {
        background: #ff4f68;
    }

    .no-appointments {
        background: white;
        padding: 60px 30px;
        text-align: center;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .no-appointments .icon {
        font-size: 50px;
        margin-bottom: 15px;
    }

    .no-appointments h2 {
        margin-bottom: 10px;
        color: #333;
    }

    .no-appointments p {
        color: #777;
        margin-bottom: 25px;
    }

    .book-btn {
        display: inline-block;
        padding: 11px 20px;
        background: #ff6b81;
        color: white;
        text-decoration: none;
        border-radius: 7px;
        font-weight: bold;
    }

    .book-btn:hover {
        background: #ff4f68;
    }

    @media (max-width: 700px) {

        .appointments-table th,
        .appointments-table td {
            padding: 10px;
            font-size: 13px;
        }

        .appointments-table-container {
            padding: 15px;
        }

    }

</style>


<div class="appointments-page">

    {{-- =========================
         HEADER
    ========================== --}}

    <div class="appointments-header">

        <h1>
            My Appointments
        </h1>

        <p>
            View and manage your pet grooming appointments.
        </p>

    </div>


    {{-- =========================
         SUCCESS MESSAGE
    ========================== --}}

    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- =========================
         ERROR MESSAGE
    ========================== --}}

    @if(session('error'))

        <div class="alert alert-error">
            {{ session('error') }}
        </div>

    @endif


    {{-- =========================
         VALIDATION ERRORS
    ========================== --}}

    @if($errors->any())

        <div class="alert alert-error">

            @foreach($errors->all() as $error)

                <div>
                    {{ $error }}
                </div>

            @endforeach

        </div>

    @endif


    {{-- =========================
         CHECK APPOINTMENTS
    ========================== --}}

    @if(count($appointments) > 0)

        <div class="appointments-table-container">

            <table class="appointments-table">

                <thead>

                    <tr>

                        <th>
                            Pet
                        </th>

                        <th>
                            Service
                        </th>

                        <th>
                            Duration
                        </th>

                        <th>
                            Date
                        </th>

                        <th>
                            Time
                        </th>

                        <th>
                            Groomer
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($appointments as $appointment)

                        <tr>

                            {{-- =========================
                                 PET
                            ========================== --}}

                            <td>

                                {{ $appointment->Pet_Name ?? 'N/A' }}

                            </td>


                            {{-- =========================
                                 SERVICE
                            ========================== --}}

                            <td>

                                @if(
                                    isset($appointment->Service_Name)
                                    && $appointment->Service_Name
                                )

                                    <div class="service-name">

                                        {{ $appointment->Service_Name }}

                                    </div>

                                @else

                                    N/A

                                @endif

                            </td>


                            {{-- =========================
                                 DURATION
                            ========================== --}}

                            <td>

                                @if(
                                    isset($appointment->Duration)
                                    && $appointment->Duration
                                )

                                    <div class="duration">

                                        {{ $appointment->Duration }}
                                        minutes

                                    </div>

                                @else

                                    N/A

                                @endif

                            </td>


                            {{-- =========================
                                 DATE
                            ========================== --}}

                            <td>

                                @if($appointment->Appointment_Date)

                                    {{ \Carbon\Carbon::parse(
                                        $appointment->Appointment_Date
                                    )->format('d M Y') }}

                                @else

                                    N/A

                                @endif

                            </td>


                            {{-- =========================
                                 TIME
                            ========================== --}}

                            <td>

                                @if($appointment->Appointment_Time)

                                    {{ \Carbon\Carbon::parse(
                                        $appointment->Appointment_Time
                                    )->format('h:i A') }}

                                @else

                                    N/A

                                @endif

                            </td>


                            {{-- =========================
                                 GROOMER
                            ========================== --}}

                            <td>

                                {{ $appointment->Groomer_Name ?? 'Not Assigned' }}

                            </td>


                            {{-- =========================
                                 STATUS
                            ========================== --}}

                            <td>

                                @php

                                    $status = strtoupper(
                                        $appointment->Status ?? 'PENDING'
                                    );

                                @endphp


                                @if($status === 'PENDING')

                                    <span class="status status-pending">
                                        PENDING
                                    </span>

                                @elseif($status === 'CONFIRMED')

                                    <span class="status status-confirmed">
                                        CONFIRMED
                                    </span>

                                @elseif($status === 'COMPLETED')

                                    <span class="status status-completed">
                                        COMPLETED
                                    </span>

                                @elseif($status === 'CANCELLED')

                                    <span class="status status-cancelled">
                                        CANCELLED
                                    </span>

                                @else

                                    <span class="status status-pending">
                                        {{ $status }}
                                    </span>

                                @endif

                            </td>


                            {{-- =========================
                                 ACTION
                            ========================== --}}

                            <td>

                                <a
                                    href="{{ route(
                                        'appointments.show',
                                        [
                                            'id' => $appointment->Appointment_ID
                                        ]
                                    ) }}"
                                    class="view-btn"
                                >

                                    View Details

                                </a>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


    @else

        {{-- =========================
             NO APPOINTMENTS
        ========================== --}}

        <div class="no-appointments">

            <div class="icon">
                🐾
            </div>

            <h2>
                No Appointments Yet
            </h2>

            <p>
                You haven't booked any grooming appointments yet.
            </p>

            <a
                href="{{ route('appointments.create') }}"
                class="book-btn"
            >

                Book an Appointment

            </a>

        </div>

    @endif

</div>

@endsection