<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Groomer;
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
    | Appointment List
    |--------------------------------------------------------------------------
    |
    | CUSTOMER:
    | Shows only appointments belonging to the logged-in customer.
    |
    | GROOMER:
    | Shows only appointments assigned to the logged-in groomer.
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
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        if ($role === 'CUSTOMER') {

            $appointments = Appointment::with([
                'pet',
                'groomer',
                'services',
                'payment'
            ])
            ->whereHas(
                'pet',
                function ($query) use ($userId) {

                    $query->where(
                        'Customer_ID',
                        $userId
                    );
                }
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


            return view(
                'appointments.index',
                compact('appointments')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | GROOMER
        |--------------------------------------------------------------------------
        */

        if ($role === 'GROOMER') {

            $appointments = Appointment::with([
                'pet',
                'groomer',
                'services',
                'payment'
            ])
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


            return view(
                'appointments.index',
                compact('appointments')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        if ($role === 'ADMIN') {

            $appointments = Appointment::with([
                'pet',
                'groomer',
                'services',
                'payment'
            ])
            ->orderBy(
                'Appointment_Date',
                'desc'
            )
            ->orderBy(
                'Appointment_Time',
                'desc'
            )
            ->get();


            return view(
                'appointments.index',
                compact('appointments')
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

        $customerId = $request->session()->get('user_id');


        /*
        |--------------------------------------------------------------------------
        | Get Customer Pets
        |--------------------------------------------------------------------------
        */

        $pets = Pet::where(
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

        $services = Service::where(
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

        $groomers = Groomer::orderBy(
            'Name'
        )->get();


        /*
        |--------------------------------------------------------------------------
        | Selected Service
        |--------------------------------------------------------------------------
        |
        | When customer clicks:
        |
        | "Book This Service"
        |
        | URL:
        |
        | /appointments/create?service_id=5
        |
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

        $pet = Pet::where(
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

        $service = Service::where(
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

        $groomer = Groomer::where(
            'ID',
            $validated['Groomer_ID']
        )->first();


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

        $duplicate = Appointment::where(
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

            $appointment = Appointment::create([

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
                'Appointment_Service'
            )->insert([

                'Appointment_ID' =>
                    $appointment->Appointment_ID,

                'Service_ID' =>
                    $validated['Service_ID'],

            ]);


            DB::commit();


            /*
            |--------------------------------------------------------------------------
            | REDIRECT TO PAYMENT PAGE
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'payments.create',
                    [
                        'appointmentId' =>
                            $appointment->Appointment_ID
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

        $appointment = Appointment::with([
            'pet',
            'groomer',
            'services',
            'payment'
        ])
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

        if ($role === 'GROOMER') {

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

        if ($role === 'ADMIN') {

            // Admin can view all appointments.
        }


        /*
        |--------------------------------------------------------------------------
        | Unknown Role
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $role,
                [
                    'CUSTOMER',
                    'GROOMER',
                    'ADMIN'
                ]
            )
        ) {

            return redirect()->route('home');
        }


        /*
        |--------------------------------------------------------------------------
        | Return Appointment Details
        |--------------------------------------------------------------------------
        */

        return view(
            'appointments.show',
            compact('appointment')
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
        | Find Customer Appointment
        |--------------------------------------------------------------------------
        */

        $appointment = Appointment::where(
            'Appointment_ID',
            $id
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
        | Cancel Appointment
        |--------------------------------------------------------------------------
        */

        $appointment->Status =
            'CANCELLED';

        $appointment->save();


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