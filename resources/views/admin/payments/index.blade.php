@extends('layouts.app')

@section('title', 'Payment Management')

@section('content')

<style>

    .payments-page {
        padding: 20px 0 50px;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .page-header h1 {
        color: #285b94;
        font-size: 32px;
        margin-bottom: 8px;
    }

    .page-header p {
        color: #777;
        font-size: 15px;
    }

    /* =========================
       ALERTS
    ========================= */

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


    /* =========================
       SUMMARY CARDS
    ========================= */

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 30px;
    }

    .summary-card {
        background: white;
        border-radius: 14px;
        padding: 22px;
        box-shadow: 0 5px 18px rgba(0,0,0,0.08);
        border-left: 5px solid #3478c9;
    }

    .summary-card h3 {
        color: #777;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .summary-card .number {
        color: #285b94;
        font-size: 25px;
        font-weight: bold;
    }

    .summary-card.paid {
        border-left-color: #28a745;
    }

    .summary-card.paid .number {
        color: #218838;
    }

    .summary-card.unpaid {
        border-left-color: #dc3545;
    }

    .summary-card.unpaid .number {
        color: #c62828;
    }

    .summary-card.discount {
        border-left-color: #f0ad4e;
    }

    .summary-card.discount .number {
        color: #a66b00;
    }


    /* =========================
       TABLE CARD
    ========================= */

    .table-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .table-header {
        padding: 22px 25px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-header h2 {
        color: #333;
        font-size: 21px;
    }

    .table-header span {
        color: #777;
        font-size: 14px;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    table {
        width: 100%;
        min-width: 1250px;
        border-collapse: collapse;
    }

    th {
        background: #f5f9fd;
        color: #555;
        padding: 15px 12px;
        text-align: left;
        font-size: 13px;
        white-space: nowrap;
    }

    td {
        padding: 15px 12px;
        border-top: 1px solid #eee;
        color: #444;
        font-size: 14px;
        vertical-align: middle;
    }

    tr:hover td {
        background: #fafcff;
    }


    /* =========================
       CUSTOMER
    ========================= */

    .customer-name {
        font-weight: bold;
        color: #285b94;
    }

    .customer-email {
        color: #888;
        font-size: 12px;
        margin-top: 3px;
    }


    /* =========================
       PET
    ========================= */

    .pet-name {
        font-weight: bold;
        color: #444;
    }


    /* =========================
       AMOUNT
    ========================= */

    .amount {
        font-weight: bold;
        color: #285b94;
        white-space: nowrap;
    }

    .discount-amount {
        color: #218838;
        font-weight: bold;
        white-space: nowrap;
    }

    .points {
        color: #a66b00;
        font-weight: bold;
    }


    /* =========================
       BADGES
    ========================= */

    .badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        white-space: nowrap;
    }

    .badge-paid {
        background: #e8f8ee;
        color: #218838;
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
       ACTION BUTTONS
    ========================= */

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 7px;
        align-items: flex-start;
    }

    .view-btn {
        display: inline-block;
        text-decoration: none;
        background: #3478c9;
        color: white;
        padding: 8px 13px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: bold;
        transition: 0.3s;
        white-space: nowrap;
    }

    .view-btn:hover {
        background: #285b94;
    }

    .paid-btn {
        border: none;
        background: #28a745;
        color: white;
        padding: 8px 13px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
        white-space: nowrap;
    }

    .paid-btn:hover {
        background: #218838;
    }

    .already-paid {
        display: inline-block;
        background: #e8f8ee;
        color: #218838;
        padding: 8px 13px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: bold;
        white-space: nowrap;
    }


    /* =========================
       EMPTY STATE
    ========================= */

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #777;
    }

    .empty-state .icon {
        font-size: 45px;
        margin-bottom: 15px;
    }

    .empty-state h3 {
        color: #555;
        margin-bottom: 8px;
    }


    /* =========================
       RESPONSIVE
    ========================= */

    @media (max-width: 1000px) {

        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .page-header h1 {
            font-size: 27px;
        }
    }

</style>

<div class="payments-page">

```
{{-- =========================
     PAGE HEADER
========================= --}}

<div class="page-header">

    <h1>
        💳 Payment Management
    </h1>

    <p>
        View, monitor and update all customer payments.
    </p>

</div>


{{-- =========================
     ALERTS
========================= --}}

@if(session('success'))

    <div class="alert alert-success">
        {{ session('success') }}
    </div>

@endif


@if(session('error'))

    <div class="alert alert-error">
        {{ session('error') }}
    </div>

@endif


{{-- =========================
     SUMMARY
========================= --}}

<div class="summary-grid">

    <div class="summary-card">

        <h3>
            Total Payments
        </h3>

        <div class="number">
            {{ $totalPayments }}
        </div>

    </div>


    <div class="summary-card paid">

        <h3>
            Paid Amount
        </h3>

        <div class="number">
            ৳ {{ number_format($paidAmount, 2) }}
        </div>

    </div>


    <div class="summary-card unpaid">

        <h3>
            Unpaid Amount
        </h3>

        <div class="number">
            ৳ {{ number_format($unpaidAmount, 2) }}
        </div>

    </div>


    <div class="summary-card discount">

        <h3>
            Loyalty Discounts
        </h3>

        <div class="number">
            ৳ {{ number_format($totalLoyaltyDiscount, 2) }}
        </div>

    </div>

</div>


{{-- =========================
     PAYMENT TABLE
========================= --}}

<div class="table-card">

    <div class="table-header">

        <h2>
            Customer Payments
        </h2>

        <span>
            {{ $totalPayments }} payment(s)
        </span>

    </div>


    @if($payments->count() > 0)

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>Payment ID</th>
                        <th>Customer</th>
                        <th>Pet</th>
                        <th>Appointment</th>
                        <th>Amount</th>
                        <th>Loyalty Redeemed</th>
                        <th>Discount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Payment Date</th>
                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($payments as $payment)

                        <tr>

                            {{-- PAYMENT ID --}}

                            <td>
                                <strong>
                                    #{{ $payment->Payment_ID }}
                                </strong>
                            </td>


                            {{-- CUSTOMER --}}

                            <td>

                                <div class="customer-name">

                                    {{ $payment->First_name ?? '' }}
                                    {{ $payment->Last_name ?? '' }}

                                </div>

                                @if(!empty($payment->Email))

                                    <div class="customer-email">
                                        {{ $payment->Email }}
                                    </div>

                                @endif

                            </td>


                            {{-- PET --}}

                            <td>

                                <div class="pet-name">

                                    🐾
                                    {{ $payment->Pet_Name ?? 'N/A' }}

                                </div>

                            </td>


                            {{-- APPOINTMENT --}}

                            <td>

                                <strong>
                                    #{{ $payment->Appointment_ID }}
                                </strong>

                                <br>

                                <small>

                                    {{ $payment->Appointment_Date }}

                                    @if($payment->Appointment_Time)

                                        <br>

                                        {{ $payment->Appointment_Time }}

                                    @endif

                                </small>

                            </td>


                            {{-- AMOUNT --}}

                            <td>

                                <span class="amount">

                                    ৳
                                    {{ number_format(
                                        (float) $payment->Total_Amount,
                                        2
                                    ) }}

                                </span>

                            </td>


                            {{-- LOYALTY POINTS --}}

                            <td>

                                @if(
                                    (int) ($payment->Redeemed_Points ?? 0) > 0
                                )

                                    <span class="points">

                                        🎁
                                        {{ $payment->Redeemed_Points }}
                                        points

                                    </span>

                                @else

                                    <span style="color:#999;">
                                        None
                                    </span>

                                @endif

                            </td>


                            {{-- LOYALTY DISCOUNT --}}

                            <td>

                                @if(
                                    (float) ($payment->Loyalty_Discount ?? 0) > 0
                                )

                                    <span class="discount-amount">

                                        - ৳
                                        {{ number_format(
                                            (float) $payment->Loyalty_Discount,
                                            2
                                        ) }}

                                    </span>

                                @else

                                    <span style="color:#999;">
                                        ৳ 0.00
                                    </span>

                                @endif

                            </td>


                            {{-- PAYMENT METHOD --}}

                            <td>

                                @if(
                                    strtoupper(
                                        $payment->Payment_Method ?? ''
                                    ) === 'CASH'
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

                            </td>


                            {{-- PAYMENT STATUS --}}

                            <td>

                                @if(
                                    strtoupper(
                                        $payment->Payment_Status ?? ''
                                    ) === 'PAID'
                                )

                                    <span class="badge badge-paid">
                                        ✓ PAID
                                    </span>

                                @else

                                    <span class="badge badge-unpaid">

                                        ⏳
                                        {{ strtoupper(
                                            $payment->Payment_Status ?? 'PENDING'
                                        ) }}

                                    </span>

                                @endif

                            </td>


                            {{-- PAYMENT DATE --}}

                            <td>

                                @if($payment->Payment_Date)

                                    {{ date(
                                        'd M Y',
                                        strtotime($payment->Payment_Date)
                                    ) }}

                                    <br>

                                    <small>

                                        {{ date(
                                            'h:i A',
                                            strtotime($payment->Payment_Date)
                                        ) }}

                                    </small>

                                @else

                                    N/A

                                @endif

                            </td>


                            {{-- ACTION --}}

                            <td>

                                <div class="action-buttons">

                                    <a
                                        href="{{ route(
                                            'admin.payments.show',
                                            $payment->Payment_ID
                                        ) }}"
                                        class="view-btn"
                                    >
                                        👁 View Details
                                    </a>


                                    @if(
                                        in_array(
                                            strtoupper(
                                                $payment->Payment_Status ?? ''
                                            ),
                                            [
                                                'UNPAID',
                                                'PENDING'
                                            ]
                                        )
                                    )

                                        <form
                                            action="{{ route(
                                                'admin.payments.markPaid',
                                                $payment->Payment_ID
                                            ) }}"
                                            method="POST"
                                            onsubmit="return confirm(
                                                'Are you sure you want to mark Payment #{{ $payment->Payment_ID }} as PAID?'
                                            );"
                                        >

                                            @csrf

                                            <button
                                                type="submit"
                                                class="paid-btn"
                                            >
                                                ✓ Mark as Paid
                                            </button>

                                        </form>

                                    @else

                                        <span class="already-paid">
                                            ✓ Already Paid
                                        </span>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @else

        <div class="empty-state">

            <div class="icon">
                💳
            </div>

            <h3>
                No Payments Found
            </h3>

            <p>
                There are currently no customer payments.
            </p>

        </div>

    @endif

</div>
```

</div>

@endsection
