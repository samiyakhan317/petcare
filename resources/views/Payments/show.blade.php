@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')

<style>

    .payment-page {
        min-height: calc(100vh - 70px);
        background: linear-gradient(
            135deg,
            #e8f4ff,
            #f7fbff,
            #dff3f0
        );
        padding: 50px 20px;
    }

    .payment-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .payment-card {
        background: white;
        border-radius: 18px;
        padding: 35px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.10);
    }

    .payment-title {
        color: #285b94;
        font-size: 30px;
        margin-bottom: 25px;
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

    .info-box {
        background: #f5f9fd;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #e5e5e5;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .label {
        color: #666;
        font-weight: 600;
    }

    .value {
        color: #333;
        font-weight: bold;
    }

    .status {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: bold;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-paid {
        background: #d4edda;
        color: #155724;
    }

    .status-failed {
        background: #f8d7da;
        color: #721c24;
    }

    .amount-box {
        background: #f0f6fc;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        text-align: center;
    }

    .amount-label {
        color: #666;
        font-size: 16px;
        margin-bottom: 8px;
    }

    .amount {
        color: #285b94;
        font-size: 32px;
        font-weight: bold;
    }

    .button-row {
        display: flex;
        gap: 12px;
        margin-top: 25px;
    }

    .button {
        flex: 1;
        text-align: center;
        text-decoration: none;
        padding: 13px;
        border-radius: 9px;
        font-weight: bold;
        transition: 0.3s;
    }

    .back-button {
        background: #6c757d;
        color: white;
    }

    .back-button:hover {
        background: #545b62;
    }

    .appointment-button {
        background: #3478c9;
        color: white;
    }

    .appointment-button:hover {
        background: #285b94;
    }

    .note {
        margin-top: 25px;
        text-align: center;
        color: #777;
        font-size: 14px;
        line-height: 1.5;
    }

    @media (max-width: 600px) {

        .payment-card {
            padding: 22px;
        }

        .payment-title {
            font-size: 25px;
        }

        .info-row {
            gap: 15px;
        }

        .button-row {
            flex-direction: column;
        }
    }

</style>


<div class="payment-page">

    <div class="payment-container">

        <div class="payment-card">

            <h1 class="payment-title">
                Payment Details
            </h1>


            {{-- Success Message --}}

            @if(session('success'))

                <div class="alert alert-success">
                    {{ session('success') }}
                </div>

            @endif


            {{-- Error Message --}}

            @if(session('error'))

                <div class="alert alert-error">
                    {{ session('error') }}
                </div>

            @endif


            {{-- Appointment Information --}}

            <div class="info-box">

                <div class="info-row">

                    <span class="label">
                        Appointment ID
                    </span>

                    <span class="value">
                        #{{ $appointment->Appointment_ID }}
                    </span>

                </div>


                <div class="info-row">

                    <span class="label">
                        Pet
                    </span>

                    <span class="value">
                        {{ $appointment->pet->Name ?? 'N/A' }}
                    </span>

                </div>


                <div class="info-row">

                    <span class="label">
                        Groomer
                    </span>

                    <span class="value">
                        {{ $appointment->groomer->Name ?? 'Not Assigned' }}
                    </span>

                </div>


                <div class="info-row">

                    <span class="label">
                        Appointment Date
                    </span>

                    <span class="value">
                        {{ $appointment->Appointment_Date }}
                    </span>

                </div>


                <div class="info-row">

                    <span class="label">
                        Appointment Time
                    </span>

                    <span class="value">
                        {{ $appointment->Appointment_Time }}
                    </span>

                </div>

            </div>


            {{-- Payment Information --}}

            <div class="info-box">

                <div class="info-row">

                    <span class="label">
                        Payment Method
                    </span>

                    <span class="value">
                        {{ $payment->Payment_Method }}
                    </span>

                </div>


                <div class="info-row">

                    <span class="label">
                        Payment Status
                    </span>

                    <span class="value">

                        @php
                            $status = strtoupper(
                                trim($payment->Payment_Status ?? '')
                            );
                        @endphp


                        @if($status === 'PAID')

                            <span class="status status-paid">
                                PAID
                            </span>

                        @elseif($status === 'FAILED')

                            <span class="status status-failed">
                                FAILED
                            </span>

                        @else

                            <span class="status status-pending">
                                PENDING
                            </span>

                        @endif

                    </span>

                </div>


                <div class="info-row">

                    <span class="label">
                        Payment Date
                    </span>

                    <span class="value">

                        @if($payment->Payment_Date)

                            {{ \Carbon\Carbon::parse($payment->Payment_Date)->format('d M Y, h:i A') }}

                        @else

                            N/A

                        @endif

                    </span>

                </div>

            </div>


            {{-- Total Amount --}}

            <div class="amount-box">

                <div class="amount-label">
                    Total Amount
                </div>

                <div class="amount">

                    ৳ {{ number_format(
                        (float) $payment->Total_Amount,
                        2
                    ) }}

                </div>

            </div>


            {{-- Buttons --}}

            <div class="button-row">

                <a
                    href="{{ route('appointments.index') }}"
                    class="button back-button"
                >
                    ← My Appointments
                </a>


                <a
                    href="{{ route(
                        'appointments.show',
                        $appointment->Appointment_ID
                    ) }}"
                    class="button appointment-button"
                >
                    View Appointment
                </a>

            </div>


            {{-- Note --}}

            @if(strtoupper($payment->Payment_Status) === 'PENDING')

                <p class="note">

                    Your cash payment is currently
                    <strong>PENDING</strong>.

                    <br>

                    Please pay the staff when you visit.
                    The payment status will be updated after
                    the staff receives the payment.

                </p>

            @elseif(strtoupper($payment->Payment_Status) === 'PAID')

                <p class="note">

                    Your payment has been successfully received.
                    Thank you!

                </p>

            @endif

        </div>

    </div>

</div>

@endsection