<?php

namespace App\Http\Controllers;

use App\Models\GroomingReport;
use App\Models\Appointment;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroomingReportController extends Controller
{
    private function checkCustomer(Request $request)
    {
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (
            strtoupper($request->session()->get('role', '')) !== 'CUSTOMER'
        ) {
            return redirect()->route('home');
        }

        return null;
    }


    private function checkGroomer(Request $request)
    {
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (
            strtoupper($request->session()->get('role', '')) !== 'GROOMER'
        ) {
            return redirect()->route('home');
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Show Grooming Reports
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }

        $role = strtoupper($request->session()->get('role', ''));
        $userId = $request->session()->get('user_id');


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        if ($role === 'CUSTOMER') {

            $appointments = Appointment::with('pet')
                ->whereHas('pet', function ($query) use ($userId) {
                    $query->where('Customer_ID', $userId);
                })
                ->orderBy('Appointment_Date', 'desc')
                ->orderBy('Appointment_Time', 'desc')
                ->get();


            $reports = GroomingReport::whereIn(
                    'Appointment_ID',
                    $appointments->pluck('Appointment_ID')
                )
                ->get()
                ->keyBy('Appointment_ID');


            return view(
                'grooming-reports.index',
                compact('appointments', 'reports')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | GROOMER
        |--------------------------------------------------------------------------
        */

        if ($role === 'GROOMER') {

            $appointments = Appointment::with('pet')
                ->where('Groomer_ID', $userId)
                ->orderBy('Appointment_Date', 'desc')
                ->orderBy('Appointment_Time', 'desc')
                ->get();


            $reports = GroomingReport::whereIn(
                    'Appointment_ID',
                    $appointments->pluck('Appointment_ID')
                )
                ->get()
                ->keyBy('Appointment_ID');


            return view(
                'grooming-reports.index',
                compact('appointments', 'reports')
            );
        }


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
        $check = $this->checkCustomer($request);

        if ($check) {
            return $check;
        }

        $customerId = $request->session()->get('user_id');


        $appointment = Appointment::with([
            'pet',
            'groomer'
        ])
        ->where('Appointment_ID', $appointmentId)
        ->whereHas('pet', function ($query) use ($customerId) {
            $query->where('Customer_ID', $customerId);
        })
        ->first();


        if (!$appointment) {
            return redirect()
                ->route('grooming-reports.index')
                ->with(
                    'error',
                    'Appointment not found.'
                );
        }


        $report = GroomingReport::where(
            'Appointment_ID',
            $appointmentId
        )->first();


        if (!$report) {
            return redirect()
                ->route('grooming-reports.index')
                ->with(
                    'error',
                    'Grooming report is not available yet.'
                );
        }


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
        $check = $this->checkGroomer($request);

        if ($check) {
            return $check;
        }

        $groomerId = $request->session()->get('user_id');


        $appointment = Appointment::with('pet')
            ->where('Appointment_ID', $appointmentId)
            ->where('Groomer_ID', $groomerId)
            ->first();


        if (!$appointment) {
            return redirect()
                ->route('grooming-reports.index')
                ->with(
                    'error',
                    'Appointment not found or not assigned to you.'
                );
        }


        $report = GroomingReport::where(
            'Appointment_ID',
            $appointmentId
        )->first();


        if ($report) {
            return redirect()
                ->route(
                    'grooming-reports.edit',
                    $appointmentId
                );
        }


        return view(
            'grooming-reports.create',
            compact('appointment')
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
        $check = $this->checkGroomer($request);

        if ($check) {
            return $check;
        }

        $groomerId = $request->session()->get('user_id');


        /*
        |--------------------------------------------------------------------------
        | Find Appointment
        |--------------------------------------------------------------------------
        */

        $appointment = Appointment::with('pet')
            ->where('Appointment_ID', $appointmentId)
            ->where('Groomer_ID', $groomerId)
            ->first();


        if (!$appointment) {
            return redirect()
                ->route('grooming-reports.index')
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
            'Coat_Condition' => 'nullable|string|max:255',
            'Skin_Condition' => 'nullable|string|max:255',
            'Ear_Cleaning' => 'nullable|string|max:255',
            'Nail_Trimming' => 'nullable|string|max:255',
            'Recommendation' => 'nullable|string',
            'Groomer_Notes' => 'nullable|string',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Report
        |--------------------------------------------------------------------------
        */

        $existingReport = GroomingReport::where(
            'Appointment_ID',
            $appointmentId
        )->first();


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
        | Create Report + Loyalty Points
        |--------------------------------------------------------------------------
        */

        try {

            DB::beginTransaction();


            /*
            |--------------------------------------------------------------------------
            | 1. Create Grooming Report
            |--------------------------------------------------------------------------
            */

            GroomingReport::create([
                'Appointment_ID' => $appointmentId,
                'Coat_Condition' => $request->Coat_Condition,
                'Skin_Condition' => $request->Skin_Condition,
                'Ear_Cleaning' => $request->Ear_Cleaning,
                'Nail_Trimming' => $request->Nail_Trimming,
                'Recommendation' => $request->Recommendation,
                'Groomer_Notes' => $request->Groomer_Notes,
                'Created_At' => now(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | 2. Mark Appointment Completed
            |--------------------------------------------------------------------------
            */

            $appointment->Status = 'Completed';
            $appointment->save();


            /*
            |--------------------------------------------------------------------------
            | 3. Get Customer ID from Pet
            |--------------------------------------------------------------------------
            */

            $customerId = $appointment->pet
                ? $appointment->pet->Customer_ID
                : null;


            if ($customerId) {

                /*
                |--------------------------------------------------------------------------
                | 4. Calculate Total Service Price
                |--------------------------------------------------------------------------
                */

                $services = $appointment->services()->get();

                $totalPrice = 0;

                foreach ($services as $service) {
                    $totalPrice += (float) $service->Price;
                }


                /*
                |--------------------------------------------------------------------------
                | 5. Calculate Loyalty Points
                |
                | 1 point for every 100 BDT
                |--------------------------------------------------------------------------
                */

                $points = (int) floor(
                    $totalPrice / 100
                );


                /*
                |--------------------------------------------------------------------------
                | 6. Add Loyalty Points
                |--------------------------------------------------------------------------
                */

                if ($points > 0) {

                    $customer = Customer::where(
                        'ID',
                        $customerId
                    )->lockForUpdate()->first();


                    if ($customer) {

                        $customer->Loyalty_Points =
                            (int) $customer->Loyalty_Points
                            + $points;

                        $customer->save();


                        /*
                        |--------------------------------------------------------------------------
                        | 7. Create Loyalty Transaction
                        |--------------------------------------------------------------------------
                        */

                        DB::table('loyalty_transaction')->insert([
                            'Appointment_ID' => $appointmentId,
                            'Customer_ID' => $customerId,
                            'Points' => $points,
                            'Transaction_Date' => now(),
                            'Transaction_Type' => 'EARNED',
                        ]);
                    }
                }
            }


            DB::commit();


            return redirect()
                ->route('grooming-reports.index')
                ->with(
                    'success',
                    'Grooming report created successfully.'
                );

        } catch (\Exception $e) {

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
        $check = $this->checkGroomer($request);

        if ($check) {
            return $check;
        }

        $groomerId = $request->session()->get('user_id');


        $appointment = Appointment::with('pet')
            ->where('Appointment_ID', $appointmentId)
            ->where('Groomer_ID', $groomerId)
            ->first();


        if (!$appointment) {
            return redirect()
                ->route('grooming-reports.index')
                ->with(
                    'error',
                    'Appointment not found.'
                );
        }


        $report = GroomingReport::where(
            'Appointment_ID',
            $appointmentId
        )->first();


        if (!$report) {
            return redirect()
                ->route(
                    'grooming-reports.create',
                    $appointmentId
                );
        }


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
        $check = $this->checkGroomer($request);

        if ($check) {
            return $check;
        }

        $groomerId = $request->session()->get('user_id');


        $appointment = Appointment::where(
                'Appointment_ID',
                $appointmentId
            )
            ->where(
                'Groomer_ID',
                $groomerId
            )
            ->first();


        if (!$appointment) {
            return redirect()
                ->route('grooming-reports.index')
                ->with(
                    'error',
                    'Appointment not found.'
                );
        }


        $report = GroomingReport::where(
            'Appointment_ID',
            $appointmentId
        )->first();


        if (!$report) {
            return redirect()
                ->route(
                    'grooming-reports.create',
                    $appointmentId
                );
        }


        $request->validate([
            'Coat_Condition' => 'nullable|string|max:255',
            'Skin_Condition' => 'nullable|string|max:255',
            'Ear_Cleaning' => 'nullable|string|max:255',
            'Nail_Trimming' => 'nullable|string|max:255',
            'Recommendation' => 'nullable|string',
            'Groomer_Notes' => 'nullable|string',
        ]);


        try {

            $report->update([
                'Coat_Condition' => $request->Coat_Condition,
                'Skin_Condition' => $request->Skin_Condition,
                'Ear_Cleaning' => $request->Ear_Cleaning,
                'Nail_Trimming' => $request->Nail_Trimming,
                'Recommendation' => $request->Recommendation,
                'Groomer_Notes' => $request->Groomer_Notes,
            ]);


            return redirect()
                ->route('grooming-reports.index')
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