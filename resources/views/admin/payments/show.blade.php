@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')

<style>

    .payment-details-page {
        padding: 20px 0 50px;
    }

    .page-header {
        margin-bottom: 25px;
    }

    .page-header h1 {
        color: #285b94;
        font-size: 30px;
        margin-bottom: 8px;
    }

    .page-header p {
        color: #777;
    }

    /* =========================
       BACK BUTTON
    ========================= */

    .back-btn {
        display: inline-block;
        text-decoration: none;
        color: #285b94;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .back-btn:hover {
        color: #ff6b81;
    }


    /* =========================
       GRID
    ========================= */

    .details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 22px;
    }

    .details-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .details-card.full-width {
        grid-column: 1 / -1;
    }


    /* =========================
       CARD TITLE
    ========================= */

    .card-title {
        color: #285b94;
        font-size: 20px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #eee;
    }


    /* =========================
       INFORMATION ROW
    ========================= */

    .info-row {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .label {
        color: #777;
        font-weight: 600;
    }

    .value {
        color: #333;
        font-weight: bold;
        text-align: right;
        word-break: break-word;
    }


    /* =========================
       AMOUNT
    ========================= */

    .amount-box {
        background: #f0f6fc;
        border-radius: 12px;
        padding: 20px;
    }

    .amount-row {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        padding: 10px 0;
    }

    .amount-row.discount {
        color: #218838;
    }

    .final-amount {
        border-top: 2px solid #d5e3f0;
        margin-top: 10px;
        padding-top: 15px;
        font-size: 24px;
        color: #285b94;
        font-weight: bold;
    }


    /* =========================
       BADGES
    ========================= */

    .badge {
        display: inline-block;
        padding: 7px 13px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }

    .badge-paid {
        background: #e8f8ee;
        color: #218838;
    }

    .badge-pending {
        background: #fff8e6;
        color: #a66b00;
    }

    .badge-unpaid {
        background: #fdecec;
        color: #c62828;
    }

    .badge-cash {
        background: #e8f4ff;
        color: #285b94;
    }

    .badge-online {
        background: #e8f8ee;
        color: #218838;
    }


    /* =========================
       LOYALTY
    ========================= */

    .loyalty-box {
        background: #fff8e6;
        border: 1px solid #f3d27a;
        border-radius: 12px;
        padding: 20px;
    }

    .loyalty-box h3 {
        color: #8a6500;
        margin-bottom: 10px;
    }

    .loyalty-box p {
        color: #665500;
        line-height: 1.6;
        margin: 0;
    }

    .points {
        color: #a66b00;
        font-weight: bold;
    }


    /* =========================
       ALERTS
    ========================= */

    .success-message {
        background: #e8f8ee;
        border: 1px solid #b8e6c8;
        color: #218838;
        padding: 14px 18px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .error-message {
        background: #fdecec;
        border: 1px solid #f5b5b5;
        color: #c62828;
        padding: 14px 18px;
        border-radius: 8px;
        margin-bottom: 20px;
    }


    /* =========================
       SERVICES TABLE
    ========================= */

    .service-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .service-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 600px;
    }

    .service-table th {
        background: #f5f9fd;
        color: #555;
        text-align: left;
        padding: 13px;
        font-size: 14px;
    }

    .service-table td {
        padding: 13px;
        border-top: 1px solid #eee;
        color: #444;
    }

    .service-price {
        font-weight: bold;
        color: #285b94;
    }

    .service-duration {
        color: #777;
    }


    /* =========================
       EMPTY SERVICES
    ========================= */

    .empty-services {
        text-align: center;
        padding: 25px;
        color: #777;
        background: #f9f9f9;
        border-radius: 10px;
    }


    /* =========================
       PRINT
    ========================= */

    .print-btn {
        display: inline-block;
        border: none;
        background: #3478c9;
        color: white;
        padding: 11px 18px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        margin-top: 20px;
    }

    .print-btn:hover {
        background: #285b94;
    }


    /* =========================
       RESPONSIVE
    ========================= */

    @media (max-width: 750px) {

        .details-grid {
            grid-template-columns: 1fr;
        }

        .details-card.full-width {
            grid-column: auto;
        }

        .info-row {
            flex-direction: column;
            gap: 5px;
        }

        .value {
            text-align: left;
        }

        .amount-row {
            flex-direction: column;
            gap: 5px;
        }

        .final-amount {
            flex-direction: row;
        }

    }


    /* =========================
       PRINT
    ========================= */

    @media print {

        .navbar,
        .back-btn,
        .print-btn {
            display: none !important;
        }

        .payment-details-page {
            padding: 0;
        }

        .details-card {
            box-shadow: none;
            border: 1px solid #ddd;
        }

    }

</style>


<div class="payment-details-page">


    {{-- =========================
         BACK
    ========================= --}}

    <a
        href="{{ route('admin.payments') }}"
        class="back-btn"
    >
        ← Back to Payment Management
    </a>


    {{-- =========================
         HEADER
    ========================= --}}

    <div class="page-header">

        <h1>
            💳 Payment Details
        </h1>

        <p>
            Payment #{{ $payment->Payment_ID }}
        </p>

    </div>


    {{-- =========================
         ALERTS
    ========================= --}}

    @if(session('success'))

        <div class="success-message">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="error-message">
            {{ session('error') }}
        </div>

    @endif


    <div class="details-grid">


        {{-- =====================================================
             CUSTOMER INFORMATION
        ====================================================== --}}

        <div class="details-card">

            <h2 class="card-title">
                👤 Customer Information
            </h2>


            <div class="info-row">

                <span class="label">
                    Customer ID
                </span>

                <span class="value">
                    #{{ $payment->Customer_ID ?? 'N/A' }}
                </span>

            </div>


            <div class="info-row">

                <span class="label">
                    Name
                </span>

                <span class="value">

                    {{ $payment->First_name ?? '' }}
                    {{ $payment->Last_name ?? '' }}

                </span>

            </div>


            @if(!empty($payment->Email))

                <div class="info-row">

                    <span class="label">
                        Email
                    </span>

                    <span class="value">
                        {{ $payment->Email }}
                    </span>

                </div>

            @endif


            @if(!empty($payment->Address))

                <div class="info-row">

                    <span class="label">
                        Address
                    </span>

                    <span class="value">
                        {{ $payment->Address }}
                    </span>

                </div>

            @endif

        </div>


        {{-- =====================================================
             APPOINTMENT INFORMATION
        ====================================================== --}}

        <div class="details-card">

            <h2 class="card-title">
                📅 Appointment Information
            </h2>


            <div class="info-row">

                <span class="label">
                    Appointment ID
                </span>

                <span class="value">
                    #{{ $payment->Appointment_ID }}
                </span>

            </div>


            <div class="info-row">

                <span class="label">
                    Pet
                </span>

                <span class="value">
                    🐾 {{ $payment->Pet_Name ?? 'N/A' }}
                </span>

            </div>


            @if(!empty($payment->Breed))

                <div class="info-row">

                    <span class="label">
                        Breed
                    </span>

                    <span class="value">
                        {{ $payment->Breed }}
                    </span>

                </div>

            @endif


            @if(!empty($payment->Appointment_Date))

                <div class="info-row">

                    <span class="label">
                        Date
                    </span>

                    <span class="value">

                        {{ date(
                            'd M Y',
                            strtotime($payment->Appointment_Date)
                        ) }}

                    </span>

                </div>

            @endif


            @if(!empty($payment->Appointment_Time))

                <div class="info-row">

                    <span class="label">
                        Time
                    </span>

                    <span class="value">
                        {{ $payment->Appointment_Time }}
                    </span>

                </div>

            @endif

        </div>


        {{-- =====================================================
             PAYMENT INFORMATION
        ====================================================== --}}

        <div class="details-card">

            <h2 class="card-title">
                💰 Payment Information
            </h2>


            <div class="info-row">

                <span class="label">
                    Payment ID
                </span>

                <span class="value">
                    #{{ $payment->Payment_ID }}
                </span>

            </div>


            <div class="info-row">

                <span class="label">
                    Payment Method
                </span>

                <span class="value">

                    @if(
                        strtoupper($payment->Payment_Method ?? '')
                        === 'CASH'
                    )

                        <span class="badge badge-cash">
                            💵 CASH
                        </span>

                    @else

                        <span class="badge badge-online">
                            💳
                            {{ strtoupper(
                                $payment->Payment_Method ?? 'ONLINE'
                            ) }}
                        </span>

                    @endif

                </span>

            </div>


            <div class="info-row">

                <span class="label">
                    Payment Status
                </span>

                <span class="value">

                    @php

                        $status =
                            strtoupper(
                                $payment->Payment_Status ?? ''
                            );

                    @endphp


                    @if($status === 'PAID')

                        <span class="badge badge-paid">
                            ✓ PAID
                        </span>

                    @elseif($status === 'PENDING')

                        <span class="badge badge-pending">
                            ⏳ PENDING
                        </span>

                    @else

                        <span class="badge badge-unpaid">
                            ⏳
                            {{ $status ?: 'UNPAID' }}
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

                        {{ date(
                            'd M Y, h:i A',
                            strtotime(
                                $payment->Payment_Date
                            )
                        ) }}

                    @else

                        N/A

                    @endif

                </span>

            </div>

        </div>


        {{-- =====================================================
             AMOUNT INFORMATION
        ====================================================== --}}

        <div class="details-card">

            <h2 class="card-title">
                💵 Amount Summary
            </h2>


            @php

                $loyaltyDiscount =
                    (float) (
                        $payment->Loyalty_Discount ?? 0
                    );

                $finalAmount =
                    (float) (
                        $payment->Total_Amount ?? 0
                    );

                $originalAmount =
                    $finalAmount +
                    $loyaltyDiscount;

            @endphp


            <div class="amount-box">


                {{-- Original Amount --}}

                <div class="amount-row">

                    <span>
                        Original Amount
                    </span>

                    <strong>
                        ৳ {{ number_format(
                            $originalAmount,
                            2
                        ) }}
                    </strong>

                </div>


                {{-- Loyalty Discount --}}

                <div class="amount-row discount">

                    <span>
                        Loyalty Discount
                    </span>

                    <strong>
                        - ৳ {{ number_format(
                            $loyaltyDiscount,
                            2
                        ) }}
                    </strong>

                </div>


                {{-- Final Amount --}}

                <div class="amount-row final-amount">

                    <span>
                        Final Amount
                    </span>

                    <span>
                        ৳ {{ number_format(
                            $finalAmount,
                            2
                        ) }}
                    </span>

                </div>

            </div>

        </div>


        {{-- =====================================================
             LOYALTY INFORMATION
        ====================================================== --}}

        <div class="details-card full-width">

            <h2 class="card-title">
                🎁 Loyalty Point Redemption
            </h2>


            <div class="loyalty-box">

                <h3>
                    Redeemed Loyalty Points
                </h3>


                <p>

                    @if(
                        (int) (
                            $payment->Redeemed_Points ?? 0
                        ) > 0
                    )

                        Customer redeemed

                        <span class="points">

                            {{ $payment->Redeemed_Points }}

                            points

                        </span>

                        for this payment.

                        <br>

                        Each point gives a
                        <strong>৳10</strong>
                        discount.

                        <br>

                        Total loyalty discount:

                        <strong>

                            ৳ {{ number_format(
                                $loyaltyDiscount,
                                2
                            ) }}

                        </strong>

                    @else

                        No loyalty points were redeemed
                        for this payment.

                    @endif

                </p>

            </div>

        </div>


        {{-- =====================================================
             SERVICES
        ====================================================== --}}

        <div class="details-card full-width">

            <h2 class="card-title">
                🐾 Grooming Services
            </h2>


            @if(
                isset($services)
                &&
                $services->count() > 0
            )

                <div class="service-table-wrapper">

                    <table class="service-table">

                        <thead>

                            <tr>

                                <th>
                                    #
                                </th>

                                <th>
                                    Service
                                </th>

                                <th>
                                    Duration
                                </th>

                                <th>
                                    Description
                                </th>

                                <th>
                                    Price
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach(
                                $services as $index => $service
                            )

                                <tr>

                                    <td>
                                        {{ $index + 1 }}
                                    </td>


                                    <td>

                                        <strong>
                                            {{ $service->Service_Name }}
                                        </strong>

                                    </td>


                                    <td>

                                        @if(
                                            !empty(
                                                $service->Duration
                                            )
                                        )

                                            <span
                                                class="service-duration"
                                            >
                                                {{ $service->Duration }}
                                            </span>

                                        @else

                                            N/A

                                        @endif

                                    </td>


                                    <td>

                                        @if(
                                            !empty(
                                                $service->Description
                                            )
                                        )

                                            {{ $service->Description }}

                                        @else

                                            N/A

                                        @endif

                                    </td>


                                    <td>

                                        <span
                                            class="service-price"
                                        >

                                            ৳ {{ number_format(
                                                (float)
                                                $service->Price,
                                                2
                                            ) }}

                                        </span>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- Service Total --}}

                @php

                    $serviceTotal =
                        $services->sum(
                            function ($service) {

                                return (float)
                                    $service->Price;

                            }
                        );

                @endphp


                <div
                    style="
                        margin-top:20px;
                        text-align:right;
                        font-size:17px;
                        font-weight:bold;
                        color:#285b94;
                    "
                >

                    Service Total:

                    ৳ {{ number_format(
                        $serviceTotal,
                        2
                    ) }}

                </div>

            @else

                <div class="empty-services">

                    No grooming services found
                    for this appointment.

                </div>

            @endif

        </div>


        {{-- =====================================================
             PRINT
        ====================================================== --}}

        <div class="details-card full-width">

            <button
                type="button"
                class="print-btn"
                onclick="window.print()"
            >
                🖨 Print Payment Details
            </button>

        </div>


    </div>

</div>

@endsection