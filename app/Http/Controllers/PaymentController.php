<?php

namespace App\Http\Controllers;

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
    | Attach Appointment Data
    |--------------------------------------------------------------------------
    |
    | Query Builder only.
    |
    | Adds:
    | - pet
    | - groomer
    | - services
    |
    */

    private function attachAppointmentData($appointment)
    {
        if (!$appointment) {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Get Pet
        |--------------------------------------------------------------------------
        */

        $appointment->pet = DB::table('Pet')
            ->where(
                'Pet_ID',
                $appointment->Pet_ID
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Get Groomer
        |--------------------------------------------------------------------------
        */

        if (!empty($appointment->Groomer_ID)) {

            $appointment->groomer = DB::table('Groomer')
                ->where(
                    'ID',
                    $appointment->Groomer_ID
                )
                ->first();

        } else {

            $appointment->groomer = null;
        }


        /*
        |--------------------------------------------------------------------------
        | Get Services
        |--------------------------------------------------------------------------
        */

        $appointment->services = DB::table(
            'Appointment_Service as aps'
        )
        ->join(
            'Service as s',
            'aps.Service_ID',
            '=',
            's.Service_ID'
        )
        ->where(
            'aps.Appointment_ID',
            $appointment->Appointment_ID
        )
        ->select(
            's.Service_ID',
            's.Service_Name',
            's.Price',
            's.Duration',
            's.Description'
        )
        ->get();


        return $appointment;
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

        /*
        |--------------------------------------------------------------------------
        | Customer Check
        |--------------------------------------------------------------------------
        */

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

        $customer = DB::table('Customer')
            ->where(
                'ID',
                $customerId
            )
            ->first();


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
        |
        | JOIN Appointment + Pet.
        |
        */

        $appointment = DB::table(
            'Appointment as a'
        )
        ->join(
            'Pet as p',
            'a.Pet_ID',
            '=',
            'p.Pet_ID'
        )
        ->where(
            'a.Appointment_ID',
            $appointmentId
        )
        ->where(
            'p.Customer_ID',
            $customerId
        )
        ->select(
            'a.*'
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
        | Attach Pet, Groomer, Services
        |--------------------------------------------------------------------------
        */

        $this->attachAppointmentData(
            $appointment
        );


        /*
        |--------------------------------------------------------------------------
        | Check Existing Payment
        |--------------------------------------------------------------------------
        */

        $payment = DB::table('Payment')
            ->where(
                'Appointment_ID',
                $appointmentId
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Calculate Original Amount
        |--------------------------------------------------------------------------
        */

        $totalAmount = 0;

        foreach (
            $appointment->services
            as $service
        ) {

            $totalAmount +=
                (float) $service->Price;
        }


        /*
        |--------------------------------------------------------------------------
        | Loyalty Points
        |--------------------------------------------------------------------------
        */

        $loyaltyPoints =
            (int) (
                $customer->Loyalty_Points ?? 0
            );


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

        /*
        |--------------------------------------------------------------------------
        | Customer Check
        |--------------------------------------------------------------------------
        */

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

        $customer = DB::table('Customer')
            ->where(
                'ID',
                $customerId
            )
            ->first();


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

        $appointment = DB::table(
            'Appointment as a'
        )
        ->join(
            'Pet as p',
            'a.Pet_ID',
            '=',
            'p.Pet_ID'
        )
        ->where(
            'a.Appointment_ID',
            $appointmentId
        )
        ->where(
            'p.Customer_ID',
            $customerId
        )
        ->select(
            'a.*'
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
        | Attach Services
        |--------------------------------------------------------------------------
        */

        $this->attachAppointmentData(
            $appointment
        );


        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Payment
        |--------------------------------------------------------------------------
        */

        $existingPayment =
            DB::table('Payment')
                ->where(
                    'Appointment_ID',
                    $appointmentId
                )
                ->first();


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

        $totalAmount = 0;

        foreach (
            $appointment->services
            as $service
        ) {

            $totalAmount +=
                (float) $service->Price;
        }


        /*
        |--------------------------------------------------------------------------
        | Calculate Maximum Loyalty Points
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
        | Begin Transaction
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Lock Customer Row
            |--------------------------------------------------------------------------
            */

            $customer = DB::table('Customer')
                ->where(
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
            | Re-check Payment
            |--------------------------------------------------------------------------
            */

            $existingPayment =
                DB::table('Payment')
                    ->where(
                        'Appointment_ID',
                        $appointmentId
                    )
                    ->lockForUpdate()
                    ->first();


            if ($existingPayment) {

                throw new \Exception(
                    'Payment already exists for this appointment.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Deduct Loyalty Points
            |--------------------------------------------------------------------------
            */

            if ($redeemPoints > 0) {

                $newPoints =
                    $availablePoints -
                    $redeemPoints;


                DB::table('Customer')
                    ->where(
                        'ID',
                        $customerId
                    )
                    ->update([

                        'Loyalty_Points' =>
                            $newPoints,

                    ]);


                /*
                |--------------------------------------------------------------------------
                | Create Loyalty Transaction
                |--------------------------------------------------------------------------
                */

                DB::table(
                    'Loyalty_Transaction'
                )
                ->insert([

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
            */

            DB::table('Payment')
                ->insert([

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
            | Commit
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
    | Simulated online payment.
    |
    | Customer chooses:
    | CARD
    | BKASH
    | NAGAD
    |
    | Database stores Payment_Method = ONLINE.
    |
    */

    public function online(
        Request $request,
        $appointmentId
    ) {

        /*
        |--------------------------------------------------------------------------
        | Customer Check
        |--------------------------------------------------------------------------
        */

        $check = $this->checkCustomer($request);

        if ($check) {
            return $check;
        }


        $customerId =
            $request->session()->get('user_id');


        /*
        |--------------------------------------------------------------------------
        | Validate Online Payment
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

        $customer = DB::table('Customer')
            ->where(
                'ID',
                $customerId
            )
            ->first();


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

        $appointment = DB::table(
            'Appointment as a'
        )
        ->join(
            'Pet as p',
            'a.Pet_ID',
            '=',
            'p.Pet_ID'
        )
        ->where(
            'a.Appointment_ID',
            $appointmentId
        )
        ->where(
            'p.Customer_ID',
            $customerId
        )
        ->select(
            'a.*'
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
        | Attach Appointment Data
        |--------------------------------------------------------------------------
        */

        $this->attachAppointmentData(
            $appointment
        );


        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Payment
        |--------------------------------------------------------------------------
        */

        $existingPayment =
            DB::table('Payment')
                ->where(
                    'Appointment_ID',
                    $appointmentId
                )
                ->first();


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

        $totalAmount = 0;

        foreach (
            $appointment->services
            as $service
        ) {

            $totalAmount +=
                (float) $service->Price;
        }


        /*
        |--------------------------------------------------------------------------
        | Loyalty Points
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
        | Begin Transaction
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Lock Customer
            |--------------------------------------------------------------------------
            */

            $customer = DB::table('Customer')
                ->where(
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
            | Re-check Payment
            |--------------------------------------------------------------------------
            */

            $existingPayment =
                DB::table('Payment')
                    ->where(
                        'Appointment_ID',
                        $appointmentId
                    )
                    ->lockForUpdate()
                    ->first();


            if ($existingPayment) {

                throw new \Exception(
                    'Payment already exists for this appointment.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Deduct Loyalty Points
            |--------------------------------------------------------------------------
            */

            if ($redeemPoints > 0) {

                $newPoints =
                    $availablePoints -
                    $redeemPoints;


                DB::table('Customer')
                    ->where(
                        'ID',
                        $customerId
                    )
                    ->update([

                        'Loyalty_Points' =>
                            $newPoints,

                    ]);


                /*
                |--------------------------------------------------------------------------
                | Loyalty Transaction
                |--------------------------------------------------------------------------
                */

                DB::table(
                    'Loyalty_Transaction'
                )
                ->insert([

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
            | Your Payment_Method is stored as ONLINE.
            |
            */

            DB::table('Payment')
                ->insert([

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
            | Success
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

        /*
        |--------------------------------------------------------------------------
        | Customer Check
        |--------------------------------------------------------------------------
        */

        $check = $this->checkCustomer($request);

        if ($check) {
            return $check;
        }


        $customerId =
            $request->session()->get('user_id');


        /*
        |--------------------------------------------------------------------------
        | Find Customer Appointment
        |--------------------------------------------------------------------------
        */

        $appointment = DB::table(
            'Appointment as a'
        )
        ->join(
            'Pet as p',
            'a.Pet_ID',
            '=',
            'p.Pet_ID'
        )
        ->where(
            'a.Appointment_ID',
            $appointmentId
        )
        ->where(
            'p.Customer_ID',
            $customerId
        )
        ->select(
            'a.*'
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
        | Attach Pet, Groomer, Services
        |--------------------------------------------------------------------------
        */

        $this->attachAppointmentData(
            $appointment
        );


        /*
        |--------------------------------------------------------------------------
        | Find Payment
        |--------------------------------------------------------------------------
        */

        $payment = DB::table('Payment')
            ->where(
                'Appointment_ID',
                $appointmentId
            )
            ->first();


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