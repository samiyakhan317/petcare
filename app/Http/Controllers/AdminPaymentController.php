<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Check Admin Login
    |--------------------------------------------------------------------------
    */

    private function checkAdmin(Request $request)
    {
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (
            strtoupper(
                $request->session()->get('role', '')
            ) !== 'ADMIN'
        ) {
            return redirect()->route('home');
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Show All Payments
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        // Check admin access
        $check = $this->checkAdmin($request);

        if ($check) {
            return $check;
        }


        /*
        |--------------------------------------------------------------------------
        | Get All Payments
        |--------------------------------------------------------------------------
        |
        | Query Builder is used.
        | NO Eloquent ORM.
        |
        */

        $payments = DB::table('Payment as p')

            ->join(
                'Appointment as a',
                'p.Appointment_ID',
                '=',
                'a.Appointment_ID'
            )

            ->join(
                'Pet as pet',
                'a.Pet_ID',
                '=',
                'pet.Pet_ID'
            )

            ->join(
                'Customer as c',
                'pet.Customer_ID',
                '=',
                'c.ID'
            )

            ->leftJoin(
                'User as u',
                'c.ID',
                '=',
                'u.ID'
            )

            /*
            |--------------------------------------------------------------------------
            | Loyalty Redeemed Points
            |--------------------------------------------------------------------------
            */

            ->leftJoin(
                DB::raw("
                    (
                        SELECT
                            Appointment_ID,
                            Customer_ID,
                            SUM(Points) AS Redeemed_Points
                        FROM loyalty_transaction
                        WHERE Transaction_Type = 'REDEEM'
                        GROUP BY
                            Appointment_ID,
                            Customer_ID
                    ) AS lt
                "),
                function ($join) {

                    $join->on(
                        'a.Appointment_ID',
                        '=',
                        'lt.Appointment_ID'
                    );

                    $join->on(
                        'c.ID',
                        '=',
                        'lt.Customer_ID'
                    );
                }
            )

            ->select([

                // Payment
                'p.Payment_ID',
                'p.Appointment_ID',
                'p.Payment_Status',
                'p.Total_Amount',
                'p.Payment_Method',
                'p.Payment_Date',

                // Appointment
                'a.Appointment_Date',
                'a.Appointment_Time',

                // Pet
                'pet.Pet_ID',
                'pet.Name as Pet_Name',

                // Customer
                'c.ID as Customer_ID',
                'c.First_name',
                'c.Last_name',
                'u.Email',

                // Loyalty
                DB::raw(
                    'COALESCE(lt.Redeemed_Points, 0) AS Redeemed_Points'
                ),

                DB::raw(
                    'COALESCE(lt.Redeemed_Points, 0) * 10 AS Loyalty_Discount'
                ),
            ])

            ->orderBy(
                'p.Payment_ID',
                'desc'
            )

            ->get();


        /*
        |--------------------------------------------------------------------------
        | Calculate Summary
        |--------------------------------------------------------------------------
        */

        $totalPayments = $payments->count();

        $totalAmount = 0;
        $paidAmount = 0;
        $unpaidAmount = 0;
        $totalLoyaltyDiscount = 0;


        foreach ($payments as $payment) {

            $amount = (float) ($payment->Total_Amount ?? 0);

            $totalAmount += $amount;

            $status = strtoupper(
                $payment->Payment_Status ?? ''
            );


            if ($status === 'PAID') {

                $paidAmount += $amount;

            }


            if (
                $status === 'UNPAID' ||
                $status === 'PENDING'
            ) {

                $unpaidAmount += $amount;

            }


            $totalLoyaltyDiscount +=
                (float) ($payment->Loyalty_Discount ?? 0);
        }


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.payments.index',
            compact(
                'payments',
                'totalPayments',
                'totalAmount',
                'paidAmount',
                'unpaidAmount',
                'totalLoyaltyDiscount'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Mark Payment As Paid
    |--------------------------------------------------------------------------
    */

    public function markAsPaid(
        Request $request,
        $paymentId
    ) {

        // Check admin access
        $check = $this->checkAdmin($request);

        if ($check) {
            return $check;
        }


        /*
        |--------------------------------------------------------------------------
        | Find Payment
        |--------------------------------------------------------------------------
        |
        | Query Builder only.
        |
        */

        $payment = DB::table('Payment')
            ->where(
                'Payment_ID',
                $paymentId
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Payment Not Found
        |--------------------------------------------------------------------------
        */

        if (!$payment) {

            return redirect()
                ->route('admin.payments')
                ->with(
                    'error',
                    'Payment not found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Check Existing Status
        |--------------------------------------------------------------------------
        */

        if (
            strtoupper(
                $payment->Payment_Status ?? ''
            ) === 'PAID'
        ) {

            return redirect()
                ->route('admin.payments')
                ->with(
                    'error',
                    'This payment is already marked as PAID.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Update Payment
        |--------------------------------------------------------------------------
        */

        DB::table('Payment')
            ->where(
                'Payment_ID',
                $paymentId
            )
            ->update([

                'Payment_Status' => 'PAID',

                'Payment_Date' => now(),

            ]);


        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.payments')
            ->with(
                'success',
                'Payment #' .
                $paymentId .
                ' has been marked as PAID successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Show Individual Payment
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        $paymentId
    ) {

        // Check admin access
        $check = $this->checkAdmin($request);

        if ($check) {
            return $check;
        }


        /*
        |--------------------------------------------------------------------------
        | Get Payment Details
        |--------------------------------------------------------------------------
        |
        | Query Builder only.
        | NO Eloquent ORM.
        |
        */

        $payment = DB::table('Payment as p')

            ->join(
                'Appointment as a',
                'p.Appointment_ID',
                '=',
                'a.Appointment_ID'
            )

            ->join(
                'Pet as pet',
                'a.Pet_ID',
                '=',
                'pet.Pet_ID'
            )

            ->join(
                'Customer as c',
                'pet.Customer_ID',
                '=',
                'c.ID'
            )

            ->leftJoin(
                'User as u',
                'c.ID',
                '=',
                'u.ID'
            )

            /*
            |--------------------------------------------------------------------------
            | Loyalty Redeemed Points
            |--------------------------------------------------------------------------
            */

            ->leftJoin(
                DB::raw("
                    (
                        SELECT
                            Appointment_ID,
                            Customer_ID,
                            SUM(Points) AS Redeemed_Points
                        FROM loyalty_transaction
                        WHERE Transaction_Type = 'REDEEM'
                        GROUP BY
                            Appointment_ID,
                            Customer_ID
                    ) AS lt
                "),
                function ($join) {

                    $join->on(
                        'a.Appointment_ID',
                        '=',
                        'lt.Appointment_ID'
                    );

                    $join->on(
                        'c.ID',
                        '=',
                        'lt.Customer_ID'
                    );
                }
            )

            ->where(
                'p.Payment_ID',
                $paymentId
            )

            ->select([

                // Payment
                'p.Payment_ID',
                'p.Appointment_ID',
                'p.Payment_Status',
                'p.Total_Amount',
                'p.Payment_Method',
                'p.Payment_Date',

                // Appointment
                'a.Appointment_Date',
                'a.Appointment_Time',

                // Pet
                'pet.Pet_ID',
                'pet.Name as Pet_Name',
                'pet.Breed',

                // Customer
                'c.ID as Customer_ID',
                'c.First_name',
                'c.Last_name',
                'c.Address',
                'u.Email',

                // Loyalty
                DB::raw(
                    'COALESCE(lt.Redeemed_Points, 0) AS Redeemed_Points'
                ),

                DB::raw(
                    'COALESCE(lt.Redeemed_Points, 0) * 10 AS Loyalty_Discount'
                ),
            ])

            ->first();


        /*
        |--------------------------------------------------------------------------
        | Payment Not Found
        |--------------------------------------------------------------------------
        */

        if (!$payment) {

            return redirect()
                ->route('admin.payments')
                ->with(
                    'error',
                    'Payment not found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Get Services For Appointment
        |--------------------------------------------------------------------------
        */

        $services = DB::table('Service as s')

            ->join(
                'Appointment_Service as aps',
                's.Service_ID',
                '=',
                'aps.Service_ID'
            )

            ->where(
                'aps.Appointment_ID',
                $payment->Appointment_ID
            )

            ->select([

                's.Service_ID',
                's.Service_Name',
                's.Price',
                's.Duration',
                's.Description',

            ])

            ->get();


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.payments.show',
            compact(
                'payment',
                'services'
            )
        );
    }
}