@extends('layouts.app')

@section('title', 'Grooming Report')

@section('content')

<style>

    .report-page {
        min-height: calc(100vh - 70px);
        background:
            linear-gradient(
                135deg,
                #e8f4ff,
                #f7fbff,
                #dff3f0
            );
        padding: 50px 20px;
    }

    .report-container {
        max-width: 850px;
        margin: 0 auto;
    }

    .report-card {
        background: white;
        border-radius: 18px;
        padding: 35px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.10);
    }

    .report-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .report-header h1 {
        color: #285b94;
        font-size: 30px;
        margin-bottom: 8px;
    }

    .report-header p {
        color: #777;
    }

    .appointment-info {
        background: #f0f6fc;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .appointment-info h2 {
        color: #285b94;
        font-size: 20px;
        margin-bottom: 15px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .info-item {
        color: #555;
    }

    .info-item strong {
        color: #333;
    }

    .report-section {
        margin-top: 25px;
    }

    .report-section h3 {
        color: #285b94;
        font-size: 18px;
        margin-bottom: 8px;
    }

    .report-value {
        background: #f8fafc;
        border: 1px solid #e5e9ef;
        border-radius: 8px;
        padding: 14px;
        color: #555;
        min-height: 45px;
        line-height: 1.5;
    }

    .back-button {
        display: inline-block;
        margin-top: 30px;
        padding: 11px 20px;
        background: #3478c9;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: bold;
    }

    .back-button:hover {
        background: #285b94;
    }

    @media (max-width: 600px) {

        .report-card {
            padding: 22px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

    }

</style>


<div class="report-page">

    <div class="report-container">

        <div class="report-card">

            <!-- HEADER -->

            <div class="report-header">

                <h1>
                    🐾 Grooming Report
                </h1>

                <p>
                    Grooming details for your pet
                </p>

            </div>


            <!-- APPOINTMENT INFORMATION -->

            <div class="appointment-info">

                <h2>
                    Appointment Information
                </h2>

                <div class="info-grid">

                    <div class="info-item">
                        <strong>Appointment ID:</strong>
                        #{{ $appointment->Appointment_ID }}
                    </div>

                    <div class="info-item">
                        <strong>Pet:</strong>

                        @if($appointment->pet)
                            {{ $appointment->pet->Name }}
                        @else
                            N/A
                        @endif
                    </div>

                    <div class="info-item">
                        <strong>Date:</strong>

                        {{ \Carbon\Carbon::parse(
                            $appointment->Appointment_Date
                        )->format('d M Y') }}
                    </div>

                    <div class="info-item">
                        <strong>Time:</strong>

                        {{ \Carbon\Carbon::parse(
                            $appointment->Appointment_Time
                        )->format('h:i A') }}
                    </div>

                    <div class="info-item">
                        <strong>Groomer:</strong>

                        @if($appointment->groomer)
                            {{ $appointment->groomer->Name }}
                        @else
                            N/A
                        @endif
                    </div>

                </div>

            </div>


            <!-- COAT CONDITION -->

            <div class="report-section">

                <h3>
                    Coat Condition
                </h3>

                <div class="report-value">
                    {{ $report->Coat_Condition ?? 'Not provided' }}
                </div>

            </div>


            <!-- SKIN CONDITION -->

            <div class="report-section">

                <h3>
                    Skin Condition
                </h3>

                <div class="report-value">
                    {{ $report->Skin_Condition ?? 'Not provided' }}
                </div>

            </div>


            <!-- EAR CLEANING -->

            <div class="report-section">

                <h3>
                    Ear Cleaning
                </h3>

                <div class="report-value">
                    {{ $report->Ear_Cleaning ?? 'Not provided' }}
                </div>

            </div>


            <!-- NAIL TRIMMING -->

            <div class="report-section">

                <h3>
                    Nail Trimming
                </h3>

                <div class="report-value">
                    {{ $report->Nail_Trimming ?? 'Not provided' }}
                </div>

            </div>


            <!-- RECOMMENDATION -->

            <div class="report-section">

                <h3>
                    Recommendation
                </h3>

                <div class="report-value">
                    {{ $report->Recommendation ?? 'No recommendation provided' }}
                </div>

            </div>


            <!-- GROOMER NOTES -->

            <div class="report-section">

                <h3>
                    Groomer Notes
                </h3>

                <div class="report-value">
                    {{ $report->Groomer_Notes ?? 'No notes provided' }}
                </div>

            </div>


            <!-- CREATED DATE -->

            @if($report->Created_At)

                <div class="report-section">

                    <h3>
                        Report Date
                    </h3>

                    <div class="report-value">

                        {{ \Carbon\Carbon::parse(
                            $report->Created_At
                        )->format('d M Y, h:i A') }}

                    </div>

                </div>

            @endif


            <!-- BACK BUTTON -->

            <a
                href="{{ route('grooming-reports.index') }}"
                class="back-button"
            >
                ← Back to Grooming Reports
            </a>

        </div>

    </div>

</div>

@endsection