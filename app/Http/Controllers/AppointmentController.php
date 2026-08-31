<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
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
    | Add Related Data To Appointment
    |--------------------------------------------------------------------------
    |
    | We are NOT using Eloquent relationships here.
    |
    | Query Builder returns stdClass objects.
    | We manually attach:
    |
    | - pet
    | - groomer
    | - services
    | - payment
    |
    | This allows the existing Blade views to continue using:
    |
    | $appointment->pet
    | $appointment->groomer
    | $appointment->services
    | $appointment->payment
    |
    */

    private function attachAppointmentData($appointment)
    {
        if (!$appointment) {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Pet
        |--------------------------------------------------------------------------
        */

        $appointment->pet = DB::table('pet')
            ->where(
                'Pet_ID',
                $appointment->Pet_ID
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Groomer
        |--------------------------------------------------------------------------
        */

        if (!empty($appointment->Groomer_ID)) {

            $appointment->groomer = DB::table('groomer')
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
        | Services
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


        /*
        |--------------------------------------------------------------------------
        | Payment
        |--------------------------------------------------------------------------
        */

        $appointment->payment = DB::table('payment')
            ->where(
                'Appointment_ID',
                $appointment->Appointment_ID
            )
            ->first();


        return $appointment;
    }


    /*
    |--------------------------------------------------------------------------
    | Appointment List
    |--------------------------------------------------------------------------
    |
    | CUSTOMER:
    | Shows only appointments belonging to logged-in customer.
    |
    | GROOMER:
    | Shows only appointments assigned to logged-in groomer.
    |
    | ADMIN:
    | Shows all appointments.
    |
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


        $userId = $request->session()->get('user_id');

        $role = strtoupper(
            $request->session()->get('role', '')
        );


        /*
        |--------------------------------------------------------------------------
        | Base Appointment Query
        |--------------------------------------------------------------------------
        */

        $query = DB::table('appointment');


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        if ($role === 'CUSTOMER') {

            /*
             * Customer can only see appointments
             * belonging to their own pets.
             */

            $query
                ->join(
                    'pet',
                    'appointment.Pet_ID',
                    '=',
                    'pet.Pet_ID'
                )
                ->where(
                    'pet.Customer_ID',
                    $userId
                )
                ->select(
                    'appointment.*'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | GROOMER
        |--------------------------------------------------------------------------
        */

        elseif ($role === 'GROOMER') {

            $query
                ->where(
                    'Groomer_ID',
                    $userId
                )
                ->select(
                    'appointment.*'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        elseif ($role === 'ADMIN') {

            $query->select(
                'appointment.*'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | UNKNOWN ROLE
        |--------------------------------------------------------------------------
        */

        else {

            return redirect()->route('home');
        }


        /*
        |--------------------------------------------------------------------------
        | Order Appointments
        |--------------------------------------------------------------------------
        */

        $appointments = $query
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
        | Attach Related Data
        |--------------------------------------------------------------------------
        */

        foreach ($appointments as $appointment) {

            $this->attachAppointmentData(
                $appointment
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'appointments.index',
            compact('appointments')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create Appointment
    |--------------------------------------------------------------------------
    |
    | CUSTOMER ONLY
    |
    */

    public function create(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Customer Check
        |--------------------------------------------------------------------------
        */

        $check = $this->checkCustomer($request);

        if ($check) {
            return $check;
        }


        /*
        |--------------------------------------------------------------------------
        | Customer ID
        |--------------------------------------------------------------------------
        */

        $customerId =
            $request->session()->get('user_id');


        /*
        |--------------------------------------------------------------------------
        | Get Customer Pets
        |--------------------------------------------------------------------------
        */

        $pets = DB::table('pet')
            ->where(
                'Customer_ID',
                $customerId
            )
            ->orderBy(
                'Name'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Get Active Services
        |--------------------------------------------------------------------------
        */

        $services = DB::table('service')
            ->where(
                'Status',
                'ACTIVE'
            )
            ->orderBy(
                'Service_Name'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Get Groomers
        |--------------------------------------------------------------------------
        */

        $groomers = DB::table('groomer')
            ->orderBy(
                'Name'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Selected Service
        |--------------------------------------------------------------------------
        */

        $selectedServiceId =
            $request->query('service_id');


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'appointments.create',
            compact(
                'pets',
                'services',
                'groomers',
                'selectedServiceId'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store Appointment
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Customer Check
        |--------------------------------------------------------------------------
        */

        $check = $this->checkCustomer($request);

        if ($check) {
            return $check;
        }


        /*
        |--------------------------------------------------------------------------
        | Validate
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'Pet_ID' => [
                'required',
                'integer'
            ],

            'Service_ID' => [
                'required',
                'integer'
            ],

            'Groomer_ID' => [
                'required',
                'integer'
            ],

            'Appointment_Date' => [
                'required',
                'date'
            ],

            'Appointment_Time' => [
                'required'
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Customer ID
        |--------------------------------------------------------------------------
        */

        $customerId =
            $request->session()->get('user_id');


        /*
        |--------------------------------------------------------------------------
        | Check Pet Belongs To Customer
        |--------------------------------------------------------------------------
        */

        $pet = DB::table('pet')
            ->where(
                'Pet_ID',
                $validated['Pet_ID']
            )
            ->where(
                'Customer_ID',
                $customerId
            )
            ->first();


        if (!$pet) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Invalid pet selected.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Check Service
        |--------------------------------------------------------------------------
        */

        $service = DB::table('service')
            ->where(
                'Service_ID',
                $validated['Service_ID']
            )
            ->where(
                'Status',
                'ACTIVE'
            )
            ->first();


        if (!$service) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Invalid or inactive service selected.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Check Groomer
        |--------------------------------------------------------------------------
        */

        $groomer = DB::table('groomer')
            ->where(
                'ID',
                $validated['Groomer_ID']
            )
            ->first();


        if (!$groomer) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Invalid groomer selected.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Check Duplicate Appointment
        |--------------------------------------------------------------------------
        */

        $duplicate = DB::table('appointment')
            ->where(
                'Pet_ID',
                $validated['Pet_ID']
            )
            ->where(
                'Appointment_Date',
                $validated['Appointment_Date']
            )
            ->where(
                'Appointment_Time',
                $validated['Appointment_Time']
            )
            ->whereNotIn(
                'Status',
                [
                    'CANCELLED'
                ]
            )
            ->exists();


        if ($duplicate) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'This pet already has an appointment at this date and time.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Create Appointment
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        try {

            /*
             * insertGetId() returns the newly created
             * Appointment_ID.
             */

            $appointmentId = DB::table('appointment')
                ->insertGetId([

                    'Pet_ID' =>
                        $validated['Pet_ID'],

                    'Groomer_ID' =>
                        $validated['Groomer_ID'],

                    'Appointment_Date' =>
                        $validated['Appointment_Date'],

                    'Appointment_Time' =>
                        $validated['Appointment_Time'],

                    'Status' =>
                        'PENDING',

                ]);


            /*
            |--------------------------------------------------------------------------
            | Insert Appointment Service
            |--------------------------------------------------------------------------
            */

            DB::table(
                'appointment_service'
            )->insert([

                'Appointment_ID' =>
                    $appointmentId,

                'Service_ID' =>
                    $validated['Service_ID'],

            ]);


            /*
            |--------------------------------------------------------------------------
            | Commit Transaction
            |--------------------------------------------------------------------------
            */

            DB::commit();


            /*
            |--------------------------------------------------------------------------
            | Redirect To Payment
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'payments.create',
                    [
                        'appointmentId' =>
                            $appointmentId
                    ]
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to book appointment: ' .
                    $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Show Individual Appointment
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        $id
    ) {

        /*
        |--------------------------------------------------------------------------
        | Login Check
        |--------------------------------------------------------------------------
        */

        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }


        /*
        |--------------------------------------------------------------------------
        | User Information
        |--------------------------------------------------------------------------
        */

        $userId =
            $request->session()->get('user_id');

        $role = strtoupper(
            $request->session()->get('role', '')
        );


        /*
        |--------------------------------------------------------------------------
        | Find Appointment
        |--------------------------------------------------------------------------
        */

        $appointment = DB::table('appointment')
            ->where(
                'Appointment_ID',
                $id
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Appointment Not Found
        |--------------------------------------------------------------------------
        */

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
        | Add Pet, Groomer, Services and Payment
        |--------------------------------------------------------------------------
        */

        $this->attachAppointmentData(
            $appointment
        );


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER ACCESS
        |--------------------------------------------------------------------------
        */

        if ($role === 'CUSTOMER') {

            if (
                !$appointment->pet ||
                (int) $appointment->pet->Customer_ID !==
                (int) $userId
            ) {

                return redirect()
                    ->route('appointments.index')
                    ->with(
                        'error',
                        'You are not allowed to view this appointment.'
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | GROOMER ACCESS
        |--------------------------------------------------------------------------
        */

        elseif ($role === 'GROOMER') {

            if (
                (int) $appointment->Groomer_ID !==
                (int) $userId
            ) {

                return redirect()
                    ->route('appointments.index')
                    ->with(
                        'error',
                        'You are not assigned to this appointment.'
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ADMIN ACCESS
        |--------------------------------------------------------------------------
        */

        elseif ($role === 'ADMIN') {

            // Admin can view all appointments.
        }


        /*
        |--------------------------------------------------------------------------
        | UNKNOWN ROLE
        |--------------------------------------------------------------------------
        */

        else {

            return redirect()->route('home');
        }


        /*
        |--------------------------------------------------------------------------
        | Get Grooming Report
        |--------------------------------------------------------------------------
        |
        | The show.blade.php already expects:
        |
        | $report
        |
        | So we retrieve it here using Query Builder.
        |
        */

        $report = DB::table('grooming_report')
            ->where(
                'Appointment_ID',
                $appointment->Appointment_ID
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Return Appointment Details
        |--------------------------------------------------------------------------
        */

        return view(
            'appointments.show',
            compact(
                'appointment',
                'report'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Cancel Appointment
    |--------------------------------------------------------------------------
    |
    | CUSTOMER ONLY
    |
    */

    public function cancel(
        Request $request,
        $id
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


        /*
        |--------------------------------------------------------------------------
        | Customer ID
        |--------------------------------------------------------------------------
        */

        $customerId =
            $request->session()->get('user_id');


        /*
        |--------------------------------------------------------------------------
        | Find Appointment
        |--------------------------------------------------------------------------
        |
        | We use a JOIN instead of Eloquent whereHas().
        |
        */

        $appointment = DB::table('appointment')
            ->join(
                'pet',
                'appointment.Pet_ID',
                '=',
                'pet.Pet_ID'
            )
            ->where(
                'appointment.Appointment_ID',
                $id
            )
            ->where(
                'pet.Customer_ID',
                $customerId
            )
            ->select(
                'appointment.*'
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Appointment Not Found
        |--------------------------------------------------------------------------
        */

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
        | Prevent Cancelling Already Cancelled
        |--------------------------------------------------------------------------
        */

        if (
            strtoupper(
                $appointment->Status
            ) === 'CANCELLED'
        ) {

            return redirect()
                ->route('appointments.index')
                ->with(
                    'error',
                    'This appointment is already cancelled.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Cancelling Completed Appointment
        |--------------------------------------------------------------------------
        */

        if (
            strtoupper(
                $appointment->Status
            ) === 'COMPLETED'
        ) {

            return redirect()
                ->route('appointments.index')
                ->with(
                    'error',
                    'Completed appointments cannot be cancelled.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Cancel Appointment
        |--------------------------------------------------------------------------
        */

        DB::table('appointment')
            ->where(
                'Appointment_ID',
                $id
            )
            ->update([

                'Status' =>
                    'CANCELLED'

            ]);


        /*
        |--------------------------------------------------------------------------
        | Return
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('appointments.index')
            ->with(
                'success',
                'Appointment cancelled successfully.'
            );
    }
}