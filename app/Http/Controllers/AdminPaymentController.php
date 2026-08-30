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

            return redirect()
                ->route('login');
        }

        if (
            strtoupper(
                $request->session()->get('role', '')
            ) !== 'ADMIN'
        ) {

            return redirect()
                ->route('home');
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
        /*
        |--------------------------------------------------------------------------
        | Admin Access Check
        |--------------------------------------------------------------------------
        */

        $check = $this->checkAdmin($request);

        if ($check) {
            return $check;
        }


        /*
        |--------------------------------------------------------------------------
        | Get All Payment Information
        |--------------------------------------------------------------------------
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
                    ) as lt
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

                /*
                | Payment
                */

                'p.Payment_ID',
                'p.Appointment_ID',
                'p.Payment_Status',
                'p.Total_Amount',
                'p.Payment_Method',
                'p.Payment_Date',


                /*
                | Appointment
                */

                'a.Appointment_Date',
                'a.Appointment_Time',


                /*
                | Pet
                */

                'pet.Pet_ID',
                'pet.Name as Pet_Name',


                /*
                | Customer
                */

                'c.ID as Customer_ID',
                'c.First_name',
                'c.Last_name',
                'u.Email',


                /*
                | Loyalty
                */

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
        | Summary
        |--------------------------------------------------------------------------
        */

        $totalPayments =
            $payments->count();


        $totalAmount =
            $payments->sum(
                function ($payment) {

                    return (float)
                        $payment->Total_Amount;
                }
            );


        /*
        |--------------------------------------------------------------------------
        | Paid Amount
        |--------------------------------------------------------------------------
        */

        $paidAmount =
            $payments
                ->filter(
                    function ($payment) {

                        return strtoupper(
                            $payment->Payment_Status ?? ''
                        ) === 'PAID';
                    }
                )
                ->sum(
                    function ($payment) {

                        return (float)
                            $payment->Total_Amount;
                    }
                );


        /*
        |--------------------------------------------------------------------------
        | Pending / Unpaid Amount
        |--------------------------------------------------------------------------
        */

        $unpaidAmount =
            $payments
                ->filter(
                    function ($payment) {

                        return in_array(
                            strtoupper(
                                $payment->Payment_Status ?? ''
                            ),
                            [
                                'UNPAID',
                                'PENDING'
                            ]
                        );
                    }
                )
                ->sum(
                    function ($payment) {

                        return (float)
                            $payment->Total_Amount;
                    }
                );


        /*
        |--------------------------------------------------------------------------
        | Total Loyalty Discount
        |--------------------------------------------------------------------------
        */

        $totalLoyaltyDiscount =
            $payments->sum(
                function ($payment) {

                    return (float)
                        $payment->Loyalty_Discount;
                }
            );


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
    |
    | Admin uses this for cash payments after receiving the money.
    |
    | PENDING / UNPAID
    |        ↓
    |      PAID
    |
    */

    public function markAsPaid(
        Request $request,
        $paymentId
    ) {
        /*
        |--------------------------------------------------------------------------
        | Admin Access Check
        |--------------------------------------------------------------------------
        */

        $check = $this->checkAdmin($request);

        if ($check) {
            return $check;
        }


        /*
        |--------------------------------------------------------------------------
        | Find Payment
        |--------------------------------------------------------------------------
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
        | Check Current Status
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
        | Success Message
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.payments')
            ->with(
                'success',
                'Payment #' . $paymentId . ' has been marked as PAID successfully.'
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
        /*
        |--------------------------------------------------------------------------
        | Admin Access Check
        |--------------------------------------------------------------------------
        */

        $check = $this->checkAdmin($request);

        if ($check) {
            return $check;
        }


        /*
        |--------------------------------------------------------------------------
        | Get Payment Details
        |--------------------------------------------------------------------------
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
                    ) as lt
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

                /*
                | Payment
                */

                'p.Payment_ID',
                'p.Appointment_ID',
                'p.Payment_Status',
                'p.Total_Amount',
                'p.Payment_Method',
                'p.Payment_Date',


                /*
                | Appointment
                */

                'a.Appointment_Date',
                'a.Appointment_Time',


                /*
                | Pet
                */

                'pet.Pet_ID',
                'pet.Name as Pet_Name',
                'pet.Breed',


                /*
                | Customer
                */

                'c.ID as Customer_ID',
                'c.First_name',
                'c.Last_name',
                'c.Address',
                'u.Email',


                /*
                | Loyalty
                */

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
        | Get Services For This Appointment
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
        | Return Payment Details View
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
