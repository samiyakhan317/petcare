@extends('layouts.app')

@section('title', 'Appointment Details')

@section('content')

<style>

    .appointment-page {
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

    .appointment-container {
        max-width: 850px;
        margin: 0 auto;
    }

    .appointment-card {
        background: white;
        padding: 35px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.10);
        margin-bottom: 25px;
    }

    .appointment-card h1 {
        color: #285b94;
        margin-bottom: 10px;
    }

    .subtitle {
        color: #777;
        margin-bottom: 25px;
    }

    .success-message {
        background: #e8f8ee;
        color: #218838;
        border: 1px solid #b8e6c8;
        padding: 12px 15px;
        border-radius: 7px;
        margin-bottom: 20px;
    }

    .error-message {
        background: #fdecec;
        color: #c62828;
        border: 1px solid #f5b5b5;
        padding: 12px 15px;
        border-radius: 7px;
        margin-bottom: 20px;
    }

    .details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-top: 25px;
    }

    .detail-box {
        background: #f7fbff;
        padding: 18px;
        border-radius: 10px;
        border: 1px solid #e2edf7;
    }

    .detail-label {
        color: #777;
        font-size: 14px;
        margin-bottom: 6px;
    }

    .detail-value {
        color: #285b94;
        font-weight: bold;
        font-size: 16px;
    }

    .status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
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

    .status-default {
        background: #e2e3e5;
        color: #383d41;
    }

    .services-title {
        color: #285b94;
        margin-bottom: 20px;
    }

    .service-table {
        width: 100%;
        border-collapse: collapse;
    }

    .service-table th {
        background: #f0f6fc;
        color: #285b94;
        padding: 13px;
        text-align: left;
    }

    .service-table td {
        padding: 13px;
        border-bottom: 1px solid #eee;
        color: #555;
        vertical-align: top;
    }

    .total-row td {
        font-weight: bold;
        color: #285b94;
        font-size: 17px;
        border-bottom: none;
        padding-top: 20px;
    }

    .no-services {
        background: #f7f7f7;
        padding: 20px;
        border-radius: 8px;
        color: #777;
        text-align: center;
    }

    /* =========================
       GROOMING REPORT
    ========================= */

    .report-title {
        color: #285b94;
        margin-bottom: 8px;
    }

    .report-subtitle {
        color: #777;
        margin-bottom: 25px;
    }

    .report-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .report-box {
        background: #f7fbff;
        border: 1px solid #e2edf7;
        border-radius: 10px;
        padding: 18px;
    }

    .report-box.full-width {
        grid-column: 1 / -1;
    }

    .report-label {
        color: #777;
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .report-value {
        color: #444;
        font-size: 15px;
        line-height: 1.6;
        white-space: pre-line;
    }

    .report-date {
        background: #e8f4ff;
        color: #285b94;
        padding: 10px 14px;
        border-radius: 8px;
        display: inline-block;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: bold;
    }

    .no-report {
        background: #f7f7f7;
        padding: 25px;
        border-radius: 10px;
        color: #777;
        text-align: center;
    }

    .no-report-icon {
        font-size: 38px;
        margin-bottom: 10px;
    }

    .buttons {
        display: flex;
        gap: 12px;
        margin-top: 25px;
        flex-wrap: wrap;
    }

    .button {
        display: inline-block;
        padding: 11px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .back-button {
        background: #3478c9;
        color: white;
    }

    .back-button:hover {
        background: #285b94;
    }

    .cancel-button {
        background: #dc3545;
        color: white;
    }

    .cancel-button:hover {
        background: #b02a37;
    }

    @media (max-width: 700px) {

        .appointment-page {
            padding: 30px 20px;
        }

        .details {
            grid-template-columns: 1fr;
        }

        .report-grid {
            grid-template-columns: 1fr;
        }

        .report-box.full-width {
            grid-column: auto;
        }

        .service-table {
            font-size: 13px;
        }

        .service-table th,
        .service-table td {
            padding: 9px;
        }

        .appointment-card {
            padding: 25px;
        }

        .buttons {
            flex-direction: column;
        }

        .button {
            text-align: center;
        }

    }

</style>

<div class="appointment-page">

```
<div class="appointment-container">


    {{-- =========================
         SUCCESS MESSAGE
    ========================== --}}

    @if(session('success'))

        <div class="success-message">
            {{ session('success') }}
        </div>

    @endif


    {{-- =========================
         ERROR MESSAGE
    ========================== --}}

    @if(session('error'))

        <div class="error-message">
            {{ session('error') }}
        </div>

    @endif


    {{-- =========================
         APPOINTMENT INFORMATION
    ========================== --}}

    <div class="appointment-card">

        <h1>
            Appointment Details
        </h1>

        <p class="subtitle">
            View your grooming appointment information.
        </p>


        <div class="details">


            {{-- APPOINTMENT ID --}}

            <div class="detail-box">

                <div class="detail-label">
                    Appointment ID
                </div>

                <div class="detail-value">
                    #{{ $appointment->Appointment_ID }}
                </div>

            </div>


            {{-- PET --}}

            <div class="detail-box">

                <div class="detail-label">
                    Pet
                </div>

                <div class="detail-value">

                    @if($appointment->pet)

                        {{ $appointment->pet->Name }}

                    @else

                        Not available

                    @endif

                </div>

            </div>


            {{-- DATE --}}

            <div class="detail-box">

                <div class="detail-label">
                    Appointment Date
                </div>

                <div class="detail-value">

                    @if($appointment->Appointment_Date)

                        {{ \Carbon\Carbon::parse(
                            $appointment->Appointment_Date
                        )->format('d M Y') }}

                    @else

                        Not available

                    @endif

                </div>

            </div>


            {{-- TIME --}}

            <div class="detail-box">

                <div class="detail-label">
                    Appointment Time
                </div>

                <div class="detail-value">

                    @if($appointment->Appointment_Time)

                        {{ \Carbon\Carbon::parse(
                            $appointment->Appointment_Time
                        )->format('h:i A') }}

                    @else

                        Not available

                    @endif

                </div>

            </div>


            {{-- GROOMER --}}

            <div class="detail-box">

                <div class="detail-label">
                    Groomer
                </div>

                <div class="detail-value">

                    @if($appointment->groomer)

                        {{ $appointment->groomer->Name }}

                    @else

                        Not assigned yet

                    @endif

                </div>

            </div>


            {{-- STATUS --}}

            <div class="detail-box">

                <div class="detail-label">
                    Status
                </div>

                <div class="detail-value">

                    @php

                        $status = strtolower(
                            trim($appointment->Status ?? '')
                        );

                        $statusClass = match($status) {

                            'pending' =>
                                'status status-pending',

                            'confirmed' =>
                                'status status-confirmed',

                            'completed' =>
                                'status status-completed',

                            'cancelled' =>
                                'status status-cancelled',

                            default =>
                                'status status-default'

                        };

                    @endphp


                    <span class="{{ $statusClass }}">

                        {{ $appointment->Status ?? 'Pending' }}

                    </span>

                </div>

            </div>


        </div>

    </div>


    {{-- =========================
         GROOMING SERVICES
    ========================== --}}

    <div class="appointment-card">

        <h2 class="services-title">
            Grooming Service
        </h2>


        @php

            /*
             * Get only the services belonging
             * to this appointment.
             */

            $appointmentServices =
                $appointment->services ?? collect();

            /*
             * Calculate total price.
             */

            $totalPrice =
                $appointmentServices->sum('Price');

        @endphp


        @if($appointmentServices->count() > 0)

            <div style="overflow-x: auto;">

                <table class="service-table">

                    <thead>

                        <tr>

                            <th>
                                Service
                            </th>

                            <th>
                                Duration
                            </th>

                            <th>
                                Price
                            </th>

                            <th>
                                Description
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($appointmentServices as $service)

                            <tr>

                                <td>

                                    <strong>
                                        {{ $service->Service_Name }}
                                    </strong>

                                </td>

                                <td>
                                    {{ $service->Duration }} minutes
                                </td>

                                <td>
                                    ৳{{ number_format(
                                        $service->Price,
                                        2
                                    ) }}
                                </td>

                                <td>

                                    {{ $service->Description
                                        ?: 'No description'
                                    }}

                                </td>

                            </tr>

                        @endforeach


                        {{-- TOTAL --}}

                        <tr class="total-row">

                            <td colspan="2">
                                Total
                            </td>

                            <td colspan="2">

                                ৳{{ number_format(
                                    $totalPrice,
                                    2
                                ) }}

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        @else

            <div class="no-services">

                No grooming service has been added
                to this appointment.

            </div>

        @endif

    </div>


    {{-- =========================
         GROOMING REPORT
    ========================== --}}

    <div class="appointment-card">

        <h2 class="report-title">
            Grooming Report
        </h2>

        <p class="report-subtitle">
            Grooming details recorded by the groomer.
        </p>


        @if(isset($report) && $report)

            @if($report->Created_At)

                <div class="report-date">

                    Report Date:

                    {{ \Carbon\Carbon::parse(
                        $report->Created_At
                    )->format('d M Y, h:i A') }}

                </div>

            @endif


            <div class="report-grid">


                {{-- COAT CONDITION --}}

                <div class="report-box">

                    <div class="report-label">
                        Coat Condition
                    </div>

                    <div class="report-value">

                        {{ $report->Coat_Condition
                            ?: 'Not provided'
                        }}

                    </div>

                </div>


                {{-- SKIN CONDITION --}}

                <div class="report-box">

                    <div class="report-label">
                        Skin Condition
                    </div>

                    <div class="report-value">

                        {{ $report->Skin_Condition
                            ?: 'Not provided'
                        }}

                    </div>

                </div>


                {{-- EAR CLEANING --}}

                <div class="report-box">

                    <div class="report-label">
                        Ear Cleaning
                    </div>

                    <div class="report-value">

                        {{ $report->Ear_Cleaning
                            ?: 'Not provided'
                        }}

                    </div>

                </div>


                {{-- NAIL TRIMMING --}}

                <div class="report-box">

                    <div class="report-label">
                        Nail Trimming
                    </div>

                    <div class="report-value">

                        {{ $report->Nail_Trimming
                            ?: 'Not provided'
                        }}

                    </div>

                </div>


                {{-- RECOMMENDATION --}}

                <div class="report-box full-width">

                    <div class="report-label">
                        Recommendation
                    </div>

                    <div class="report-value">

                        {{ $report->Recommendation
                            ?: 'No recommendation provided.'
                        }}

                    </div>

                </div>


                {{-- GROOMER NOTES --}}

                <div class="report-box full-width">

                    <div class="report-label">
                        Grooming Notes
                    </div>

                    <div class="report-value">

                        {{ $report->Groomer_Notes
                            ?: 'No grooming notes provided.'
                        }}

                    </div>

                </div>


            </div>

        @else

            <div class="no-report">

                <div class="no-report-icon">
                    🐾
                </div>

                <strong>
                    Grooming Report Not Available
                </strong>

                <p style="margin-top: 8px;">
                    The groomer has not submitted a grooming
                    report for this appointment yet.
                </p>

            </div>

        @endif

    </div>


    {{-- =========================
         BUTTONS
    ========================== --}}

    <div class="appointment-card">

        <div class="buttons">


            {{-- BACK --}}

            <a
                href="{{ route('appointments.index') }}"
                class="button back-button"
            >
                ← Back to My Appointments
            </a>


            {{-- CANCEL --}}

            @if(
                !in_array(
                    strtolower(
                        trim($appointment->Status ?? '')
                    ),
                    ['cancelled', 'completed']
                )
            )

                <form
                    method="POST"
                    action="{{ route(
                        'appointments.cancel',
                        $appointment->Appointment_ID
                    ) }}"
                    style="display: inline;"
                >

                    @csrf

                    <button
                        type="submit"
                        class="button cancel-button"
                        onclick="return confirm(
                            'Are you sure you want to cancel this appointment?'
                        );"
                    >
                        Cancel Appointment
                    </button>

                </form>

            @endif


        </div>

    </div>


</div>
```

</div>

@endsection
