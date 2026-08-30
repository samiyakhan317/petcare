<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\LoyaltyTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Check Customer Login
    |--------------------------------------------------------------------------
    */

    private function checkCustomer(Request $request)
    {
        if (!$request->session()->has('user_id')) {

            return redirect()
                ->route('login');
        }

        if (
            strtoupper(
                $request->session()->get('role', '')
            ) !== 'CUSTOMER'
        ) {

            return redirect()
                ->route('home');
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Show Payment Page
    |--------------------------------------------------------------------------
    */

    public function create(
        Request $request,
        $appointmentId
    ) {
        $check = $this->checkCustomer($request);

        if ($check) {
            return $check;
        }

        $customerId =
            $request->session()->get('user_id');


        /*
        |--------------------------------------------------------------------------
        | Find Customer
        |--------------------------------------------------------------------------
        */

        $customer = Customer::where(
            'ID',
            $customerId
        )->first();


        if (!$customer) {

            return redirect()
                ->route('home')
                ->with(
                    'error',
                    'Customer account not found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Find Customer Appointment
        |--------------------------------------------------------------------------
        */

        $appointment = Appointment::with([
            'pet',
            'groomer',
            'services'
        ])
        ->where(
            'Appointment_ID',
            $appointmentId
        )
        ->whereHas(
            'pet',
            function ($query) use ($customerId) {

                $query->where(
                    'Customer_ID',
                    $customerId
                );
            }
        )
        ->first();


        if (!$appointment) {

            return redirect()
                ->route('appointments.index')
                ->with(
                    'error',
                    'Appointment not found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Check Existing Payment
        |--------------------------------------------------------------------------
        */

        $payment = Payment::where(
            'Appointment_ID',
            $appointmentId
        )->first();


        /*
        |--------------------------------------------------------------------------
        | Calculate Original Amount
        |--------------------------------------------------------------------------
        */

        $totalAmount =
            $appointment->services->sum(
                function ($service) {

                    return (float) $service->Price;
                }
            );


        /*
        |--------------------------------------------------------------------------
        | Loyalty Points
        |--------------------------------------------------------------------------
        */

        $loyaltyPoints =
            (int) ($customer->Loyalty_Points ?? 0);


        /*
        |--------------------------------------------------------------------------
        | Maximum Redeemable Points
        |--------------------------------------------------------------------------
        |
        | 1 point = 10 BDT
        |
        */

        $maxPointsByBill =
            (int) floor(
                $totalAmount / 10
            );


        $maxRedeemPoints =
            min(
                $loyaltyPoints,
                $maxPointsByBill
            );


        /*
        |--------------------------------------------------------------------------
        | Show Payment Page
        |--------------------------------------------------------------------------
        */

        return view(
            'payments.create',
            compact(
                'appointment',
                'payment',
                'totalAmount',
                'loyaltyPoints',
                'maxRedeemPoints'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Process Cash Payment
    |--------------------------------------------------------------------------
    */

    public function cash(
        Request $request,
        $appointmentId
    ) {
        $check = $this->checkCustomer($request);

        if ($check) {
            return $check;
        }


        $customerId =
            $request->session()->get('user_id');


        /*
        |--------------------------------------------------------------------------
        | Validate Loyalty Points
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'redeem_points' =>
                'nullable|integer|min:0',
        ]);


        $redeemPoints =
            (int) (
                $request->redeem_points ?? 0
            );


        /*
        |--------------------------------------------------------------------------
        | Find Customer
        |--------------------------------------------------------------------------
        */

        $customer = Customer::where(
            'ID',
            $customerId
        )->first();


        if (!$customer) {

            return redirect()
                ->route('home')
                ->with(
                    'error',
                    'Customer account not found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Find Appointment
        |--------------------------------------------------------------------------
        */

        $appointment = Appointment::with(
            'services'
        )
        ->where(
            'Appointment_ID',
            $appointmentId
        )
        ->whereHas(
            'pet',
            function ($query) use ($customerId) {

                $query->where(
                    'Customer_ID',
                    $customerId
                );
            }
        )
        ->first();


        if (!$appointment) {

            return redirect()
                ->route('appointments.index')
                ->with(
                    'error',
                    'Appointment not found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Payment
        |--------------------------------------------------------------------------
        */

        $existingPayment =
            Payment::where(
                'Appointment_ID',
                $appointmentId
            )->first();


        if ($existingPayment) {

            return redirect()
                ->route(
                    'payments.show',
                    $appointmentId
                )
                ->with(
                    'error',
                    'Payment already exists for this appointment.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Calculate Original Amount
        |--------------------------------------------------------------------------
        */

        $totalAmount =
            $appointment->services->sum(
                function ($service) {

                    return (float) $service->Price;
                }
            );


        /*
        |--------------------------------------------------------------------------
        | Calculate Maximum Loyalty Points
        |--------------------------------------------------------------------------
        */

        $availablePoints =
            (int) ($customer->Loyalty_Points ?? 0);


        $maxPointsByBill =
            (int) floor(
                $totalAmount / 10
            );


        $maxRedeemPoints =
            min(
                $availablePoints,
                $maxPointsByBill
            );


        /*
        |--------------------------------------------------------------------------
        | Validate Redeemed Points
        |--------------------------------------------------------------------------
        */

        if (
            $redeemPoints >
            $maxRedeemPoints
        ) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'You can redeem a maximum of ' .
                    $maxRedeemPoints .
                    ' loyalty points.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Calculate Discount
        |--------------------------------------------------------------------------
        */

        $loyaltyDiscount =
            $redeemPoints * 10;


        /*
        |--------------------------------------------------------------------------
        | Calculate Final Amount
        |--------------------------------------------------------------------------
        */

        $finalAmount =
            $totalAmount -
            $loyaltyDiscount;


        if ($finalAmount < 0) {

            $finalAmount = 0;
        }


        /*
        |--------------------------------------------------------------------------
        | Database Transaction
        |--------------------------------------------------------------------------
        */

        try {

            DB::beginTransaction();


            /*
            |--------------------------------------------------------------------------
            | Lock Customer
            |--------------------------------------------------------------------------
            */

            $customer =
                Customer::where(
                    'ID',
                    $customerId
                )
                ->lockForUpdate()
                ->first();


            if (!$customer) {

                throw new \Exception(
                    'Customer account not found.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Re-check Loyalty Balance
            |--------------------------------------------------------------------------
            */

            $availablePoints =
                (int) (
                    $customer->Loyalty_Points ?? 0
                );


            if (
                $redeemPoints >
                $availablePoints
            ) {

                throw new \Exception(
                    'Insufficient loyalty points.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Deduct Loyalty Points
            |--------------------------------------------------------------------------
            */

            if ($redeemPoints > 0) {

                $customer->Loyalty_Points =
                    $availablePoints -
                    $redeemPoints;

                $customer->save();


                /*
                |--------------------------------------------------------------------------
                | Create Loyalty Transaction
                |--------------------------------------------------------------------------
                */

                LoyaltyTransaction::create([

                    'Appointment_ID' =>
                        $appointmentId,

                    'Customer_ID' =>
                        $customerId,

                    'Points' =>
                        $redeemPoints,

                    'Transaction_Date' =>
                        now(),

                    'Transaction_Type' =>
                        'REDEEM',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Create Cash Payment
            |--------------------------------------------------------------------------
            |
            | Cash payment starts as PENDING.
            |
            */

            Payment::create([

                'Appointment_ID' =>
                    $appointmentId,

                'Payment_Status' =>
                    'PENDING',

                'Total_Amount' =>
                    round(
                        $finalAmount,
                        2
                    ),

                'Payment_Method' =>
                    'CASH',

                'Payment_Date' =>
                    now(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | Commit Transaction
            |--------------------------------------------------------------------------
            */

            DB::commit();


            /*
            |--------------------------------------------------------------------------
            | Redirect
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'payments.show',
                    $appointmentId
                )
                ->with(
                    'success',
                    'Cash payment selected successfully.'
                );

        } catch (\Exception $e) {

            DB::rollBack();


            return back()
                ->withInput()
                ->with(
                    'error',
                    'Failed to create payment: ' .
                    $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Process Online Payment
    |--------------------------------------------------------------------------
    |
    | This is a simulated online payment for the project.
    |
    | Customer chooses:
    |
    | CARD
    | BKASH
    | NAGAD
    |
    | Payment table stores:
    |
    | Payment_Method = ONLINE
    | Payment_Status = PAID
    |
    */

    public function online(
        Request $request,
        $appointmentId
    ) {
        $check = $this->checkCustomer($request);

        if ($check) {
            return $check;
        }


        $customerId =
            $request->session()->get('user_id');


        /*
        |--------------------------------------------------------------------------
        | Validate Online Payment Method
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'online_method' =>
                'required|in:CARD,BKASH,NAGAD',

            'redeem_points' =>
                'nullable|integer|min:0',

        ]);


        $onlineMethod =
            strtoupper(
                $request->online_method
            );


        $redeemPoints =
            (int) (
                $request->redeem_points ?? 0
            );


        /*
        |--------------------------------------------------------------------------
        | Find Customer
        |--------------------------------------------------------------------------
        */

        $customer = Customer::where(
            'ID',
            $customerId
        )->first();


        if (!$customer) {

            return redirect()
                ->route('home')
                ->with(
                    'error',
                    'Customer account not found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Find Appointment
        |--------------------------------------------------------------------------
        */

        $appointment = Appointment::with([
            'pet',
            'groomer',
            'services'
        ])
        ->where(
            'Appointment_ID',
            $appointmentId
        )
        ->whereHas(
            'pet',
            function ($query) use ($customerId) {

                $query->where(
                    'Customer_ID',
                    $customerId
                );
            }
        )
        ->first();


        if (!$appointment) {

            return redirect()
                ->route('appointments.index')
                ->with(
                    'error',
                    'Appointment not found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Payment
        |--------------------------------------------------------------------------
        */

        $existingPayment =
            Payment::where(
                'Appointment_ID',
                $appointmentId
            )->first();


        if ($existingPayment) {

            return redirect()
                ->route(
                    'payments.show',
                    $appointmentId
                )
                ->with(
                    'error',
                    'Payment already exists for this appointment.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Calculate Original Amount
        |--------------------------------------------------------------------------
        */

        $totalAmount =
            $appointment->services->sum(
                function ($service) {

                    return (float) $service->Price;
                }
            );


        /*
        |--------------------------------------------------------------------------
        | Calculate Loyalty Points
        |--------------------------------------------------------------------------
        */

        $availablePoints =
            (int) (
                $customer->Loyalty_Points ?? 0
            );


        $maxPointsByBill =
            (int) floor(
                $totalAmount / 10
            );


        $maxRedeemPoints =
            min(
                $availablePoints,
                $maxPointsByBill
            );


        /*
        |--------------------------------------------------------------------------
        | Validate Redeemed Points
        |--------------------------------------------------------------------------
        */

        if (
            $redeemPoints >
            $maxRedeemPoints
        ) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'You can redeem a maximum of ' .
                    $maxRedeemPoints .
                    ' loyalty points.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Calculate Loyalty Discount
        |--------------------------------------------------------------------------
        */

        $loyaltyDiscount =
            $redeemPoints * 10;


        /*
        |--------------------------------------------------------------------------
        | Calculate Final Amount
        |--------------------------------------------------------------------------
        */

        $finalAmount =
            $totalAmount -
            $loyaltyDiscount;


        if ($finalAmount < 0) {

            $finalAmount = 0;
        }


        /*
        |--------------------------------------------------------------------------
        | Database Transaction
        |--------------------------------------------------------------------------
        */

        try {

            DB::beginTransaction();


            /*
            |--------------------------------------------------------------------------
            | Lock Customer
            |--------------------------------------------------------------------------
            */

            $customer =
                Customer::where(
                    'ID',
                    $customerId
                )
                ->lockForUpdate()
                ->first();


            if (!$customer) {

                throw new \Exception(
                    'Customer account not found.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Re-check Loyalty Points
            |--------------------------------------------------------------------------
            */

            $availablePoints =
                (int) (
                    $customer->Loyalty_Points ?? 0
                );


            if (
                $redeemPoints >
                $availablePoints
            ) {

                throw new \Exception(
                    'Insufficient loyalty points.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Deduct Loyalty Points
            |--------------------------------------------------------------------------
            */

            if ($redeemPoints > 0) {

                $customer->Loyalty_Points =
                    $availablePoints -
                    $redeemPoints;

                $customer->save();


                /*
                |--------------------------------------------------------------------------
                | Loyalty Transaction
                |--------------------------------------------------------------------------
                */

                LoyaltyTransaction::create([

                    'Appointment_ID' =>
                        $appointmentId,

                    'Customer_ID' =>
                        $customerId,

                    'Points' =>
                        $redeemPoints,

                    'Transaction_Date' =>
                        now(),

                    'Transaction_Type' =>
                        'REDEEM',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Create Online Payment
            |--------------------------------------------------------------------------
            |
            | Payment_Method must be ONLINE because your
            | database ENUM is:
            |
            | CASH, CARD, ONLINE
            |
            */

            Payment::create([

                'Appointment_ID' =>
                    $appointmentId,

                'Payment_Status' =>
                    'PAID',

                'Total_Amount' =>
                    round(
                        $finalAmount,
                        2
                    ),

                'Payment_Method' =>
                    'ONLINE',

                'Payment_Date' =>
                    now(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | Commit
            |--------------------------------------------------------------------------
            */

            DB::commit();


            /*
            |--------------------------------------------------------------------------
            | Payment Successful
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'payments.show',
                    $appointmentId
                )
                ->with(
                    'success',
                    'Online payment completed successfully using ' .
                    $onlineMethod .
                    '.'
                );

        } catch (\Exception $e) {

            DB::rollBack();


            return back()
                ->withInput()
                ->with(
                    'error',
                    'Failed to process online payment: ' .
                    $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Payment Details
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        $appointmentId
    ) {
        $check = $this->checkCustomer($request);

        if ($check) {
            return $check;
        }


        $customerId =
            $request->session()->get('user_id');


        /*
        |--------------------------------------------------------------------------
        | Find Appointment
        |--------------------------------------------------------------------------
        */

        $appointment = Appointment::with([
            'pet',
            'groomer',
            'services'
        ])
        ->where(
            'Appointment_ID',
            $appointmentId
        )
        ->whereHas(
            'pet',
            function ($query) use ($customerId) {

                $query->where(
                    'Customer_ID',
                    $customerId
                );
            }
        )
        ->first();


        if (!$appointment) {

            return redirect()
                ->route('appointments.index')
                ->with(
                    'error',
                    'Appointment not found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Find Payment
        |--------------------------------------------------------------------------
        */

        $payment =
            Payment::where(
                'Appointment_ID',
                $appointmentId
            )->first();


        if (!$payment) {

            return redirect()
                ->route(
                    'payments.create',
                    $appointmentId
                )
                ->with(
                    'error',
                    'Payment has not been created yet.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Show Payment Details
        |--------------------------------------------------------------------------
        */

        return view(
            'payments.show',
            compact(
                'appointment',
                'payment'
            )
        );
    }
}