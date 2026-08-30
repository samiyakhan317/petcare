@extends('layouts.app')

@section('title', 'Payment')

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

    .info-box {
        background: #f5f9fd;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e5e5e5;
        gap: 20px;
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
        text-align: right;
    }

    .loyalty-box {
        background: #fff8e6;
        border: 1px solid #f3d27a;
        border-radius: 12px;
        padding: 22px;
        margin-bottom: 25px;
    }

    .loyalty-title {
        color: #8a6500;
        font-size: 20px;
        margin-bottom: 10px;
    }

    .loyalty-info {
        color: #665500;
        margin-bottom: 15px;
        line-height: 1.6;
    }

    .points-input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 16px;
        margin-top: 8px;
        box-sizing: border-box;
    }

    .points-input:focus {
        outline: none;
        border-color: #3478c9;
    }

    .calculation-box {
        background: #f0f6fc;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .calculation-row {
        display: flex;
        justify-content: space-between;
        padding: 9px 0;
        gap: 20px;
    }

    .discount {
        color: #218838;
        font-weight: bold;
    }

    .final-row {
        border-top: 2px solid #d5e3f0;
        margin-top: 10px;
        padding-top: 15px;
        font-size: 21px;
        font-weight: bold;
        color: #285b94;
    }

    .payment-method {
        margin-bottom: 25px;
    }

    .payment-method h3 {
        color: #333;
        margin-bottom: 15px;
    }

    .cash-button,
    .online-button {
        width: 100%;
        border: none;
        color: white;
        padding: 14px;
        border-radius: 9px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    .cash-button {
        background: #3478c9;
    }

    .cash-button:hover {
        background: #285b94;
    }

    .online-button {
        background: #28a745;
        margin-top: 12px;
    }

    .online-button:hover {
        background: #218838;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-error {
        background: #fdecec;
        color: #c62828;
        border: 1px solid #f5b5b5;
    }

    .alert-success {
        background: #e8f8ee;
        color: #218838;
        border: 1px solid #b8e6c8;
    }

    .note {
        margin-top: 20px;
        color: #777;
        font-size: 14px;
        text-align: center;
        line-height: 1.5;
    }


    /*
    |--------------------------------------------------------------------------
    | ONLINE PAYMENT MODAL
    |--------------------------------------------------------------------------
    */

    .modal-overlay {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.55);
        justify-content: center;
        align-items: center;
        padding: 20px;
        box-sizing: border-box;
    }

    .modal-box {
        width: 100%;
        max-width: 500px;
        background: white;
        border-radius: 18px;
        padding: 30px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.25);
        position: relative;
        animation: modalOpen 0.25s ease;
        box-sizing: border-box;
    }

    @keyframes modalOpen {

        from {
            transform: scale(0.90);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .modal-title {
        color: #285b94;
        font-size: 25px;
        margin-bottom: 8px;
    }

    .modal-subtitle {
        color: #777;
        font-size: 14px;
        margin-bottom: 22px;
        line-height: 1.5;
    }

    .close-button {
        position: absolute;
        right: 18px;
        top: 14px;
        border: none;
        background: transparent;
        font-size: 28px;
        color: #777;
        cursor: pointer;
    }

    .close-button:hover {
        color: #333;
    }

    .online-summary {
        background: #f5f9fd;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .online-summary-row {
        display: flex;
        justify-content: space-between;
        padding: 7px 0;
    }

    .online-summary-final {
        border-top: 1px solid #ddd;
        margin-top: 7px;
        padding-top: 12px;
        font-size: 20px;
        font-weight: bold;
        color: #285b94;
    }

    .method-title {
        font-weight: bold;
        color: #333;
        margin-bottom: 12px;
    }

    .method-options {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }

    .method-option {
        position: relative;
    }

    .method-option input {
        position: absolute;
        opacity: 0;
    }

    .method-label {
        display: block;
        text-align: center;
        padding: 13px 8px;
        border: 2px solid #ddd;
        border-radius: 9px;
        cursor: pointer;
        font-weight: bold;
        color: #555;
        transition: 0.2s;
    }

    .method-label:hover {
        border-color: #3478c9;
    }

    .method-option input:checked + .method-label {
        border-color: #3478c9;
        background: #eef6ff;
        color: #285b94;
    }

    .online-confirm-button {
        width: 100%;
        border: none;
        background: #28a745;
        color: white;
        padding: 14px;
        border-radius: 9px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
    }

    .online-confirm-button:hover {
        background: #218838;
    }

    .online-cancel-button {
        width: 100%;
        border: none;
        background: #6c757d;
        color: white;
        padding: 12px;
        border-radius: 9px;
        font-size: 15px;
        font-weight: bold;
        cursor: pointer;
        margin-top: 10px;
    }

    .online-cancel-button:hover {
        background: #545b62;
    }

    .security-note {
        text-align: center;
        margin-top: 15px;
        color: #777;
        font-size: 12px;
    }


    @media (max-width: 600px) {

        .payment-card {
            padding: 22px;
        }

        .payment-title {
            font-size: 25px;
        }

        .info-row,
        .calculation-row {
            gap: 15px;
        }

        .method-options {
            grid-template-columns: 1fr;
        }

        .modal-box {
            padding: 22px;
        }
    }

</style>

<div class="payment-page">

```
<div class="payment-container">

    <div class="payment-card">

        <h1 class="payment-title">
            Payment
        </h1>


        {{-- Error Message --}}

        @if(session('error'))

            <div class="alert alert-error">
                {{ session('error') }}
            </div>

        @endif


        {{-- Success Message --}}

        @if(session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif


        {{-- Validation Errors --}}

        @if($errors->any())

            <div class="alert alert-error">

                @foreach($errors->all() as $error)

                    <div>
                        {{ $error }}
                    </div>

                @endforeach

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


        {{-- Loyalty Points --}}

        <div class="loyalty-box">

            <h3 class="loyalty-title">
                🎁 Loyalty Points
            </h3>

            <p class="loyalty-info">

                You currently have
                <strong>{{ $loyaltyPoints }}</strong>
                loyalty points.

                <br>

                <strong>
                    1 loyalty point = 10 BDT discount.
                </strong>

                <br>

                You can redeem up to
                <strong>{{ $maxRedeemPoints }}</strong>
                points for this bill.

            </p>


            <label for="redeem_points">
                Points to Redeem
            </label>


            <input
                type="number"
                id="redeem_points"
                name="redeem_points"
                class="points-input"
                min="0"
                max="{{ $maxRedeemPoints }}"
                value="{{ old('redeem_points', 0) }}"
                form="cash-payment-form"
                oninput="calculateDiscount()"
            >

        </div>


        {{-- Payment Calculation --}}

        <div class="calculation-box">

            <div class="calculation-row">

                <span>
                    Original Amount
                </span>

                <strong>
                    ৳
                    <span id="originalAmount">
                        {{ number_format($totalAmount, 2) }}
                    </span>
                </strong>

            </div>


            <div class="calculation-row">

                <span>
                    Loyalty Discount
                </span>

                <strong class="discount">

                    - ৳

                    <span id="discountAmount">
                        0.00
                    </span>

                </strong>

            </div>


            <div class="calculation-row final-row">

                <span>
                    Final Amount
                </span>

                <span>

                    ৳

                    <span id="finalAmount">
                        {{ number_format($totalAmount, 2) }}
                    </span>

                </span>

            </div>

        </div>


        {{-- Payment Methods --}}

        <div class="payment-method">

            <h3>
                Choose Payment Method
            </h3>


            {{-- CASH PAYMENT --}}

            <form
                id="cash-payment-form"
                action="{{ route(
                    'payments.cash',
                    $appointment->Appointment_ID
                ) }}"
                method="POST"
            >

                @csrf

                <input
                    type="hidden"
                    name="redeem_points"
                    id="cash_redeem_points"
                    value="0"
                >

                <button
                    type="submit"
                    class="cash-button"
                >
                    💵 Pay by Cash
                </button>

            </form>


            {{-- ONLINE PAYMENT --}}

            <button
                type="button"
                class="online-button"
                onclick="openOnlinePayment()"
            >
                💳 Pay Online
            </button>

        </div>


        <p class="note">

            Cash payments are initially marked as
            <strong>PENDING</strong>.

            <br>

            Online payments are marked as
            <strong>PAID</strong>
            after successful confirmation.

            <br>

            Redeemed loyalty points will be deducted immediately.

        </p>

    </div>

</div>
```

</div>

{{-- ================================================================= --}}
{{-- ONLINE PAYMENT POPUP --}}
{{-- ================================================================= --}}

<div
    id="onlinePaymentModal"
    class="modal-overlay"
>

```
<div class="modal-box">

    <button
        type="button"
        class="close-button"
        onclick="closeOnlinePayment()"
    >
        &times;
    </button>


    <h2 class="modal-title">
        Online Payment
    </h2>


    <p class="modal-subtitle">
        Select your preferred online payment method
        and confirm your payment.
    </p>


    {{-- Online Payment Summary --}}

    <div class="online-summary">

        <div class="online-summary-row">

            <span>
                Original Amount
            </span>

            <strong>
                ৳ <span id="onlineOriginalAmount">
                    {{ number_format($totalAmount, 2) }}
                </span>
            </strong>

        </div>


        <div class="online-summary-row">

            <span>
                Loyalty Discount
            </span>

            <strong class="discount">
                - ৳ <span id="onlineDiscountAmount">
                    0.00
                </span>
            </strong>

        </div>


        <div class="online-summary-row online-summary-final">

            <span>
                Payable Amount
            </span>

            <span>
                ৳ <span id="onlineFinalAmount">
                    {{ number_format($totalAmount, 2) }}
                </span>
            </span>

        </div>

    </div>


    {{-- Online Payment Form --}}

    <form
        id="online-payment-form"
        action="{{ route(
            'payments.online',
            $appointment->Appointment_ID
        ) }}"
        method="POST"
    >

        @csrf


        {{-- Loyalty Points --}}

        <input
            type="hidden"
            name="redeem_points"
            id="online_redeem_points"
            value="0"
        >


        <div class="method-title">
            Select Payment Method
        </div>


        <div class="method-options">


            {{-- CARD --}}

            <div class="method-option">

                <input
                    type="radio"
                    id="method_card"
                    name="online_method"
                    value="CARD"
                    checked
                >

                <label
                    for="method_card"
                    class="method-label"
                >
                    💳 CARD
                </label>

            </div>


            {{-- BKASH --}}

            <div class="method-option">

                <input
                    type="radio"
                    id="method_bkash"
                    name="online_method"
                    value="BKASH"
                >

                <label
                    for="method_bkash"
                    class="method-label"
                >
                    📱 BKASH
                </label>

            </div>


            {{-- NAGAD --}}

            <div class="method-option">

                <input
                    type="radio"
                    id="method_nagad"
                    name="online_method"
                    value="NAGAD"
                >

                <label
                    for="method_nagad"
                    class="method-label"
                >
                    📱 NAGAD
                </label>

            </div>

        </div>


        <button
            type="submit"
            class="online-confirm-button"
        >
            ✓ Confirm Online Payment
        </button>


        <button
            type="button"
            class="online-cancel-button"
            onclick="closeOnlinePayment()"
        >
            Cancel
        </button>


        <div class="security-note">
            🔒 This is a simulated online payment for the project.
        </div>

    </form>

</div>
```

</div>

<script>

    /*
    |--------------------------------------------------------------------------
    | Global Amount
    |--------------------------------------------------------------------------
    */

    const originalAmount =
        {{ (float) $totalAmount }};


    /*
    |--------------------------------------------------------------------------
    | Calculate Discount
    |--------------------------------------------------------------------------
    */

    function calculateDiscount() {

        const pointsInput =
            document.getElementById('redeem_points');

        const points =
            parseInt(pointsInput.value) || 0;

        const maxPoints =
            parseInt(pointsInput.max) || 0;


        let validPoints = points;


        /*
        | Prevent negative points
        */

        if (validPoints < 0) {

            validPoints = 0;

            pointsInput.value = 0;
        }


        /*
        | Prevent exceeding maximum
        */

        if (validPoints > maxPoints) {

            validPoints = maxPoints;

            pointsInput.value = maxPoints;
        }


        /*
        |--------------------------------------------------------------------------
        | 1 Point = 10 BDT
        |--------------------------------------------------------------------------
        */

        const discount =
            validPoints * 10;


        /*
        |--------------------------------------------------------------------------
        | Final Amount
        |--------------------------------------------------------------------------
        */

        let finalAmount =
            originalAmount - discount;


        if (finalAmount < 0) {
            finalAmount = 0;
        }


        /*
        |--------------------------------------------------------------------------
        | Update Main Calculation
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'discountAmount'
        ).innerText =
            discount.toFixed(2);


        document.getElementById(
            'finalAmount'
        ).innerText =
            finalAmount.toFixed(2);


        /*
        |--------------------------------------------------------------------------
        | Keep Cash Form Loyalty Points Updated
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'cash_redeem_points'
        ).value =
            validPoints;


        /*
        |--------------------------------------------------------------------------
        | Keep Online Form Loyalty Points Updated
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'online_redeem_points'
        ).value =
            validPoints;


        /*
        |--------------------------------------------------------------------------
        | Update Online Popup
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'onlineDiscountAmount'
        ).innerText =
            discount.toFixed(2);


        document.getElementById(
            'onlineFinalAmount'
        ).innerText =
            finalAmount.toFixed(2);

    }


    /*
    |--------------------------------------------------------------------------
    | Open Online Payment Popup
    |--------------------------------------------------------------------------
    */

    function openOnlinePayment() {

        /*
        | Make sure the latest loyalty points are copied
        */

        calculateDiscount();


        document.getElementById(
            'onlinePaymentModal'
        ).style.display = 'flex';

        document.body.style.overflow = 'hidden';
    }


    /*
    |--------------------------------------------------------------------------
    | Close Online Payment Popup
    |--------------------------------------------------------------------------
    */

    function closeOnlinePayment() {

        document.getElementById(
            'onlinePaymentModal'
        ).style.display = 'none';

        document.body.style.overflow = '';
    }


    /*
    |--------------------------------------------------------------------------
    | Close Popup When Clicking Outside
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        'onlinePaymentModal'
    ).addEventListener(
        'click',
        function(event) {

            if (
                event.target === this
            ) {

                closeOnlinePayment();
            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Close Popup With ESC
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function(event) {

            if (event.key === 'Escape') {

                closeOnlinePayment();
            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Calculate On Page Load
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'DOMContentLoaded',
        function() {

            calculateDiscount();

        }
    );

</script>

@endsection
