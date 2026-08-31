<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroomingReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Check Customer
    |--------------------------------------------------------------------------
    */

    private function checkCustomer(Request $request)
    {
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (
            strtoupper(
                $request->session()->get('role', '')
            ) !== 'CUSTOMER'
        ) {
            return redirect()->route('home');
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Check Groomer
    |--------------------------------------------------------------------------
    */

    private function checkGroomer(Request $request)
    {
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (
            strtoupper(
                $request->session()->get('role', '')
            ) !== 'GROOMER'
        ) {
            return redirect()->route('home');
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Add Pet And Groomer Data
    |--------------------------------------------------------------------------
    |
    | Query Builder only.
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
    | Show Grooming Reports
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Login Check
        |--------------------------------------------------------------------------
        */

        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }


        $role = strtoupper(
            $request->session()->get('role', '')
        );

        $userId =
            $request->session()->get('user_id');


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        if ($role === 'CUSTOMER') {

            /*
            |--------------------------------------------------------------------------
            | Get Customer Appointments
            |--------------------------------------------------------------------------
            */

            $appointments = DB::table('Appointment as a')
                ->join(
                    'Pet as p',
                    'a.Pet_ID',
                    '=',
                    'p.Pet_ID'
                )
                ->where(
                    'p.Customer_ID',
                    $userId
                )
                ->select(
                    'a.*'
                )
                ->orderBy(
                    'a.Appointment_Date',
                    'desc'
                )
                ->orderBy(
                    'a.Appointment_Time',
                    'desc'
                )
                ->get();


            /*
            |--------------------------------------------------------------------------
            | Attach Pet And Groomer
            |--------------------------------------------------------------------------
            */

            foreach ($appointments as $appointment) {

                $this->attachAppointmentData(
                    $appointment
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Get Reports
            |--------------------------------------------------------------------------
            */

            $appointmentIds =
                $appointments
                    ->pluck('Appointment_ID')
                    ->toArray();


            if (!empty($appointmentIds)) {

                $reports = DB::table(
                    'Grooming_Report'
                )
                ->whereIn(
                    'Appointment_ID',
                    $appointmentIds
                )
                ->get()
                ->keyBy(
                    'Appointment_ID'
                );

            } else {

                $reports = collect();
            }


            return view(
                'grooming-reports.index',
                compact(
                    'appointments',
                    'reports'
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | GROOMER
        |--------------------------------------------------------------------------
        */

        if ($role === 'GROOMER') {

            /*
            |--------------------------------------------------------------------------
            | Get Assigned Appointments
            |--------------------------------------------------------------------------
            */

            $appointments = DB::table(
                'Appointment'
            )
            ->where(
                'Groomer_ID',
                $userId
            )
            ->orderBy(
                'Appointment_Date',
                'desc'
            )
            ->orderBy(
                'Appointment_Time',
                'desc'
            )
            ->get();


            /*
            |--------------------------------------------------------------------------
            | Attach Pet And Groomer
            |--------------------------------------------------------------------------
            */

            foreach ($appointments as $appointment) {

                $this->attachAppointmentData(
                    $appointment
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Get Reports
            |--------------------------------------------------------------------------
            */

            $appointmentIds =
                $appointments
                    ->pluck('Appointment_ID')
                    ->toArray();


            if (!empty($appointmentIds)) {

                $reports = DB::table(
                    'Grooming_Report'
                )
                ->whereIn(
                    'Appointment_ID',
                    $appointmentIds
                )
                ->get()
                ->keyBy(
                    'Appointment_ID'
                );

            } else {

                $reports = collect();
            }


            return view(
                'grooming-reports.index',
                compact(
                    'appointments',
                    'reports'
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Unknown Role
        |--------------------------------------------------------------------------
        */

        return redirect()->route('home');
    }


    /*
    |--------------------------------------------------------------------------
    | Customer View Report
    |--------------------------------------------------------------------------
    */

    public function viewReport(
        Request $request,
        $appointmentId
    ) {

        /*
        |--------------------------------------------------------------------------
        | Customer Check
        |--------------------------------------------------------------------------
        */

        $check =
            $this->checkCustomer($request);

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


        /*
        |--------------------------------------------------------------------------
        | Appointment Not Found
        |--------------------------------------------------------------------------
        */

        if (!$appointment) {

            return redirect()
                ->route(
                    'grooming-reports.index'
                )
                ->with(
                    'error',
                    'Appointment not found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Attach Related Data
        |--------------------------------------------------------------------------
        */

        $this->attachAppointmentData(
            $appointment
        );


        /*
        |--------------------------------------------------------------------------
        | Get Report
        |--------------------------------------------------------------------------
        */

        $report = DB::table(
            'Grooming_Report'
        )
        ->where(
            'Appointment_ID',
            $appointmentId
        )
        ->first();


        /*
        |--------------------------------------------------------------------------
        | Report Not Available
        |--------------------------------------------------------------------------
        */

        if (!$report) {

            return redirect()
                ->route(
                    'grooming-reports.index'
                )
                ->with(
                    'error',
                    'Grooming report is not available yet.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'grooming-reports.view',
            compact(
                'appointment',
                'report'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Show Create Report Form
    |--------------------------------------------------------------------------
    */

    public function create(
        Request $request,
        $appointmentId
    ) {

        /*
        |--------------------------------------------------------------------------
        | Groomer Check
        |--------------------------------------------------------------------------
        */

        $check =
            $this->checkGroomer($request);

        if ($check) {
            return $check;
        }


        $groomerId =
            $request->session()->get('user_id');


        /*
        |--------------------------------------------------------------------------
        | Find Assigned Appointment
        |--------------------------------------------------------------------------
        */

        $appointment = DB::table(
            'Appointment'
        )
        ->where(
            'Appointment_ID',
            $appointmentId
        )
        ->where(
            'Groomer_ID',
            $groomerId
        )
        ->first();


        /*
        |--------------------------------------------------------------------------
        | Appointment Not Found
        |--------------------------------------------------------------------------
        */

        if (!$appointment) {

            return redirect()
                ->route(
                    'grooming-reports.index'
                )
                ->with(
                    'error',
                    'Appointment not found or not assigned to you.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Attach Pet And Groomer
        |--------------------------------------------------------------------------
        */

        $this->attachAppointmentData(
            $appointment
        );


        /*
        |--------------------------------------------------------------------------
        | Check Existing Report
        |--------------------------------------------------------------------------
        */

        $report = DB::table(
            'Grooming_Report'
        )
        ->where(
            'Appointment_ID',
            $appointmentId
        )
        ->first();


        /*
        |--------------------------------------------------------------------------
        | Report Already Exists
        |--------------------------------------------------------------------------
        */

        if ($report) {

            return redirect()
                ->route(
                    'grooming-reports.edit',
                    $appointmentId
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Return Create View
        |--------------------------------------------------------------------------
        */

        return view(
            'grooming-reports.create',
            compact(
                'appointment'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store Grooming Report
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        $appointmentId
    ) {

        /*
        |--------------------------------------------------------------------------
        | Groomer Check
        |--------------------------------------------------------------------------
        */

        $check =
            $this->checkGroomer($request);

        if ($check) {
            return $check;
        }


        $groomerId =
            $request->session()->get('user_id');


        /*
        |--------------------------------------------------------------------------
        | Find Assigned Appointment
        |--------------------------------------------------------------------------
        */

        $appointment = DB::table(
            'Appointment'
        )
        ->where(
            'Appointment_ID',
            $appointmentId
        )
        ->where(
            'Groomer_ID',
            $groomerId
        )
        ->first();


        /*
        |--------------------------------------------------------------------------
        | Appointment Not Found
        |--------------------------------------------------------------------------
        */

        if (!$appointment) {

            return redirect()
                ->route(
                    'grooming-reports.index'
                )
                ->with(
                    'error',
                    'Appointment not found or not assigned to you.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'Coat_Condition' =>
                'nullable|string|max:255',

            'Skin_Condition' =>
                'nullable|string|max:255',

            'Ear_Cleaning' =>
                'nullable|string|max:255',

            'Nail_Trimming' =>
                'nullable|string|max:255',

            'Recommendation' =>
                'nullable|string',

            'Groomer_Notes' =>
                'nullable|string',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Check Existing Report
        |--------------------------------------------------------------------------
        */

        $existingReport = DB::table(
            'Grooming_Report'
        )
        ->where(
            'Appointment_ID',
            $appointmentId
        )
        ->first();


        if ($existingReport) {

            return redirect()
                ->route(
                    'grooming-reports.edit',
                    $appointmentId
                )
                ->with(
                    'error',
                    'A grooming report already exists for this appointment.'
                );
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
            | STEP 1: Create Grooming Report
            |--------------------------------------------------------------------------
            */

            DB::table(
                'Grooming_Report'
            )
            ->insert([

                'Appointment_ID' =>
                    $appointmentId,

                'Coat_Condition' =>
                    $request->Coat_Condition,

                'Skin_Condition' =>
                    $request->Skin_Condition,

                'Ear_Cleaning' =>
                    $request->Ear_Cleaning,

                'Nail_Trimming' =>
                    $request->Nail_Trimming,

                'Recommendation' =>
                    $request->Recommendation,

                'Groomer_Notes' =>
                    $request->Groomer_Notes,

                'Created_At' =>
                    now(),

            ]);


            /*
            |--------------------------------------------------------------------------
            | STEP 2: Mark Appointment Completed
            |--------------------------------------------------------------------------
            */

            DB::table(
                'Appointment'
            )
            ->where(
                'Appointment_ID',
                $appointmentId
            )
            ->update([

                'Status' =>
                    'COMPLETED',

            ]);


            /*
            |--------------------------------------------------------------------------
            | STEP 3: Get Customer ID
            |--------------------------------------------------------------------------
            */

            $pet = DB::table(
                'Pet'
            )
            ->where(
                'Pet_ID',
                $appointment->Pet_ID
            )
            ->first();


            $customerId =
                $pet
                    ? $pet->Customer_ID
                    : null;


            /*
            |--------------------------------------------------------------------------
            | STEP 4: Calculate Service Price
            |--------------------------------------------------------------------------
            */

            if ($customerId) {

                $services = DB::table(
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
                    $appointmentId
                )
                ->select(
                    's.Price'
                )
                ->get();


                $totalPrice = 0;


                foreach ($services as $service) {

                    $totalPrice +=
                        (float) $service->Price;
                }


                /*
                |--------------------------------------------------------------------------
                | STEP 5: Calculate Loyalty Points
                |--------------------------------------------------------------------------
                |
                | 1 point for every 100 BDT.
                |
                */

                $points = (int) floor(
                    $totalPrice / 100
                );


                /*
                |--------------------------------------------------------------------------
                | STEP 6: Add Loyalty Points
                |--------------------------------------------------------------------------
                */

                if ($points > 0) {

                    /*
                    |--------------------------------------------------------------------------
                    | Lock Customer Row
                    |--------------------------------------------------------------------------
                    */

                    $customer = DB::table(
                        'Customer'
                    )
                    ->where(
                        'ID',
                        $customerId
                    )
                    ->lockForUpdate()
                    ->first();


                    if ($customer) {

                        $newPoints =
                            (int) $customer->Loyalty_Points
                            + $points;


                        DB::table(
                            'Customer'
                        )
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
                        | STEP 7: Loyalty Transaction
                        |--------------------------------------------------------------------------
                        */

                        DB::table(
                            'loyalty_transaction'
                        )
                        ->insert([

                            'Appointment_ID' =>
                                $appointmentId,

                            'Customer_ID' =>
                                $customerId,

                            'Points' =>
                                $points,

                            'Transaction_Date' =>
                                now(),

                            'Transaction_Type' =>
                                'EARNED',

                        ]);
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Commit Transaction
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
                    'grooming-reports.index'
                )
                ->with(
                    'success',
                    'Grooming report created successfully.'
                );


        } catch (\Exception $e) {

            /*
            |--------------------------------------------------------------------------
            | Rollback
            |--------------------------------------------------------------------------
            */

            DB::rollBack();


            return back()
                ->withInput()
                ->with(
                    'error',
                    'Failed to create grooming report: ' .
                    $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Grooming Report
    |--------------------------------------------------------------------------
    */

    public function edit(
        Request $request,
        $appointmentId
    ) {

        /*
        |--------------------------------------------------------------------------
        | Groomer Check
        |--------------------------------------------------------------------------
        */

        $check =
            $this->checkGroomer($request);

        if ($check) {
            return $check;
        }


        $groomerId =
            $request->session()->get('user_id');


        /*
        |--------------------------------------------------------------------------
        | Find Assigned Appointment
        |--------------------------------------------------------------------------
        */

        $appointment = DB::table(
            'Appointment'
        )
        ->where(
            'Appointment_ID',
            $appointmentId
        )
        ->where(
            'Groomer_ID',
            $groomerId
        )
        ->first();


        /*
        |--------------------------------------------------------------------------
        | Appointment Not Found
        |--------------------------------------------------------------------------
        */

        if (!$appointment) {

            return redirect()
                ->route(
                    'grooming-reports.index'
                )
                ->with(
                    'error',
                    'Appointment not found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Attach Pet And Groomer
        |--------------------------------------------------------------------------
        */

        $this->attachAppointmentData(
            $appointment
        );


        /*
        |--------------------------------------------------------------------------
        | Get Report
        |--------------------------------------------------------------------------
        */

        $report = DB::table(
            'Grooming_Report'
        )
        ->where(
            'Appointment_ID',
            $appointmentId
        )
        ->first();


        /*
        |--------------------------------------------------------------------------
        | Report Not Found
        |--------------------------------------------------------------------------
        */

        if (!$report) {

            return redirect()
                ->route(
                    'grooming-reports.create',
                    $appointmentId
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Return Edit View
        |--------------------------------------------------------------------------
        */

        return view(
            'grooming-reports.edit',
            compact(
                'appointment',
                'report'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Grooming Report
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $appointmentId
    ) {

        /*
        |--------------------------------------------------------------------------
        | Groomer Check
        |--------------------------------------------------------------------------
        */

        $check =
            $this->checkGroomer($request);

        if ($check) {
            return $check;
        }


        $groomerId =
            $request->session()->get('user_id');


        /*
        |--------------------------------------------------------------------------
        | Check Appointment
        |--------------------------------------------------------------------------
        */

        $appointment = DB::table(
            'Appointment'
        )
        ->where(
            'Appointment_ID',
            $appointmentId
        )
        ->where(
            'Groomer_ID',
            $groomerId
        )
        ->first();


        /*
        |--------------------------------------------------------------------------
        | Appointment Not Found
        |--------------------------------------------------------------------------
        */

        if (!$appointment) {

            return redirect()
                ->route(
                    'grooming-reports.index'
                )
                ->with(
                    'error',
                    'Appointment not found.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Get Report
        |--------------------------------------------------------------------------
        */

        $report = DB::table(
            'Grooming_Report'
        )
        ->where(
            'Appointment_ID',
            $appointmentId
        )
        ->first();


        /*
        |--------------------------------------------------------------------------
        | Report Not Found
        |--------------------------------------------------------------------------
        */

        if (!$report) {

            return redirect()
                ->route(
                    'grooming-reports.create',
                    $appointmentId
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'Coat_Condition' =>
                'nullable|string|max:255',

            'Skin_Condition' =>
                'nullable|string|max:255',

            'Ear_Cleaning' =>
                'nullable|string|max:255',

            'Nail_Trimming' =>
                'nullable|string|max:255',

            'Recommendation' =>
                'nullable|string',

            'Groomer_Notes' =>
                'nullable|string',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Update Report
        |--------------------------------------------------------------------------
        */

        try {

            DB::table(
                'Grooming_Report'
            )
            ->where(
                'Appointment_ID',
                $appointmentId
            )
            ->update([

                'Coat_Condition' =>
                    $request->Coat_Condition,

                'Skin_Condition' =>
                    $request->Skin_Condition,

                'Ear_Cleaning' =>
                    $request->Ear_Cleaning,

                'Nail_Trimming' =>
                    $request->Nail_Trimming,

                'Recommendation' =>
                    $request->Recommendation,

                'Groomer_Notes' =>
                    $request->Groomer_Notes,

            ]);


            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'grooming-reports.index'
                )
                ->with(
                    'success',
                    'Grooming report updated successfully.'
                );


        } catch (\Exception $e) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Failed to update grooming report: ' .
                    $e->getMessage()
                );
        }
    }
}